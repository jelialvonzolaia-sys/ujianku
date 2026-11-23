<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\SchoolSetting;
use App\Models\TahunAjaran;
use App\Models\Ujian;
use App\Models\UjianPeserta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UjianMonitorController extends Controller
{
    public function ujianIndex(Request $request): View
    {
        $query = Ujian::with(['guru', 'mataPelajaran', 'kelas'])
            ->withCount('pesertas')
            ->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(fn ($q) => $q->where('nama_ujian', 'like', "%{$search}%")
                ->orWhere('kode_ujian', 'like', "%{$search}%"));
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($mapelId = $request->input('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $mapelId);
        }

        $ujians = $query->paginate(10)->withQueryString();
        $mapels = MataPelajaran::where('is_active', true)->orderBy('nama_mapel')->get();

        return view('admin.ujian.index', compact('ujians', 'mapels'));
    }

    public function updateStatus(Request $request, Ujian $ujian): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'aktif', 'selesai'])],
        ]);

        $ujian->update(['status' => $validated['status']]);
        ActivityLog::record($request->user(), 'Mengubah status ujian', "{$ujian->nama_ujian} -> {$validated['status']}", 'admin', $request->ip());

        return back()->with('success', 'Status ujian berhasil diperbarui.');
    }

    public function hasilIndex(Request $request): View
    {
        $query = UjianPeserta::with(['ujian.mataPelajaran', 'siswa.kelas'])
            ->where('status', 'selesai')
            ->latest('selesai_at');

        if ($ujianId = $request->input('ujian_id')) {
            $query->where('ujian_id', $ujianId);
        }
        if ($kelasId = $request->input('kelas_id')) {
            $query->whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelasId));
        }

        $hasils = $query->paginate(15)->withQueryString();
        $ujians = Ujian::orderBy('nama_ujian')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('admin.hasil-ujian.index', compact('hasils', 'ujians', 'kelasList'));
    }

    public function laporanIndex(): View
    {
        $ujianSummary = Ujian::with(['mataPelajaran', 'guru', 'kelas'])
            ->withCount([
                'pesertas as peserta_selesai_count' => fn ($q) => $q->where('status', 'selesai'),
            ])
            ->withAvg([
                'pesertas as nilai_rata_rata' => fn ($q) => $q->where('status', 'selesai'),
            ], 'nilai_akhir')
            ->latest()
            ->paginate(12);

        $totalSelesai = UjianPeserta::where('status', 'selesai')->count();
        $rataKeseluruhan = round((float) (UjianPeserta::where('status', 'selesai')->avg('nilai_akhir') ?? 0), 1);
        $lulusCount = UjianPeserta::where('status', 'selesai')->where('nilai_akhir', '>=', 75)->count();
        $passRate = $totalSelesai > 0 ? round(($lulusCount / $totalSelesai) * 100, 1) : 0;

        return view('admin.laporan.index', compact('ujianSummary', 'totalSelesai', 'rataKeseluruhan', 'passRate'));
    }

    public function exportLaporan(): StreamedResponse
    {
        $rows = UjianPeserta::with(['ujian.mataPelajaran', 'siswa.kelas'])
            ->where('status', 'selesai')
            ->latest('selesai_at')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Hasil Ujian');

        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Nama Ujian', 'Mata Pelajaran', 'Nilai PG', 'Nilai Essay', 'Nilai Akhir', 'Status Koreksi', 'Selesai Pada'];
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue([$idx + 1, 1], $h);
        }

        foreach ($rows as $index => $row) {
            $r = $index + 2;
            $sheet->setCellValue([1, $r], $index + 1);
            $sheet->setCellValue([2, $r], $row->siswa?->nis);
            $sheet->setCellValue([3, $r], $row->siswa?->nama_lengkap);
            $sheet->setCellValue([4, $r], $row->siswa?->kelas?->nama_kelas ?? '-');
            $sheet->setCellValue([5, $r], $row->ujian?->nama_ujian);
            $sheet->setCellValue([6, $r], $row->ujian?->mataPelajaran?->nama_mapel);
            $sheet->setCellValue([7, $r], $row->nilai_pg);
            $sheet->setCellValue([8, $r], $row->nilai_essay);
            $sheet->setCellValue([9, $r], $row->nilai_akhir);
            $sheet->setCellValue([10, $r], $row->status_koreksi);
            $sheet->setCellValue([11, $r], $row->selesai_at?->format('Y-m-d H:i'));
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'laporan_hasil_ujian_ujianku.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function pengaturanIndex(): View
    {
        $settings = [
            'school_name' => SchoolSetting::getValue('school_name', 'SMA Negeri 1 Ujianku'),
            'school_npsn' => SchoolSetting::getValue('school_npsn', '20100123'),
            'school_address' => SchoolSetting::getValue('school_address', 'Jl. Pendidikan No. 1, Jakarta'),
            'kkm_default' => SchoolSetting::getValue('kkm_default', '75'),
            'anti_cheat_blur' => SchoolSetting::getValue('anti_cheat_blur', '1'),
        ];
        $tahunAjarans = TahunAjaran::orderByDesc('tahun')->get();

        return view('admin.pengaturan.index', compact('settings', 'tahunAjarans'));
    }

    public function pengaturanUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_npsn' => ['nullable', 'string', 'max:30'],
            'school_address' => ['nullable', 'string', 'max:500'],
            'kkm_default' => ['required', 'integer', 'min:0', 'max:100'],
            'anti_cheat_blur' => ['nullable', 'boolean'],
        ]);

        SchoolSetting::setValue('school_name', $validated['school_name']);
        SchoolSetting::setValue('school_npsn', $validated['school_npsn'] ?? '');
        SchoolSetting::setValue('school_address', $validated['school_address'] ?? '');
        SchoolSetting::setValue('kkm_default', (string) $validated['kkm_default']);
        SchoolSetting::setValue('anti_cheat_blur', $request->boolean('anti_cheat_blur') ? '1' : '0');

        ActivityLog::record($request->user(), 'Memperbarui pengaturan sekolah', $validated['school_name'], 'admin', $request->ip());

        return back()->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
