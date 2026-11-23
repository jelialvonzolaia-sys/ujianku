<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Ujian;
use App\Models\UjianPeserta;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalSiswa = Siswa::count();
        $totalGuru = Guru::count();
        $totalKelas = Kelas::where('is_active', true)->count();
        $ujianAktif = Ujian::where('status', 'aktif')->count();

        $ujianTerbaru = Ujian::with(['mataPelajaran', 'guru'])
            ->latest()
            ->take(6)
            ->get();

        $aktivitasTerbaru = ActivityLog::latest()->take(6)->get();

        // Monthly average score summary for the last 6 months
        $months = collect(range(5, 0))->map(function (int $offset) {
            $date = now()->subMonths($offset);
            $avg = UjianPeserta::where('status', 'selesai')
                ->whereYear('selesai_at', $date->year)
                ->whereMonth('selesai_at', $date->month)
                ->avg('nilai_akhir');

            return [
                'label' => $date->translatedFormat('M'),
                'avg' => round((float) ($avg ?? 0), 1),
                'height' => max(12, min(100, (int) round($avg ?? 15))),
                'isCurrent' => $offset === 0,
            ];
        });

        return view('admin.dashboard.index', compact(
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'ujianAktif',
            'ujianTerbaru',
            'aktivitasTerbaru',
            'months'
        ));
    }
}
