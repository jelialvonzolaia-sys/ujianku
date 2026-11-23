<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MataPelajaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = MataPelajaran::withCount(['kelasMataPelajarans', 'bankSoals'])->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(fn ($q) => $q->where('nama_mapel', 'like', "%{$search}%")
                ->orWhere('kode_mapel', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'aktif');
        }

        $mapels = $query->paginate(10)->withQueryString();
        $totalMapel = MataPelajaran::count();
        $totalAktif = MataPelajaran::where('is_active', true)->count();

        return view('admin.mapel.index', compact('mapels', 'totalMapel', 'totalAktif'));
    }

    public function create(): View
    {
        return view('admin.mapel.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:20', 'unique:mata_pelajarans,kode_mapel'],
            'nama_mapel' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $mapel = MataPelajaran::create([
            'kode_mapel' => strtoupper(trim($validated['kode_mapel'])),
            'nama_mapel' => trim($validated['nama_mapel']),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'is_active' => $validated['is_active'] === '1',
        ]);

        ActivityLog::record($request->user(), 'Menambah mata pelajaran', $mapel->kode_mapel.' - '.$mapel->nama_mapel, 'admin', $request->ip());

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mapel): View
    {
        return view('admin.mapel.edit', compact('mapel'));
    }

    public function update(Request $request, MataPelajaran $mapel): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:20', Rule::unique('mata_pelajarans', 'kode_mapel')->ignore($mapel->id)],
            'nama_mapel' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $mapel->update([
            'kode_mapel' => strtoupper(trim($validated['kode_mapel'])),
            'nama_mapel' => trim($validated['nama_mapel']),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'is_active' => $validated['is_active'] === '1',
        ]);

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mapel): RedirectResponse
    {
        if ($mapel->bankSoals()->exists() || $mapel->ujians()->exists()) {
            return back()->with('error', 'Mata pelajaran tidak dapat dihapus karena masih terkait dengan bank soal atau ujian.');
        }

        $mapel->delete();

        return redirect()->route('admin.mapel.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
