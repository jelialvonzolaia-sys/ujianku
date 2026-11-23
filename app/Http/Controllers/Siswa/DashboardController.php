<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\UjianPeserta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $siswa = $request->user()->siswa?->load('kelas');

        $allClassUjians = $siswa && $siswa->kelas_id
            ? Ujian::with(['guru', 'mataPelajaran', 'bankSoal'])
                ->whereHas('kelas', fn ($q) => $q->where('kelas.id', $siswa->kelas_id))
                ->whereIn('status', ['aktif', 'draft'])
                ->latest()
                ->get()
            : collect();

        $pesertaMap = $siswa
            ? UjianPeserta::where('siswa_id', $siswa->id)->get()->keyBy('ujian_id')
            : collect();

        $ujianTersedia = $allClassUjians->filter(function (Ujian $u) use ($pesertaMap) {
            $p = $pesertaMap->get($u->id);

            return $u->status === 'aktif' && (! $p || $p->status !== 'selesai');
        });

        $ujianMendatang = $allClassUjians->filter(function (Ujian $u) {
            return $u->status === 'draft' || ($u->waktu_mulai && now()->lt($u->waktu_mulai));
        });

        $riwayat = $siswa
            ? UjianPeserta::with(['ujian.mataPelajaran', 'ujian.tahunAjaran'])
                ->where('siswa_id', $siswa->id)
                ->where('status', 'selesai')
                ->latest('selesai_at')
                ->take(6)
                ->get()
            : collect();

        $ujianSelesaiCount = $siswa
            ? UjianPeserta::where('siswa_id', $siswa->id)->where('status', 'selesai')->count()
            : 0;

        return view('siswa.dashboard.index', [
            'siswa' => $siswa,
            'ujianTersediaCount' => $ujianTersedia->count(),
            'ujianSelesaiCount' => $ujianSelesaiCount,
            'ujianMendatangCount' => $ujianMendatang->count(),
            'ujianCards' => $allClassUjians->take(4),
            'pesertaMap' => $pesertaMap,
            'riwayat' => $riwayat,
        ]);
    }

    public function ujianTersedia(Request $request): View
    {
        $siswa = $request->user()->siswa;

        $ujians = $siswa && $siswa->kelas_id
            ? Ujian::with(['guru', 'mataPelajaran', 'bankSoal'])
                ->withCount(['bankSoal'])
                ->whereHas('kelas', fn ($q) => $q->where('kelas.id', $siswa->kelas_id))
                ->where('status', '!=', 'selesai')
                ->latest()
                ->get()
            : collect();

        $pesertaMap = $siswa
            ? UjianPeserta::where('siswa_id', $siswa->id)->get()->keyBy('ujian_id')
            : collect();

        return view('siswa.ujian.index', compact('siswa', 'ujians', 'pesertaMap'));
    }

    public function riwayat(Request $request): View
    {
        $siswa = $request->user()->siswa;

        $riwayats = $siswa
            ? UjianPeserta::with(['ujian.mataPelajaran', 'ujian.guru'])
                ->where('siswa_id', $siswa->id)
                ->where('status', 'selesai')
                ->latest('selesai_at')
                ->paginate(10)
            : UjianPeserta::whereRaw('1 = 0')->paginate(10);

        return view('siswa.riwayat.index', compact('siswa', 'riwayats'));
    }

    public function nilai(Request $request): View
    {
        $siswa = $request->user()->siswa;

        $nilais = $siswa
            ? UjianPeserta::with(['ujian.mataPelajaran', 'jawabans.soal'])
                ->where('siswa_id', $siswa->id)
                ->where('status', 'selesai')
                ->latest('selesai_at')
                ->get()
            : collect();

        $visibleNilais = $nilais->filter(fn (UjianPeserta $p) => $p->ujian?->tampilkan_nilai);
        $rataRata = $visibleNilais->isNotEmpty() ? round($visibleNilais->avg('nilai_akhir'), 1) : 0;
        $tertinggi = $visibleNilais->isNotEmpty() ? $visibleNilais->max('nilai_akhir') : 0;

        return view('siswa.nilai.index', compact('siswa', 'nilais', 'rataRata', 'tertinggi'));
    }

    public function profil(Request $request): View
    {
        $user = $request->user();
        $siswa = $user->siswa?->load(['kelas', 'jurusan']);

        return view('siswa.profil.index', compact('user', 'siswa'));
    }

    public function profilUpdate(Request $request): RedirectResponse
    {
        $user = $request->user();
        $siswa = $user->siswa;

        $validated = $request->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        DB::transaction(function () use ($user, $siswa, $validated) {
            $userData = ['email' => $validated['email']];
            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            $user->update($userData);
            $siswa?->update(['no_hp' => $validated['no_hp'] ?? null]);
        });

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
