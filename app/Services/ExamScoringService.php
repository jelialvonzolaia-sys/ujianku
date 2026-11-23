<?php

namespace App\Services;

use App\Models\Soal;
use App\Models\UjianJawaban;
use App\Models\UjianPeserta;

class ExamScoringService
{
    /**
     * Evaluate an objective answer and update the UjianJawaban score.
     */
    public function scoreObjectiveAnswer(UjianJawaban $jawaban, Soal $soal): void
    {
        $skor = 0.0;
        $dikoreksi = true;

        if (in_array($soal->jenis_soal, ['pilihan_ganda', 'benar_salah'], true)) {
            $opsi = $soal->opsis->firstWhere('id', $jawaban->soal_opsi_id);
            $skor = $opsi ? (float) $opsi->skor : 0.0;
        } elseif ($soal->jenis_soal === 'pg_kompleks') {
            $selectedIds = collect($jawaban->jawaban_json ?? [])->map(fn ($v) => (int) $v)->all();
            $skor = (float) $soal->opsis
                ->whereIn('id', $selectedIds)
                ->sum('skor');
        } elseif ($soal->jenis_soal === 'isian_singkat') {
            $kunci = mb_strtolower(trim((string) $soal->kunci_jawaban));
            $jawab = mb_strtolower(trim((string) $jawaban->jawaban_teks));
            $skor = ($kunci !== '' && $jawab === $kunci) ? (float) $soal->bobot : 0.0;
        } elseif (in_array($soal->jenis_soal, ['essay', 'menjodohkan'], true)) {
            $dikoreksi = false;
            $skor = (float) $jawaban->skor_diperoleh;
        }

        $jawaban->update([
            'skor_diperoleh' => max(0, $skor),
            'is_dikoreksi' => $dikoreksi,
        ]);
    }

    /**
     * Recalculate total scores (PG, Essay, Final) for a student exam attempt.
     */
    public function recalculatePeserta(UjianPeserta $peserta): void
    {
        $peserta->loadMissing(['ujian.bankSoal.soals.opsis', 'jawabans.soal']);

        $bankSoal = $peserta->ujian->bankSoal;
        $soals = $bankSoal->soals;
        $jawabansBySoal = $peserta->jawabans->keyBy('soal_id');

        $nilaiPg = 0.0;
        $nilaiEssay = 0.0;
        $maxTotal = 0.0;
        $hasPendingEssay = false;
        $skorKosong = (float) $bankSoal->skor_tidak_menjawab;

        foreach ($soals as $soal) {
            $maxTotal += $soal->max_skor;
            $jawaban = $jawabansBySoal->get($soal->id);

            $isObjective = in_array($soal->jenis_soal, ['pilihan_ganda', 'pg_kompleks', 'benar_salah', 'isian_singkat'], true);

            if (! $jawaban) {
                if ($isObjective && $skorKosong !== 0.0) {
                    $nilaiPg += $skorKosong;
                }
                if (! $isObjective) {
                    $hasPendingEssay = true;
                }
                continue;
            }

            if ($isObjective) {
                $nilaiPg += (float) $jawaban->skor_diperoleh;
            } else {
                if (! $jawaban->is_dikoreksi) {
                    $hasPendingEssay = true;
                }
                $nilaiEssay += (float) $jawaban->skor_diperoleh;
            }
        }

        $rawTotal = max(0, $nilaiPg + $nilaiEssay);
        $nilaiAkhir = $bankSoal->skala_nilai === 'skala_100' && $maxTotal > 0
            ? round(($rawTotal / $maxTotal) * 100, 2)
            : round($rawTotal, 2);

        $peserta->update([
            'nilai_pg' => round($nilaiPg, 2),
            'nilai_essay' => round($nilaiEssay, 2),
            'nilai_akhir' => min(100, max(0, $nilaiAkhir)),
            'status_koreksi' => $hasPendingEssay ? 'menunggu_essay' : 'selesai',
        ]);
    }
}
