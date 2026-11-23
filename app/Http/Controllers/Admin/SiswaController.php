<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\UjianPeserta;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiswaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Siswa::with(['user', 'kelas', 'jurusan'])->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%"));
            });
        }

        if ($kelasId = $request->input('kelas_id')) {
            $query->where('kelas_id', $kelasId);
        }

        if ($jurusanId = $request->input('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'aktif');
        }

        $siswas = $query->paginate(10)->withQueryString();
        $kelasList = Kelas::with('jurusan')->withCount('siswas')->orderBy('nama_kelas')->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode_jurusan')->get();

        $totalSiswa = Siswa::count();
        $siswaAktif = Siswa::where('is_active', true)->count();
        $siswaNonaktif = Siswa::where('is_active', false)->count();
        $totalKelas = Kelas::where('is_active', true)->count();

        $agamaStats = Siswa::select('agama', DB::raw('count(*) as total'))
            ->whereNotNull('agama')
            ->groupBy('agama')
            ->pluck('total', 'agama');

        return view('admin.siswa.index', compact(
            'siswas',
            'kelasList',
            'jurusans',
            'totalSiswa',
            'siswaAktif',
            'siswaNonaktif',
            'totalKelas',
            'agamaStats'
        ));
    }

    public function perKelas(Request $request, Kelas $kelas): View
    {
        $kelas->load(['jurusan', 'waliKelas', 'tahunAjaran']);
        $query = $kelas->siswas()->with('user')->orderBy('nama_lengkap');

        if ($search = trim((string) $request->input('search'))) {
            $query->where(fn ($q) => $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhere('nis', 'like', "%{$search}%")
                ->orWhere('nisn', 'like', "%{$search}%"));
        }

        $siswas = $query->paginate(15)->withQueryString();
        $totalSiswa = $kelas->siswas()->count();
        $siswaAktif = $kelas->siswas()->where('is_active', true)->count();
        $siswaNonaktif = $kelas->siswas()->where('is_active', false)->count();

        return view('admin.siswa.per-kelas', compact(
            'kelas',
            'siswas',
            'totalSiswa',
            'siswaAktif',
            'siswaNonaktif'
        ));
    }

    public function create(Request $request): View
    {
        $kelasList = Kelas::with('jurusan')->where('is_active', true)->orderBy('nama_kelas')->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode_jurusan')->get();
        $selectedKelasId = $request->input('kelas_id');

        return view('admin.siswa.create', compact('kelasList', 'jurusans', 'selectedKelasId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nis' => ['required', 'string', 'max:30', 'unique:siswas,nis'],
            'nisn' => ['required', 'string', 'max:30', 'unique:siswas,nisn'],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'agama' => ['nullable', 'string', 'max:50'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'jurusan_id' => ['nullable', 'exists:jurusans,id'],
            'is_active' => ['nullable', Rule::in(['0', '1'])],
        ]);

        $kelas = Kelas::findOrFail($validated['kelas_id']);
        $isActive = ($validated['is_active'] ?? '1') === '1';
        $email = $validated['email'] ?: $validated['nis'].'@siswa.ujianku.sch.id';

        DB::transaction(function () use ($validated, $kelas, $isActive, $email, $request) {
            $user = User::create([
                'name' => $validated['nama_lengkap'],
                'email' => $email,
                'password' => Hash::make($validated['password']),
                'role' => 'siswa',
                'is_active' => $isActive,
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $kelas->id,
                'jurusan_id' => $validated['jurusan_id'] ?? $kelas->jurusan_id,
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'agama' => $validated['agama'] ?? 'Islam',
                'no_hp' => $validated['no_hp'] ?? null,
                'is_active' => $isActive,
            ]);

            ActivityLog::record($request->user(), 'Menambah siswa', "{$validated['nama_lengkap']} ({$kelas->nama_kelas})", 'admin', $request->ip());
        });

        if ($request->input('redirect_to') === 'kelas') {
            return redirect()->route('admin.kelas.show', $kelas)->with('success', 'Siswa berhasil ditambahkan ke kelas '.$kelas->nama_kelas.'.');
        }
        if ($request->input('redirect_to') === 'per_kelas') {
            return redirect()->route('admin.siswa.per-kelas', $kelas)->with('success', 'Siswa berhasil ditambahkan ke kelas '.$kelas->nama_kelas.'.');
        }

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa): View
    {
        $siswa->load(['user', 'kelas', 'jurusan', 'ujianPesertas.ujian.mataPelajaran']);

        $totalUjian = $siswa->kelas_id
            ? \App\Models\Ujian::whereHas('kelas', fn ($q) => $q->where('kelas.id', $siswa->kelas_id))->count()
            : 0;
        $ujianSelesai = $siswa->ujianPesertas()->where('status', 'selesai')->count();
        $belumDikerjakan = max(0, $totalUjian - $ujianSelesai);
        $rataNilai = round((float) ($siswa->ujianPesertas()->where('status', 'selesai')->avg('nilai_akhir') ?? 0), 1);
        $recentLogs = ActivityLog::where('user_id', $siswa->user_id)->latest()->take(6)->get();
        $pesertas = UjianPeserta::with('ujian.mataPelajaran')->where('siswa_id', $siswa->id)->latest()->take(8)->get();

        return view('admin.siswa.show', compact(
            'siswa',
            'totalUjian',
            'ujianSelesai',
            'belumDikerjakan',
            'rataNilai',
            'recentLogs',
            'pesertas'
        ));
    }

    public function edit(Siswa $siswa): View
    {
        $siswa->load('user');
        $kelasList = Kelas::with('jurusan')->where('is_active', true)->orderBy('nama_kelas')->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode_jurusan')->get();

        return view('admin.siswa.edit', compact('siswa', 'kelasList', 'jurusans'));
    }

    public function update(Request $request, Siswa $siswa): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($siswa->user_id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'nis' => ['required', 'string', 'max:30', Rule::unique('siswas', 'nis')->ignore($siswa->id)],
            'nisn' => ['required', 'string', 'max:30', Rule::unique('siswas', 'nisn')->ignore($siswa->id)],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'agama' => ['nullable', 'string', 'max:50'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'jurusan_id' => ['nullable', 'exists:jurusans,id'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $kelas = Kelas::findOrFail($validated['kelas_id']);
        $isActive = $validated['is_active'] === '1';

        DB::transaction(function () use ($siswa, $validated, $kelas, $isActive) {
            $userData = [
                'name' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'is_active' => $isActive,
            ];
            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            $siswa->user->update($userData);

            $siswa->update([
                'kelas_id' => $kelas->id,
                'jurusan_id' => $validated['jurusan_id'] ?? $kelas->jurusan_id,
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'agama' => $validated['agama'] ?? $siswa->agama,
                'no_hp' => $validated['no_hp'] ?? null,
                'is_active' => $isActive,
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function toggleActive(Siswa $siswa): RedirectResponse
    {
        $next = ! $siswa->is_active;
        DB::transaction(function () use ($siswa, $next) {
            $siswa->update(['is_active' => $next]);
            $siswa->user->update(['is_active' => $next]);
        });

        return back()->with('success', 'Status akun siswa berhasil diubah.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        DB::transaction(function () use ($siswa) {
            $user = $siswa->user;
            $siswa->delete();
            $user?->delete();
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Siswa');

        $headers = ['NIS', 'NISN', 'Nama Lengkap', 'Email', 'Jenis Kelamin (L/P)', 'Agama', 'No HP', 'Nama Kelas', 'Password'];
        foreach ($headers as $idx => $header) {
            $sheet->setCellValue([$idx + 1, 1], $header);
        }

        $sample = ['2026101', '0089990001', 'Contoh Siswa', 'contoh@siswa.ujianku.sch.id', 'L', 'Islam', '081234567890', Kelas::value('nama_kelas') ?? 'X IPA 1', 'password123'];
        foreach ($sample as $idx => $value) {
            $sheet->setCellValue([$idx + 1, 2], $value);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_import_siswa_ujianku.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray();
        $defaultKelas = $request->filled('kelas_id') ? Kelas::find($request->input('kelas_id')) : Kelas::first();

        $imported = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, $defaultKelas, &$imported, &$skipped) {
            foreach (array_slice($rows, 1) as $row) {
                $nis = trim((string) ($row[0] ?? ''));
                $nisn = trim((string) ($row[1] ?? ''));
                $nama = trim((string) ($row[2] ?? ''));
                if ($nis === '' || $nama === '') {
                    continue;
                }

                if (Siswa::where('nis', $nis)->orWhere('nisn', $nisn)->exists()) {
                    $skipped++;
                    continue;
                }

                $email = trim((string) ($row[3] ?? ''));
                if ($email === '' || User::where('email', $email)->exists()) {
                    $email = Str::slug($nis, '').'@siswa.ujianku.sch.id';
                }

                $jk = strtoupper(trim((string) ($row[4] ?? 'L'))) === 'P' ? 'P' : 'L';
                $agama = trim((string) ($row[5] ?? 'Islam')) ?: 'Islam';
                $noHp = trim((string) ($row[6] ?? '')) ?: null;
                $namaKelas = trim((string) ($row[7] ?? ''));
                $password = trim((string) ($row[8] ?? '')) ?: 'password';

                $kelas = $namaKelas !== '' ? Kelas::where('nama_kelas', $namaKelas)->first() : $defaultKelas;
                $kelas = $kelas ?? $defaultKelas;

                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'siswa',
                    'is_active' => true,
                ]);

                Siswa::create([
                    'user_id' => $user->id,
                    'kelas_id' => $kelas?->id,
                    'jurusan_id' => $kelas?->jurusan_id,
                    'nis' => $nis,
                    'nisn' => $nisn !== '' ? $nisn : $nis,
                    'nama_lengkap' => $nama,
                    'jenis_kelamin' => $jk,
                    'agama' => $agama,
                    'no_hp' => $noHp,
                    'is_active' => true,
                ]);

                $imported++;
            }
        });

        ActivityLog::record($request->user(), 'Import data siswa Excel', "{$imported} berhasil, {$skipped} dilewati", 'admin', $request->ip());

        return back()->with('success', "Import selesai: {$imported} siswa ditambahkan" . ($skipped > 0 ? ", {$skipped} dilewati (duplikat)." : '.'));
    }
}
