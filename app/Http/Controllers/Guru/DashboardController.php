<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianPeserta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $guru = $request->user()->guru;

        $ujianDibuat = $guru ? Ujian::where('guru_id', $guru->id)->count() : 0;
        $ujianAktif = $guru ? Ujian::where('guru_id', $guru->id)->where('status', 'aktif')->count() : 0;
        $jumlahSoal = $guru
            ? Soal::whereHas('bankSoal', fn ($q) => $q->where('guru_id', $guru->id))->count()
            : 0;
        $menungguNilai = $guru
            ? UjianPeserta::whereHas('ujian', fn ($q) => $q->where('guru_id', $guru->id))
                ->where('status_koreksi', 'menunggu_essay')
                ->count()
            : 0;

        $ujianTerbaru = $guru
            ? Ujian::with(['mataPelajaran', 'kelas'])->where('guru_id', $guru->id)->latest()->take(6)->get()
            : collect();

        $bankSoalTerbaru = $guru
            ? BankSoal::with('mataPelajaran')->withCount('soals')->where('guru_id', $guru->id)->latest()->take(4)->get()
            : collect();

        return view('guru.dashboard.index', compact(
            'guru',
            'ujianDibuat',
            'ujianAktif',
            'jumlahSoal',
            'menungguNilai',
            'ujianTerbaru',
            'bankSoalTerbaru'
        ));
    }
}
