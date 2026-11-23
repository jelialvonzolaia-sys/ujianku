<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BankSoal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BankSoalController extends Controller
{
    public function index(Request $request): View
    {
        $guru = $request->user()->guru;

        $query = BankSoal::with(['mataPelajaran', 'kelas'])
            ->withCount('soals')
            ->where('guru_id', $guru->id)
            ->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(fn ($q) => $q->where('nama_bank_soal', 'like', "%{$search}%")
                ->orWhere('kode_soal', 'like', "%{$search}%")
                ->orWhere('materi', 'like', "%{$search}%"));
        }

        if ($mapelId = $request->input('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $mapelId);
        }

        if ($tingkat = $request->input('tingkat_kelas')) {
            $query->where('tingkat_kelas', $tingkat);
        }

        $bankSoals = $query->paginate(10)->withQueryString();
        $mapels = MataPelajaran::where('is_active', true)->orderBy('nama_mapel')->get();

        return view('guru.bank-soal.index', compact('bankSoals', 'mapels'));
    }

    public function create(): View
    {
        $mapels = MataPelajaran::where('is_active', true)->orderBy('nama_mapel')->get();
        $kelasList = Kelas::where('is_active', true)->orderBy('nama_kelas')->get();

        return view('guru.bank-soal.create', compact('mapels', 'kelasList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $guru = $request->user()->guru;

        $validated = $request->validate([
            'nama_bank_soal' => ['required', 'string', 'max:255'],
            'kode_soal' => ['nullable', 'string', 'max:50', 'unique:bank_soals,kode_soal'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'tingkat_kelas' => ['required', Rule::in(['X', 'XI', 'XII'])],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'materi' => ['nullable', 'string', 'max:255'],
            'mode_jawaban' => ['required', Rule::in(['ABC', 'ABCD', 'ABCDE'])],
            'skala_nilai' => ['required', Rule::in(['skala_100', 'skor_total'])],
            'skor_tidak_menjawab' => ['required', 'numeric'],
            'continuous_mode' => ['required', Rule::in(['0', '1'])],
            'harus_menjawab' => ['required', Rule::in(['0', '1'])],
            'redirect_after_save' => ['required', Rule::in(['0', '1'])],
            'petunjuk_pengerjaan' => ['nullable', 'string'],
        ]);

        $mapel = MataPelajaran::find($validated['mata_pelajaran_id']);
        $kode = trim((string) ($validated['kode_soal'] ?? ''));
        if ($kode === '') {
            $kode = strtoupper(Str::limit($mapel?->kode_mapel ?? 'BS', 6, '')) . '-' . strtoupper(Str::random(5));
        }

        $bankSoal = BankSoal::create([
            'guru_id' => $guru->id,
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'kelas_id' => $validated['kelas_id'] ?: null,
            'kode_soal' => $kode,
            'nama_bank_soal' => $validated['nama_bank_soal'],
            'tingkat_kelas' => $validated['tingkat_kelas'],
            'materi' => $validated['materi'] ?? null,
            'mode_jawaban' => $validated['mode_jawaban'],
            'acak_soal' => $request->boolean('acak_soal'),
            'acak_jawaban' => $request->boolean('acak_jawaban'),
            'skala_nilai' => $validated['skala_nilai'],
            'skor_tidak_menjawab' => $validated['skor_tidak_menjawab'],
            'continuous_mode' => $validated['continuous_mode'] === '1',
            'harus_menjawab' => $validated['harus_menjawab'] === '1',
            'redirect_after_save' => $validated['redirect_after_save'] === '1',
            'petunjuk_pengerjaan' => $validated['petunjuk_pengerjaan'] ?? null,
        ]);

        ActivityLog::record($request->user(), 'Membuat bank soal', $bankSoal->nama_bank_soal, 'guru', $request->ip());

        if ($bankSoal->redirect_after_save) {
            return redirect()->route('guru.bank-soal.soal.index', $bankSoal)->with('success', 'Bank soal berhasil dibuat. Silakan tambahkan butir soal.');
        }

        return redirect()->route('guru.bank-soal.index')->with('success', 'Bank soal berhasil dibuat.');
    }

    public function edit(Request $request, BankSoal $bankSoal): View
    {
        $this->authorizeOwner($request, $bankSoal);
        $mapels = MataPelajaran::where('is_active', true)->orderBy('nama_mapel')->get();
        $kelasList = Kelas::where('is_active', true)->orderBy('nama_kelas')->get();

        return view('guru.bank-soal.edit', compact('bankSoal', 'mapels', 'kelasList'));
    }

    public function update(Request $request, BankSoal $bankSoal): RedirectResponse
    {
        $this->authorizeOwner($request, $bankSoal);

        $validated = $request->validate([
            'nama_bank_soal' => ['required', 'string', 'max:255'],
            'kode_soal' => ['required', 'string', 'max:50', Rule::unique('bank_soals', 'kode_soal')->ignore($bankSoal->id)],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'tingkat_kelas' => ['required', Rule::in(['X', 'XI', 'XII'])],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'materi' => ['nullable', 'string', 'max:255'],
            'mode_jawaban' => ['required', Rule::in(['ABC', 'ABCD', 'ABCDE'])],
            'skala_nilai' => ['required', Rule::in(['skala_100', 'skor_total'])],
            'skor_tidak_menjawab' => ['required', 'numeric'],
            'continuous_mode' => ['required', Rule::in(['0', '1'])],
            'harus_menjawab' => ['required', Rule::in(['0', '1'])],
            'redirect_after_save' => ['required', Rule::in(['0', '1'])],
            'petunjuk_pengerjaan' => ['nullable', 'string'],
        ]);

        $bankSoal->update([
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'kelas_id' => $validated['kelas_id'] ?: null,
            'kode_soal' => $validated['kode_soal'],
            'nama_bank_soal' => $validated['nama_bank_soal'],
            'tingkat_kelas' => $validated['tingkat_kelas'],
            'materi' => $validated['materi'] ?? null,
            'mode_jawaban' => $validated['mode_jawaban'],
            'acak_soal' => $request->boolean('acak_soal'),
            'acak_jawaban' => $request->boolean('acak_jawaban'),
            'skala_nilai' => $validated['skala_nilai'],
            'skor_tidak_menjawab' => $validated['skor_tidak_menjawab'],
            'continuous_mode' => $validated['continuous_mode'] === '1',
            'harus_menjawab' => $validated['harus_menjawab'] === '1',
            'redirect_after_save' => $validated['redirect_after_save'] === '1',
            'petunjuk_pengerjaan' => $validated['petunjuk_pengerjaan'] ?? null,
        ]);

        return redirect()->route('guru.bank-soal.index')->with('success', 'Bank soal berhasil diperbarui.');
    }

    public function duplicate(Request $request, BankSoal $bankSoal): RedirectResponse
    {
        $this->authorizeOwner($request, $bankSoal);
        $bankSoal->load('soals.opsis');

        DB::transaction(function () use ($bankSoal) {
            $copy = $bankSoal->replicate();
            $copy->nama_bank_soal = $bankSoal->nama_bank_soal . ' (Salinan)';
            $copy->kode_soal = $bankSoal->kode_soal . '-' . strtoupper(Str::random(4));
            $copy->save();

            foreach ($bankSoal->soals as $soal) {
                $soalCopy = $soal->replicate();
                $soalCopy->bank_soal_id = $copy->id;
                $soalCopy->save();

                foreach ($soal->opsis as $opsi) {
                    $opsiCopy = $opsi->replicate();
                    $opsiCopy->soal_id = $soalCopy->id;
                    $opsiCopy->save();
                }
            }
        });

        return back()->with('success', 'Bank soal dan seluruh butir soalnya berhasil diduplikasi.');
    }

    public function destroy(Request $request, BankSoal $bankSoal): RedirectResponse
    {
        $this->authorizeOwner($request, $bankSoal);
        if ($bankSoal->ujians()->exists()) {
            return back()->with('error', 'Bank soal tidak dapat dihapus karena sedang digunakan oleh ujian.');
        }

        $bankSoal->delete();

        return redirect()->route('guru.bank-soal.index')->with('success', 'Bank soal berhasil dihapus.');
    }

    private function authorizeOwner(Request $request, BankSoal $bankSoal): void
    {
        abort_unless($bankSoal->guru_id === $request->user()->guru?->id, 403);
    }
}
