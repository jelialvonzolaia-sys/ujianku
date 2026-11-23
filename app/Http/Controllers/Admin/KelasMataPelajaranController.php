<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KelasMataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasMataPelajaranController extends Controller
{
    public function store(Request $request, Kelas $kelas): RedirectResponse
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'guru_id' => ['required', 'exists:gurus,id'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        KelasMataPelajaran::updateOrCreate(
            [
                'kelas_id' => $kelas->id,
                'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            ],
            [
                'guru_id' => $validated['guru_id'],
                'is_active' => $validated['is_active'] === '1',
            ]
        );

        return redirect()->route('admin.kelas.show', ['kelas' => $kelas, 'tab' => 'mapel'])
            ->with('success', 'Mata pelajaran kelas berhasil disimpan.');
    }

    public function destroy(Kelas $kelas, KelasMataPelajaran $kelasMataPelajaran): RedirectResponse
    {
        if ($kelasMataPelajaran->kelas_id === $kelas->id) {
            $kelasMataPelajaran->delete();
        }

        return redirect()->route('admin.kelas.show', ['kelas' => $kelas, 'tab' => 'mapel'])
            ->with('success', 'Mata pelajaran berhasil dihapus dari kelas.');
    }
}
