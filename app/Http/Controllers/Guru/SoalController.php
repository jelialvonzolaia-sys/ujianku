<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Soal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SoalController extends Controller
{
    public function index(Request $request, BankSoal $bankSoal): View
    {
        $this->authorizeOwner($request, $bankSoal);
        $bankSoal->load(['mataPelajaran', 'kelas', 'soals.opsis']);

        $soals = $bankSoal->soals;
        $totalSoal = $soals->count();
        $totalPg = $soals->where('jenis_soal', 'pilihan_ganda')->count();
        $maksimumSkor = $soals->sum(fn (Soal $s) => $s->max_skor);

        $activeNomor = (int) $request->input('nomor', $soals->first()?->nomor_soal ?? 1);
        $activeSoal = $soals->firstWhere('nomor_soal', $activeNomor) ?? $soals->first();

        return view('guru.bank-soal.soal.index', compact(
            'bankSoal',
            'soals',
            'totalSoal',
            'totalPg',
            'maksimumSkor',
            'activeSoal'
        ));
    }

    public function create(Request $request, BankSoal $bankSoal): View
    {
        $this->authorizeOwner($request, $bankSoal);
        $nextNomor = ($bankSoal->soals()->max('nomor_soal') ?? 0) + 1;
        $soal = new Soal([
            'nomor_soal' => $nextNomor,
            'jenis_soal' => 'pilihan_ganda',
            'bobot' => 1,
        ]);

        return view('guru.bank-soal.soal.form', compact('bankSoal', 'soal', 'nextNomor'));
    }

    public function store(Request $request, BankSoal $bankSoal): RedirectResponse
    {
        $this->authorizeOwner($request, $bankSoal);

        $validated = $this->validateSoal($request);

        $soal = DB::transaction(function () use ($bankSoal, $validated) {
            $soal = $bankSoal->soals()->create([
                'nomor_soal' => $validated['nomor_soal'],
                'jenis_soal' => $validated['jenis_soal'],
                'pertanyaan' => $validated['pertanyaan'],
                'bobot' => $validated['bobot'],
                'kunci_jawaban' => $validated['kunci_jawaban'] ?? null,
                'pembahasan' => $validated['pembahasan'] ?? null,
            ]);

            $this->syncOpsis($soal, $validated);

            return $soal;
        });

        return redirect()
            ->route('guru.bank-soal.soal.index', ['bankSoal' => $bankSoal, 'nomor' => $soal->nomor_soal])
            ->with('success', "Soal nomor {$soal->nomor_soal} berhasil ditambahkan.");
    }

    public function edit(Request $request, BankSoal $bankSoal, Soal $soal): View
    {
        $this->authorizeSoal($request, $bankSoal, $soal);
        $soal->load('opsis');
        $nextNomor = $soal->nomor_soal;

        return view('guru.bank-soal.soal.form', compact('bankSoal', 'soal', 'nextNomor'));
    }

    public function update(Request $request, BankSoal $bankSoal, Soal $soal): RedirectResponse
    {
        $this->authorizeSoal($request, $bankSoal, $soal);
        $validated = $this->validateSoal($request);

        DB::transaction(function () use ($soal, $validated) {
            $soal->update([
                'nomor_soal' => $validated['nomor_soal'],
                'jenis_soal' => $validated['jenis_soal'],
                'pertanyaan' => $validated['pertanyaan'],
                'bobot' => $validated['bobot'],
                'kunci_jawaban' => $validated['kunci_jawaban'] ?? null,
                'pembahasan' => $validated['pembahasan'] ?? null,
            ]);

            $soal->opsis()->delete();
            $this->syncOpsis($soal, $validated);
        });

        return redirect()
            ->route('guru.bank-soal.soal.index', ['bankSoal' => $bankSoal, 'nomor' => $soal->nomor_soal])
            ->with('success', "Soal nomor {$soal->nomor_soal} berhasil diperbarui.");
    }

    public function duplicate(Request $request, BankSoal $bankSoal, Soal $soal): RedirectResponse
    {
        $this->authorizeSoal($request, $bankSoal, $soal);
        $soal->load('opsis');

        $nextNomor = ($bankSoal->soals()->max('nomor_soal') ?? 0) + 1;

        DB::transaction(function () use ($soal, $nextNomor) {
            $copy = $soal->replicate();
            $copy->nomor_soal = $nextNomor;
            $copy->save();

            foreach ($soal->opsis as $opsi) {
                $opsiCopy = $opsi->replicate();
                $opsiCopy->soal_id = $copy->id;
                $opsiCopy->save();
            }
        });

        return redirect()
            ->route('guru.bank-soal.soal.index', ['bankSoal' => $bankSoal, 'nomor' => $nextNomor])
            ->with('success', "Soal berhasil diduplikasi sebagai nomor {$nextNomor}.");
    }

    public function destroy(Request $request, BankSoal $bankSoal, Soal $soal): RedirectResponse
    {
        $this->authorizeSoal($request, $bankSoal, $soal);
        $soal->delete();

        // Re-sequence numbers cleanly
        $bankSoal->soals()->orderBy('nomor_soal')->get()->each(function (Soal $item, int $idx) {
            $item->update(['nomor_soal' => $idx + 1]);
        });

        return redirect()->route('guru.bank-soal.soal.index', $bankSoal)->with('success', 'Soal berhasil dihapus.');
    }

    public function importForm(Request $request, BankSoal $bankSoal): View
    {
        $this->authorizeOwner($request, $bankSoal);

        return view('guru.bank-soal.soal.import', compact('bankSoal'));
    }

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Soal');

        $headers = [
            'Jenis (pilihan_ganda/essay/isian_singkat/benar_salah)',
            'Pertanyaan',
            'Opsi A',
            'Opsi B',
            'Opsi C',
            'Opsi D',
            'Opsi E',
            'Kunci (A/B/C/D/E atau teks isian)',
            'Skor/Bobot',
        ];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue([$idx + 1, 1], $h);
        }

        $samples = [
            ['pilihan_ganda', 'Antonim kata "panjang" adalah ....', 'Pendek', 'Tinggi', 'Lebar', 'Besar', 'Luas', 'A', '2'],
            ['essay', 'Jelaskan hukum Pythagoras secara singkat beserta rumusnya!', '', '', '', '', '', '', '10'],
        ];
        foreach ($samples as $rIdx => $row) {
            foreach ($row as $cIdx => $val) {
                $sheet->setCellValue([$cIdx + 1, $rIdx + 2], $val);
            }
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_import_soal_ujianku.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importStore(Request $request, BankSoal $bankSoal): RedirectResponse
    {
        $this->authorizeOwner($request, $bankSoal);
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        $nextNomor = ($bankSoal->soals()->max('nomor_soal') ?? 0) + 1;
        $count = 0;

        DB::transaction(function () use ($rows, $bankSoal, &$nextNomor, &$count) {
            foreach (array_slice($rows, 1) as $row) {
                $jenisRaw = strtolower(trim((string) ($row[0] ?? 'pilihan_ganda')));
                $pertanyaan = trim((string) ($row[1] ?? ''));
                if ($pertanyaan === '') {
                    continue;
                }

                $allowed = ['pilihan_ganda', 'pg_kompleks', 'benar_salah', 'isian_singkat', 'essay'];
                $jenis = in_array($jenisRaw, $allowed, true) ? $jenisRaw : 'pilihan_ganda';
                $kunci = trim((string) ($row[7] ?? 'A'));
                $bobot = max(1, (float) ($row[8] ?? 1));

                $soal = $bankSoal->soals()->create([
                    'nomor_soal' => $nextNomor++,
                    'jenis_soal' => $jenis,
                    'pertanyaan' => $pertanyaan,
                    'bobot' => $bobot,
                    'kunci_jawaban' => $jenis === 'isian_singkat' ? $kunci : null,
                ]);

                if (in_array($jenis, ['pilihan_ganda', 'pg_kompleks', 'benar_salah'], true)) {
                    $labels = ['A', 'B', 'C', 'D', 'E'];
                    $kunciSet = collect(explode(',', strtoupper($kunci)))->map(fn ($k) => trim($k))->filter()->all();
                    foreach ($labels as $idx => $label) {
                        $teks = trim((string) ($row[2 + $idx] ?? ''));
                        if ($teks === '') {
                            continue;
                        }
                        $isBenar = in_array($label, $kunciSet, true);
                        $soal->opsis()->create([
                            'label' => $label,
                            'teks_opsi' => $teks,
                            'skor' => $isBenar ? $bobot : 0,
                            'is_benar' => $isBenar,
                        ]);
                    }
                }

                $count++;
            }
        });

        return redirect()
            ->route('guru.bank-soal.soal.index', $bankSoal)
            ->with('success', "{$count} butir soal berhasil diimpor ke {$bankSoal->nama_bank_soal}.");
    }

    private function validateSoal(Request $request): array
    {
        return $request->validate([
            'nomor_soal' => ['required', 'integer', 'min:1'],
            'jenis_soal' => ['required', Rule::in(['pilihan_ganda', 'pg_kompleks', 'benar_salah', 'menjodohkan', 'isian_singkat', 'essay'])],
            'pertanyaan' => ['required', 'string'],
            'bobot' => ['required', 'numeric', 'min:0'],
            'kunci_jawaban' => ['nullable', 'string'],
            'pembahasan' => ['nullable', 'string'],
            'opsi_teks' => ['nullable', 'array'],
            'opsi_teks.*' => ['nullable', 'string'],
            'opsi_skor' => ['nullable', 'array'],
            'opsi_skor.*' => ['nullable', 'numeric'],
        ]);
    }

    private function syncOpsis(Soal $soal, array $validated): void
    {
        if (! in_array($validated['jenis_soal'], ['pilihan_ganda', 'pg_kompleks', 'benar_salah'], true)) {
            return;
        }

        $labels = ['A', 'B', 'C', 'D', 'E', 'F'];
        $teksList = $validated['opsi_teks'] ?? [];
        $skorList = $validated['opsi_skor'] ?? [];

        $idx = 0;
        foreach ($teksList as $key => $teks) {
            $clean = trim((string) $teks);
            if ($clean === '') {
                continue;
            }
            $skor = (float) ($skorList[$key] ?? 0);
            $soal->opsis()->create([
                'label' => $labels[$idx] ?? (string) ($idx + 1),
                'teks_opsi' => $clean,
                'skor' => $skor,
                'is_benar' => $skor > 0,
            ]);
            $idx++;
        }
    }

    private function authorizeOwner(Request $request, BankSoal $bankSoal): void
    {
        abort_unless($bankSoal->guru_id === $request->user()->guru?->id, 403);
    }

    private function authorizeSoal(Request $request, BankSoal $bankSoal, Soal $soal): void
    {
        $this->authorizeOwner($request, $bankSoal);
        abort_unless($soal->bank_soal_id === $bankSoal->id, 404);
    }
}
