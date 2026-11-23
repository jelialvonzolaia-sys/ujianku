<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TahunAjaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = TahunAjaran::withCount('kelas')->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where('tahun', 'like', "%{$search}%");
        }
        if ($semester = $request->input('semester')) {
            $query->where('semester', $semester);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'aktif');
        }

        $tahunAjarans = $query->paginate(10)->withQueryString();

        return view('admin.tahun-ajaran.index', compact('tahunAjarans'));
    }

    public function create(): View
    {
        return view('admin.tahun-ajaran.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tahun_ajarans')->where(fn ($q) => $q->where('semester', $request->input('semester'))),
            ],
            'semester' => ['required', Rule::in(['Ganjil', 'Genap'])],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'tahun.unique' => 'Kombinasi tahun pelajaran dan semester tersebut sudah terdaftar.',
        ]);

        $activate = $request->boolean('is_active');

        DB::transaction(function () use ($validated, $activate, $request) {
            if ($activate) {
                TahunAjaran::query()->update(['is_active' => false]);
            }

            $ta = TahunAjaran::create([
                'tahun' => $validated['tahun'],
                'semester' => $validated['semester'],
                'is_active' => $activate,
            ]);

            ActivityLog::record($request->user(), 'Menambah tahun pelajaran', "{$ta->tahun} ({$ta->semester})", 'admin', $request->ip());
        });

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun pelajaran berhasil ditambahkan.');
    }

    public function edit(TahunAjaran $tahunAjaran): View
    {
        return view('admin.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran): RedirectResponse
    {
        $validated = $request->validate([
            'tahun' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tahun_ajarans')
                    ->where(fn ($q) => $q->where('semester', $request->input('semester')))
                    ->ignore($tahunAjaran->id),
            ],
            'semester' => ['required', Rule::in(['Ganjil', 'Genap'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $activate = $request->boolean('is_active');

        DB::transaction(function () use ($tahunAjaran, $validated, $activate) {
            if ($activate) {
                TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['is_active' => false]);
            }
            $tahunAjaran->update([
                'tahun' => $validated['tahun'],
                'semester' => $validated['semester'],
                'is_active' => $activate,
            ]);
        });

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun pelajaran berhasil diperbarui.');
    }

    public function toggleActive(Request $request, TahunAjaran $tahunAjaran): RedirectResponse
    {
        DB::transaction(function () use ($tahunAjaran) {
            $nextState = ! $tahunAjaran->is_active;
            if ($nextState) {
                TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['is_active' => false]);
            }
            $tahunAjaran->update(['is_active' => $nextState]);
        });

        $msg = $tahunAjaran->fresh()->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::record($request->user(), 'Mengubah status tahun pelajaran', "{$tahunAjaran->tahun} {$msg}", 'admin', $request->ip());

        return back()->with('success', "Tahun pelajaran {$tahunAjaran->tahun} ({$tahunAjaran->semester}) berhasil {$msg}.");
    }

    public function destroy(TahunAjaran $tahunAjaran): RedirectResponse
    {
        if ($tahunAjaran->kelas()->exists()) {
            return back()->with('error', 'Tahun pelajaran tidak dapat dihapus karena masih digunakan oleh data kelas.');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun pelajaran berhasil dihapus.');
    }
}
