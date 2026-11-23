<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\UjianJawaban;
use App\Models\UjianPeserta;
use App\Services\ExamScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HasilController extends Controller
{
    public function __construct(private readonly ExamScoringService $scoringService)
    {
    }

    public function hasilIndex(Request $request): View
    {
        $guru = $request->user()->guru;

        $ujians = Ujian::with(['mataPelajaran', 'kelas'])
            ->withCount([
                'pesertas as selesai_count' => fn ($q) => $q->where('status', 'selesai'),
            ])
            ->withAvg([
                'pesertas as avg_nilai' => fn ($q) => $q->where('status', 'selesai'),
            ], 'nilai_akhir')
            ->where('guru_id', $guru->id)
            ->latest()
            ->paginate(10);

        return view('guru.hasil.index', compact('ujians'));
    }

    public function hasilShow(Request $request, Ujian $ujian): View
    {
        abort_unless($ujian->guru_id === $request->user()->guru?->id, 403);
        $ujian->load(['mataPelajaran', 'kelas']);

        $pesertas = UjianPeserta::with(['siswa.kelas'])
            ->where('ujian_id', $ujian->id)
            ->orderByDesc('nilai_akhir')
            ->paginate(20);

        return view('guru.hasil.show', compact('ujian', 'pesertas'));
    }

    public function exportExcel(Request $request, Ujian $ujian): StreamedResponse
    {
        abort_unless($ujian->guru_id === $request->user()->guru?->id, 403);
        $rows = UjianPeserta::with(['siswa.kelas'])
            ->where('ujian_id', $ujian->id)
            ->where('status', 'selesai')
            ->orderByDesc('nilai_akhir')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nilai Ujian');

        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Nilai PG', 'Nilai Essay', 'Nilai Akhir', 'Status Koreksi', 'Selesai'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue([$idx + 1, 1], $h);
        }

        foreach ($rows as $index => $row) {
            $r = $index + 2;
            $sheet->setCellValue([1, $r], $index + 1);
            $sheet->setCellValue([2, $r], $row->siswa?->nis);
            $sheet->setCellValue([3, $r], $row->siswa?->nama_lengkap);
            $sheet->setCellValue([4, $r], $row->siswa?->kelas?->nama_kelas ?? '-');
            $sheet->setCellValue([5, $r], $row->nilai_pg);
            $sheet->setCellValue([6, $r], $row->nilai_essay);
            $sheet->setCellValue([7, $r], $row->nilai_akhir);
            $sheet->setCellValue([8, $r], $row->status_koreksi);
            $sheet->setCellValue([9, $r], $row->selesai_at?->format('Y-m-d H:i'));
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'hasil_' . $ujian->kode_ujian . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function essayIndex(Request $request): View
    {
        $guru = $request->user()->guru;

        $pesertas = UjianPeserta::with(['ujian.mataPelajaran', 'siswa.kelas'])
            ->whereHas('ujian', fn ($q) => $q->where('guru_id', $guru->id))
            ->where('status', 'selesai')
            ->whereHas('jawabans.soal', fn ($q) => $q->whereIn('jenis_soal', ['essay', 'menjodohkan']))
            ->orderByRaw("CASE WHEN status_koreksi = 'menunggu_essay' THEN 0 ELSE 1 END")
            ->latest('selesai_at')
            ->paginate(12);

        return view('guru.essay.index', compact('pesertas'));
    }

    public function essayGrade(Request $request, UjianPeserta $peserta): View
    {
        abort_unless($peserta->ujian?->guru_id === $request->user()->guru?->id, 403);
        $peserta->load(['ujian.mataPelajaran', 'siswa.kelas', 'jawabans.soal']);

        $essayJawabans = $peserta->jawabans
            ->filter(fn (UjianJawaban $j) => in_array($j->soal?->jenis_soal, ['essay', 'menjodohkan'], true))
            ->sortBy(fn (UjianJawaban $j) => $j->soal?->nomor_soal ?? 0);

        return view('guru.essay.grade', compact('peserta', 'essayJawabans'));
    }

    public function essayStore(Request $request, UjianPeserta $peserta): RedirectResponse
    {
        abort_unless($peserta->ujian?->guru_id === $request->user()->guru?->id, 403);

        $validated = $request->validate([
            'skor' => ['required', 'array'],
            'skor.*' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'array'],
            'catatan.*' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($peserta, $validated) {
            foreach ($validated['skor'] as $jawabanId => $nilai) {
                $jawaban = UjianJawaban::where('id', $jawabanId)
                    ->where('ujian_peserta_id', $peserta->id)
                    ->with('soal')
                    ->first();

                if (! $jawaban) {
                    continue;
                }

                $maxBobot = (float) ($jawaban->soal?->bobot ?? 100);
                $skor = min($maxBobot, max(0, (float) $nilai));

                $jawaban->update([
                    'skor_diperoleh' => $skor,
                    'is_dikoreksi' => true,
                    'catatan_guru' => $validated['catatan'][$jawabanId] ?? null,
                ]);
            }

            $this->scoringService->recalculatePeserta($peserta->fresh());
        });

        return redirect()->route('guru.essay.index')->with('success', 'Penilaian essay dan feedback siswa berhasil disimpan.');
    }

    public function profilIndex(Request $request): View
    {
        $user = $request->user();
        $guru = $user->guru;

        return view('guru.profil.index', compact('user', 'guru'));
    }

    public function profilUpdate(Request $request): RedirectResponse
    {
        $user = $request->user();
        $guru = $user->guru;

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        DB::transaction(function () use ($user, $guru, $validated) {
            $userData = [
                'name' => $validated['nama_lengkap'],
                'email' => $validated['email'],
            ];
            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            $user->update($userData);
            $guru?->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_hp' => $validated['no_hp'] ?? null,
            ]);
        });

        return back()->with('success', 'Profil guru berhasil diperbarui.');
    }
}
