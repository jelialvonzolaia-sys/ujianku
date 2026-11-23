@extends('layouts.siswa')

@section('title', 'Riwayat Ujian | Ujianku')
@section('page-title', 'Riwayat Ujian')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-primary">Riwayat Pengerjaan Ujian</h2>
        <p class="text-sm text-on-surface-variant mt-1">Catatan lengkap seluruh ujian yang telah Anda selesaikan pada kelas {{ $siswa?->kelas?->nama_kelas ?? '-' }}.</p>
    </div>

    <div class="bg-surface-white rounded-2xl soft-shadow border border-outline-variant/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant text-xs font-semibold uppercase tracking-wider border-b border-outline-variant/40">
                        <th class="px-6 py-4">Nama Ujian</th>
                        <th class="px-6 py-4">Mata Pelajaran & Guru</th>
                        <th class="px-6 py-4">Waktu Selesai</th>
                        <th class="px-6 py-4">Status Koreksi</th>
                        <th class="px-6 py-4 text-right">Nilai</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30 text-sm">
                    @forelse($riwayats as $item)
                        @php $ujian = $item->ujian; @endphp
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-primary">{{ $ujian?->nama_ujian ?? '-' }}</p>
                                <p class="text-xs font-mono text-text-muted">{{ $ujian?->kode_ujian ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-on-surface">{{ $ujian?->mataPelajaran?->nama_mapel ?? '-' }}</p>
                                <p class="text-xs text-text-muted">{{ $ujian?->guru?->nama_lengkap ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">
                                {{ $item->selesai_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->status_koreksi === 'menunggu_essay')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-tertiary-fixed text-tertiary">
                                        Menunggu Essay
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Selesai Dikoreksi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-primary">
                                @if($ujian?->tampilkan_nilai)
                                    {{ number_format((float) $item->nilai_akhir, 1) }}
                                @else
                                    <span class="text-xs font-medium text-text-muted">Disembunyikan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($ujian)
                                    <a href="{{ route('siswa.ujian.result', $ujian) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-secondary-fixed text-secondary text-xs font-semibold hover:bg-secondary/15 transition-colors">
                                        Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-text-muted">
                                Belum ada riwayat ujian yang diselesaikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayats->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/30">
                {{ $riwayats->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
