@extends('layouts.admin')

@section('title', 'Laporan Evaluasi | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Laporan</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Laporan Evaluasi Akademik</h2>
            <p class="text-sm text-on-surface-variant mt-1">Ringkasan performa nilai dan tingkat ketuntasan peserta di setiap ujian.</p>
        </div>
        <a href="{{ route('admin.laporan.export') }}" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold inline-flex items-center gap-2 hover:bg-primary-container">
            <span class="material-symbols-outlined text-[20px]">table_view</span>
            Unduh Laporan (.xlsx)
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20">
            <p class="text-xs font-semibold text-text-muted uppercase">Total Sesi Ujian Selesai</p>
            <h3 class="text-3xl font-bold text-primary mt-1">{{ $totalSelesai }}</h3>
        </div>
        <div class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20">
            <p class="text-xs font-semibold text-text-muted uppercase">Rata-rata Nilai Sekolah</p>
            <h3 class="text-3xl font-bold text-secondary mt-1">{{ $rataKeseluruhan }}</h3>
        </div>
        <div class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20">
            <p class="text-xs font-semibold text-text-muted uppercase">Persentase Tuntas (≥ 75)</p>
            <h3 class="text-3xl font-bold text-success-green mt-1">{{ $passRate }}%</h3>
        </div>
    </div>

    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-xs font-semibold text-primary uppercase">
                    <tr>
                        <th class="px-5 py-4">Nama Ujian</th>
                        <th class="px-5 py-4">Mata Pelajaran</th>
                        <th class="px-5 py-4">Guru</th>
                        <th class="px-5 py-4 text-center">Peserta Selesai</th>
                        <th class="px-5 py-4 text-center">Rata-rata Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-sm">
                    @forelse($ujianSummary as $u)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-primary">{{ $u->nama_ujian }}</td>
                            <td class="px-5 py-4">{{ $u->mataPelajaran?->nama_mapel }}</td>
                            <td class="px-5 py-4 text-text-muted">{{ $u->guru?->nama_lengkap }}</td>
                            <td class="px-5 py-4 text-center font-semibold">{{ $u->peserta_selesai_count }}</td>
                            <td class="px-5 py-4 text-center font-bold text-secondary">{{ $u->nilai_rata_rata !== null ? round($u->nilai_rata_rata, 1) : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-text-muted">Belum ada data laporan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ujianSummary->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $ujianSummary->links() }}</div>
        @endif
    </div>
</div>
@endsection
