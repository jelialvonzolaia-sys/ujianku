<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JurusanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Jurusan::withCount('kelas')->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(fn ($q) => $q->where('nama_jurusan', 'like', "%{$search}%")
                ->orWhere('kode_jurusan', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'aktif');
        }

        $jurusans = $query->paginate(10)->withQueryString();
        $totalJurusan = Jurusan::count();
        $totalAktif = Jurusan::where('is_active', true)->count();
        $totalNonaktif = Jurusan::where('is_active', false)->count();
        $totalKelas = Kelas::count();

        return view('admin.jurusan.index', compact(
            'jurusans',
            'totalJurusan',
            'totalAktif',
            'totalNonaktif',
            'totalKelas'
        ));
    }

    public function create(): View
    {
        return view('admin.jurusan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_jurusan' => ['required', 'string', 'max:20', 'unique:jurusans,kode_jurusan'],
            'nama_jurusan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $jurusan = Jurusan::create([
            'kode_jurusan' => strtoupper(trim($validated['kode_jurusan'])),
            'nama_jurusan' => trim($validated['nama_jurusan']),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'is_active' => $validated['is_active'] === '1',
        ]);

        ActivityLog::record($request->user(), 'Menambah jurusan', $jurusan->kode_jurusan.' - '.$jurusan->nama_jurusan, 'admin', $request->ip());

        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan): View
    {
        return view('admin.jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan): RedirectResponse
    {
        $validated = $request->validate([
            'kode_jurusan' => ['required', 'string', 'max:20', Rule::unique('jurusans', 'kode_jurusan')->ignore($jurusan->id)],
            'nama_jurusan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $jurusan->update([
            'kode_jurusan' => strtoupper(trim($validated['kode_jurusan'])),
            'nama_jurusan' => trim($validated['nama_jurusan']),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'is_active' => $validated['is_active'] === '1',
        ]);

        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan): RedirectResponse
    {
        if ($jurusan->kelas()->exists()) {
            return back()->with('error', 'Jurusan tidak dapat dihapus karena masih memiliki kelas.');
        }

        $jurusan->delete();

        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil dihapus.');
    }
}
