@extends('layouts.admin')

@section('title', 'Hasil Ujian | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Hasil Ujian</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Hasil Ujian Siswa</h2>
            <p class="text-sm text-on-surface-variant mt-1">Rekap nilai seluruh peserta yang telah menyelesaikan ujian.</p>
        </div>
        <a href="{{ route('admin.laporan.export') }}" class="h-11 px-5 bg-success-green text-white rounded-lg text-sm font-semibold inline-flex items-center gap-2 hover:brightness-95">
            <span class="material-symbols-outlined text-[20px]">download</span>
            Export Excel Rekap
        </a>
    </div>

    <form method="GET" action="{{ route('admin.hasil-ujian.index') }}" class="bg-surface-white p-4 rounded-xl soft-shadow border border-outline-variant/20 flex flex-wrap gap-3">
        <select name="ujian_id" onchange="this.form.submit()" class="h-11 px-4 rounded-lg border border-outline-variant text-sm min-w-[220px]">
            <option value="">Semua Ujian</option>
            @foreach($ujians as $u)
                <option value="{{ $u->id }}" {{ (string) request('ujian_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->nama_ujian }}</option>
            @endforeach
        </select>
        <select name="kelas_id" onchange="this.form.submit()" class="h-11 px-4 rounded-lg border border-outline-variant text-sm min-w-[180px]">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)
                <option value="{{ $k->id }}" {{ (string) request('kelas_id') === (string) $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
    </form>

    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-xs font-semibold text-primary uppercase">
                    <tr>
                        <th class="px-5 py-4">Siswa</th>
                        <th class="px-5 py-4">Kelas</th>
                        <th class="px-5 py-4">Nama Ujian</th>
                        <th class="px-5 py-4">Mapel</th>
                        <th class="px-5 py-4 text-center">Nilai Akhir</th>
                        <th class="px-5 py-4">Status Koreksi</th>
                        <th class="px-5 py-4">Waktu Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-sm">
                    @forelse($hasils as $h)
                        <tr class="hover:bg-surface-container-low/40">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-primary">{{ $h->siswa?->nama_lengkap }}</p>
                                <p class="text-xs text-text-muted">NIS {{ $h->siswa?->nis }}</p>
                            </td>
                            <td class="px-5 py-4">{{ $h->siswa?->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="px-5 py-4 font-medium">{{ $h->ujian?->nama_ujian }}</td>
                            <td class="px-5 py-4 text-text-muted">{{ $h->ujian?->mataPelajaran?->nama_mapel }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-lg font-bold {{ $h->nilai_akhir >= 75 ? 'text-success-green' : 'text-error' }}">{{ $h->nilai_akhir }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $h->status_koreksi === 'menunggu_essay' ? 'bg-tertiary-fixed text-tertiary' : 'bg-success-green/10 text-success-green' }}">
                                    {{ $h->status_koreksi === 'menunggu_essay' ? 'Menunggu Essay' : 'Selesai' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-text-muted">{{ $h->selesai_at?->translatedFormat('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-text-muted">Belum ada hasil ujian tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($hasils->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $hasils->links() }}</div>
        @endif
    </div>
</div>
@endsection
