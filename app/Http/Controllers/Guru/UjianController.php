<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BankSoal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Ujian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UjianController extends Controller
{
    public function index(Request $request): View
    {
        $guru = $request->user()->guru;

        $query = Ujian::with(['mataPelajaran', 'bankSoal', 'kelas'])
            ->withCount('pesertas')
            ->where('guru_id', $guru->id)
            ->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(fn ($q) => $q->where('nama_ujian', 'like', "%{$search}%")
                ->orWhere('kode_ujian', 'like', "%{$search}%"));
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $ujians = $query->paginate(10)->withQueryString();

        return view('guru.ujian.index', compact('ujians'));
    }

    public function create(Request $request): View
    {
        $guru = $request->user()->guru;
        $bankSoals = BankSoal::with('mataPelajaran')->withCount('soals')->where('guru_id', $guru->id)->orderBy('nama_bank_soal')->get();
        $mapels = MataPelajaran::where('is_active', true)->orderBy('nama_mapel')->get();
        $kelasList = Kelas::with('jurusan')->where('is_active', true)->orderBy('nama_kelas')->get();

        return view('guru.ujian.create', compact('bankSoals', 'mapels', 'kelasList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $guru = $request->user()->guru;

        $validated = $request->validate([
            'nama_ujian' => ['required', 'string', 'max:255'],
            'bank_soal_id' => ['required', 'exists:bank_soals,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'kelas_ids' => ['required', 'array', 'min:1'],
            'kelas_ids.*' => ['exists:kelas,id'],
            'durasi_menit' => ['required', 'integer', 'min:5', 'max:360'],
            'waktu_mulai' => ['nullable', 'date'],
            'waktu_selesai' => ['nullable', 'date', 'after_or_equal:waktu_mulai'],
            'token' => ['nullable', 'string', 'max:12'],
            'status' => ['required', Rule::in(['draft', 'aktif', 'selesai'])],
            'instruksi_tambahan' => ['nullable', 'string'],
        ]);

        $bankSoal = BankSoal::where('id', $validated['bank_soal_id'])
            ->where('guru_id', $guru->id)
            ->firstOrFail();

        $activeTa = TahunAjaran::where('is_active', true)->first();
        $token = strtoupper(trim((string) ($validated['token'] ?? ''))) ?: strtoupper(Str::random(6));

        $ujian = Ujian::create([
            'guru_id' => $guru->id,
            'bank_soal_id' => $bankSoal->id,
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'tahun_ajaran_id' => $activeTa?->id,
            'kode_ujian' => 'UJ-' . strtoupper(Str::random(6)),
            'nama_ujian' => $validated['nama_ujian'],
            'token' => $token,
            'wajib_token' => $request->boolean('wajib_token'),
            'durasi_menit' => $validated['durasi_menit'],
            'waktu_mulai' => $validated['waktu_mulai'] ?? null,
            'waktu_selesai' => $validated['waktu_selesai'] ?? null,
            'status' => $validated['status'],
            'tampilkan_nilai' => $request->boolean('tampilkan_nilai', true),
            'instruksi_tambahan' => $validated['instruksi_tambahan'] ?? null,
        ]);

        $ujian->kelas()->sync($validated['kelas_ids']);

        ActivityLog::record($request->user(), 'Membuat ujian baru', $ujian->nama_ujian, 'ujian', $request->ip());

        return redirect()->route('guru.ujian.show', $ujian)->with('success', 'Jadwal ujian berhasil dibuat.');
    }

    public function show(Request $request, Ujian $ujian): View
    {
        $this->authorizeOwner($request, $ujian);
        $ujian->load(['mataPelajaran', 'bankSoal.soals', 'kelas', 'pesertas.siswa.kelas']);

        return view('guru.ujian.show', compact('ujian'));
    }

    public function edit(Request $request, Ujian $ujian): View
    {
        $this->authorizeOwner($request, $ujian);
        $guru = $request->user()->guru;
        $bankSoals = BankSoal::with('mataPelajaran')->withCount('soals')->where('guru_id', $guru->id)->orderBy('nama_bank_soal')->get();
        $mapels = MataPelajaran::where('is_active', true)->orderBy('nama_mapel')->get();
        $kelasList = Kelas::with('jurusan')->where('is_active', true)->orderBy('nama_kelas')->get();
        $selectedKelas = $ujian->kelas()->pluck('kelas.id')->all();

        return view('guru.ujian.edit', compact('ujian', 'bankSoals', 'mapels', 'kelasList', 'selectedKelas'));
    }

    public function update(Request $request, Ujian $ujian): RedirectResponse
    {
        $this->authorizeOwner($request, $ujian);

        $validated = $request->validate([
            'nama_ujian' => ['required', 'string', 'max:255'],
            'bank_soal_id' => ['required', 'exists:bank_soals,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'kelas_ids' => ['required', 'array', 'min:1'],
            'kelas_ids.*' => ['exists:kelas,id'],
            'durasi_menit' => ['required', 'integer', 'min:5', 'max:360'],
            'waktu_mulai' => ['nullable', 'date'],
            'waktu_selesai' => ['nullable', 'date', 'after_or_equal:waktu_mulai'],
            'token' => ['nullable', 'string', 'max:12'],
            'status' => ['required', Rule::in(['draft', 'aktif', 'selesai'])],
            'instruksi_tambahan' => ['nullable', 'string'],
        ]);

        $ujian->update([
            'bank_soal_id' => $validated['bank_soal_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'nama_ujian' => $validated['nama_ujian'],
            'token' => strtoupper(trim((string) ($validated['token'] ?? $ujian->token))),
            'wajib_token' => $request->boolean('wajib_token'),
            'durasi_menit' => $validated['durasi_menit'],
            'waktu_mulai' => $validated['waktu_mulai'] ?? null,
            'waktu_selesai' => $validated['waktu_selesai'] ?? null,
            'status' => $validated['status'],
            'tampilkan_nilai' => $request->boolean('tampilkan_nilai'),
            'instruksi_tambahan' => $validated['instruksi_tambahan'] ?? null,
        ]);

        $ujian->kelas()->sync($validated['kelas_ids']);

        return redirect()->route('guru.ujian.show', $ujian)->with('success', 'Data ujian berhasil diperbarui.');
    }

    public function regenerateToken(Request $request, Ujian $ujian): RedirectResponse
    {
        $this->authorizeOwner($request, $ujian);
        $ujian->update(['token' => strtoupper(Str::random(6))]);

        return back()->with('success', 'Token ujian berhasil diperbarui menjadi: ' . $ujian->token);
    }

    public function toggleStatus(Request $request, Ujian $ujian): RedirectResponse
    {
        $this->authorizeOwner($request, $ujian);
        $next = match ($ujian->status) {
            'draft' => 'aktif',
            'aktif' => 'selesai',
            default => 'draft',
        };
        $ujian->update(['status' => $next]);

        return back()->with('success', "Status ujian diubah menjadi {$next}.");
    }

    public function destroy(Request $request, Ujian $ujian): RedirectResponse
    {
        $this->authorizeOwner($request, $ujian);
        $ujian->delete();

        return redirect()->route('guru.ujian.index')->with('success', 'Ujian berhasil dihapus.');
    }

    private function authorizeOwner(Request $request, Ujian $ujian): void
    {
        abort_unless($ujian->guru_id === $request->user()->guru?->id, 403);
    }
}
