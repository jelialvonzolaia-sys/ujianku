@extends('layouts.guru')

@section('title', 'Hasil Ujian | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Hasil Ujian</span>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl md:text-3xl font-bold text-primary">Rekap Hasil Ujian</h2>
        <p class="text-sm text-on-surface-variant mt-1">Lihat statistik nilai per ujian dan ekspor daftar nilai siswa ke format Excel.</p>
    </div>

    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-xs font-semibold text-primary uppercase">
                    <tr>
                        <th class="px-5 py-4">Nama Ujian</th>
                        <th class="px-5 py-4">Mata Pelajaran</th>
                        <th class="px-5 py-4">Kelas</th>
                        <th class="px-5 py-4 text-center">Peserta Selesai</th>
                        <th class="px-5 py-4 text-center">Rata-rata Nilai</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-sm">
                    @forelse($ujians as $u)
                        <tr class="hover:bg-surface-container-low/40">
                            <td class="px-5 py-4 font-bold text-primary">{{ $u->nama_ujian }}</td>
                            <td class="px-5 py-4">{{ $u->mataPelajaran?->nama_mapel }}</td>
                            <td class="px-5 py-4 text-xs">{{ $u->kelas->pluck('nama_kelas')->implode(', ') }}</td>
                            <td class="px-5 py-4 text-center font-semibold">{{ $u->selesai_count }}</td>
                            <td class="px-5 py-4 text-center font-bold text-secondary">{{ $u->avg_nilai !== null ? round($u->avg_nilai, 1) : '-' }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('guru.hasil.show', $u) }}" class="px-3.5 py-1.5 bg-primary text-white rounded-lg text-xs font-semibold">Lihat Detail</a>
                                    <a href="{{ route('guru.hasil.export', $u) }}" class="px-3.5 py-1.5 bg-success-green text-white rounded-lg text-xs font-semibold">Excel</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-text-muted">Belum ada ujian yang dibuat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ujians->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $ujians->links() }}</div>
        @endif
    </div>
</div>
@endsection
