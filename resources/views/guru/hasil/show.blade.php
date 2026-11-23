@extends('layouts.guru')

@section('title', 'Detail Nilai | ' . $ujian->nama_ujian)

@section('breadcrumb')
    <a href="{{ route('guru.hasil.index') }}" class="hover:text-primary">Hasil Ujian</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">{{ $ujian->nama_ujian }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-primary">{{ $ujian->nama_ujian }}</h2>
            <p class="text-sm text-text-muted">{{ $ujian->mataPelajaran?->nama_mapel }} • Kelas {{ $ujian->kelas->pluck('nama_kelas')->implode(', ') }}</p>
        </div>
        <a href="{{ route('guru.hasil.export', $ujian) }}" class="h-11 px-5 bg-success-green text-white rounded-lg text-sm font-semibold inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">download</span>
            Export Excel Nilai
        </a>
    </div>

    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-xs font-semibold uppercase">
                    <tr>
                        <th class="px-5 py-3.5">No</th>
                        <th class="px-5 py-3.5">NIS</th>
                        <th class="px-5 py-3.5">Nama Siswa</th>
                        <th class="px-5 py-3.5">Kelas</th>
                        <th class="px-5 py-3.5 text-center">Nilai PG</th>
                        <th class="px-5 py-3.5 text-center">Nilai Essay</th>
                        <th class="px-5 py-3.5 text-center">Nilai Akhir</th>
                        <th class="px-5 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-sm">
                    @forelse($pesertas as $idx => $p)
                        <tr>
                            <td class="px-5 py-3.5">{{ $pesertas->firstItem() + $idx }}</td>
                            <td class="px-5 py-3.5 font-mono">{{ $p->siswa?->nis }}</td>
                            <td class="px-5 py-3.5 font-semibold text-primary">{{ $p->siswa?->nama_lengkap }}</td>
                            <td class="px-5 py-3.5">{{ $p->siswa?->kelas?->nama_kelas }}</td>
                            <td class="px-5 py-3.5 text-center">{{ $p->nilai_pg }}</td>
                            <td class="px-5 py-3.5 text-center">{{ $p->nilai_essay }}</td>
                            <td class="px-5 py-3.5 text-center font-bold text-lg text-primary">{{ $p->nilai_akhir }}</td>
                            <td class="px-5 py-3.5">
                                @if($p->status_koreksi === 'menunggu_essay')
                                    <a href="{{ route('guru.essay.grade', $p) }}" class="px-3 py-1 bg-tertiary-fixed text-tertiary rounded-full text-xs font-bold hover:underline">Koreksi Essay</a>
                                @else
                                    <span class="px-2.5 py-0.5 bg-success-green/10 text-success-green rounded-full text-xs font-bold">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-8 text-center text-text-muted">Belum ada peserta ujian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pesertas->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $pesertas->links() }}</div>
        @endif
    </div>
</div>
@endsection
