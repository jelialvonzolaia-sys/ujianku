<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $query = Kelas::with(['jurusan', 'waliKelas', 'tahunAjaran'])
            ->withCount('siswas')
            ->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('tingkat', 'like', "%{$search}%")
                    ->orWhereHas('jurusan', fn ($j) => $j->where('kode_jurusan', 'like', "%{$search}%")->orWhere('nama_jurusan', 'like', "%{$search}%"))
                    ->orWhereHas('waliKelas', fn ($g) => $g->where('nama_lengkap', 'like', "%{$search}%"));
            });
        }

        if ($jurusanId = $request->input('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'aktif');
        }

        $kelasList = $query->paginate(10)->withQueryString();
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode_jurusan')->get();

        $totalKelas = Kelas::count();
        $kelasAktif = Kelas::where('is_active', true)->count();
        $totalSiswa = \App\Models\Siswa::count();
        $totalWaliKelas = Kelas::whereNotNull('wali_kelas_id')->distinct('wali_kelas_id')->count('wali_kelas_id');

        return view('admin.kelas.index', compact(
            'kelasList',
            'jurusans',
            'totalKelas',
            'kelasAktif',
            'totalSiswa',
            'totalWaliKelas'
        ));
    }

    public function create(): View
    {
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode_jurusan')->get();
        $gurus = Guru::where('is_active', true)->orderBy('nama_lengkap')->get();
        $tahunAjarans = TahunAjaran::orderByDesc('is_active')->orderByDesc('tahun')->get();

        return view('admin.kelas.create', compact('jurusans', 'gurus', 'tahunAjarans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kelas')->where(fn ($q) => $q->where('tahun_ajaran_id', $request->input('tahun_ajaran_id'))),
            ],
            'tingkat' => ['required', Rule::in(['X', 'XI', 'XII'])],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'wali_kelas_id' => ['nullable', 'exists:gurus,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ], [
            'nama_kelas.unique' => 'Nama kelas tersebut sudah ada di tahun ajaran yang dipilih.',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => trim($validated['nama_kelas']),
            'tingkat' => $validated['tingkat'],
            'jurusan_id' => $validated['jurusan_id'],
            'wali_kelas_id' => $validated['wali_kelas_id'] ?: null,
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'is_active' => $validated['is_active'] === '1',
        ]);

        ActivityLog::record($request->user(), 'Menambah kelas', $kelas->nama_kelas, 'admin', $request->ip());

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function show(Request $request, Kelas $kelas): View
    {
        $kelas->load(['jurusan', 'waliKelas', 'tahunAjaran']);

        $siswaQuery = $kelas->siswas()->with('user');
        if ($search = trim((string) $request->input('search'))) {
            $siswaQuery->where(fn ($q) => $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhere('nis', 'like', "%{$search}%")
                ->orWhere('nisn', 'like', "%{$search}%"));
        }

        $sort = $request->input('sort', 'nama_lengkap');
        $allowedSorts = ['nama_lengkap', 'nis', 'nisn'];
        $siswaQuery->orderBy(in_array($sort, $allowedSorts, true) ? $sort : 'nama_lengkap');

        $siswas = $siswaQuery->paginate(10)->withQueryString();
        $mapelsKelas = $kelas->kelasMataPelajarans()->with(['mataPelajaran', 'guru'])->get();
        $allMapels = MataPelajaran::where('is_active', true)->orderBy('nama_mapel')->get();
        $allGurus = Guru::where('is_active', true)->orderBy('nama_lengkap')->get();

        $totalSiswa = $kelas->siswas()->count();
        $siswaAktif = $kelas->siswas()->where('is_active', true)->count();

        return view('admin.kelas.show', compact(
            'kelas',
            'siswas',
            'mapelsKelas',
            'allMapels',
            'allGurus',
            'totalSiswa',
            'siswaAktif'
        ));
    }

    public function edit(Kelas $kelas): View
    {
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode_jurusan')->get();
        $gurus = Guru::where('is_active', true)->orderBy('nama_lengkap')->get();
        $tahunAjarans = TahunAjaran::orderByDesc('is_active')->orderByDesc('tahun')->get();

        return view('admin.kelas.edit', compact('kelas', 'jurusans', 'gurus', 'tahunAjarans'));
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kelas')
                    ->where(fn ($q) => $q->where('tahun_ajaran_id', $request->input('tahun_ajaran_id')))
                    ->ignore($kelas->id),
            ],
            'tingkat' => ['required', Rule::in(['X', 'XI', 'XII'])],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'wali_kelas_id' => ['nullable', 'exists:gurus,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $kelas->update([
            'nama_kelas' => trim($validated['nama_kelas']),
            'tingkat' => $validated['tingkat'],
            'jurusan_id' => $validated['jurusan_id'],
            'wali_kelas_id' => $validated['wali_kelas_id'] ?: null,
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'is_active' => $validated['is_active'] === '1',
        ]);

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->siswas()->exists()) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih memiliki siswa terdaftar.');
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil dihapus.');
    }
}
