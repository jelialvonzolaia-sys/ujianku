@extends('layouts.siswa')

@section('title', 'Dashboard Siswa | Ujianku')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">
    <!-- Statistic Cards -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/40 flex items-center gap-4 group hover:border-primary transition-colors">
            <div class="w-14 h-14 rounded-lg bg-blue-50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-3xl">assignment</span>
            </div>
            <div>
                <p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider">Ujian Tersedia</p>
                <h3 class="text-primary text-3xl font-bold leading-none mt-1">{{ $ujianTersediaCount }}</h3>
            </div>
        </div>
        <div class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/40 flex items-center gap-4 group hover:border-success-green transition-colors">
            <div class="w-14 h-14 rounded-lg bg-green-50 flex items-center justify-center text-success-green group-hover:bg-success-green group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <div>
                <p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider">Ujian Selesai</p>
                <h3 class="text-success-green text-3xl font-bold leading-none mt-1">{{ $ujianSelesaiCount }}</h3>
            </div>
        </div>
        <div class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/40 flex items-center gap-4 group hover:border-tertiary transition-colors">
            <div class="w-14 h-14 rounded-lg bg-yellow-50 flex items-center justify-center text-tertiary group-hover:bg-tertiary group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-3xl">upcoming</span>
            </div>
            <div>
                <p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider">Ujian Mendatang</p>
                <h3 class="text-tertiary text-3xl font-bold leading-none mt-1">{{ $ujianMendatangCount }}</h3>
            </div>
        </div>
    </section>

    <!-- Daftar Ujian Tersedia -->
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-primary">Daftar Ujian Tersedia</h2>
            <a class="text-secondary text-sm font-semibold flex items-center gap-1 hover:underline" href="{{ route('siswa.ujian.index') }}">
                Lihat Semua <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($ujianCards as $ujian)
                @php
                    $peserta = $pesertaMap->get($ujian->id);
                    $isOpen = $ujian->isCurrentlyOpen();
                    $isDone = $peserta && $peserta->status === 'selesai';
                    $isInProgress = $peserta && $peserta->status === 'sedang_mengerjakan';
                @endphp
                <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/50 overflow-hidden flex flex-col">
                    <div class="p-6 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            @if($isDone)
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Sudah Selesai</span>
                            @elseif($isInProgress)
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span> Sedang Berjalan
                                </span>
                            @elseif($isOpen)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-green-600 animate-pulse"></span> Berlangsung
                                </span>
                            @else
                                <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant rounded-full text-xs font-semibold">Belum Dimulai</span>
                            @endif
                            <span class="text-on-surface-variant text-xs font-mono">{{ $ujian->kode_ujian }}</span>
                        </div>

                        <h3 class="text-lg font-bold text-on-surface mb-1">{{ $ujian->nama_ujian }}</h3>
                        <p class="text-xs font-semibold text-secondary mb-4">{{ $ujian->mataPelajaran?->nama_mapel }}</p>

                        <div class="space-y-2 text-sm text-on-surface-variant">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-primary text-[18px]">person</span>
                                <span>{{ $ujian->guru?->nama_lengkap }}</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-primary text-[18px]">calendar_today</span>
                                <span>{{ $ujian->waktu_mulai ? $ujian->waktu_mulai->translatedFormat('d M Y, H:i') : 'Tersedia Sekarang' }}</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-primary text-[18px]">timer</span>
                                <span>{{ $ujian->durasi_menit }} Menit</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-surface-container-lowest border-t border-outline-variant/40">
                        @if($isDone)
                            <a href="{{ route('siswa.ujian.result', $ujian) }}" class="w-full bg-secondary-fixed text-secondary py-2.5 rounded-lg text-sm font-semibold hover:bg-secondary hover:text-white transition-all flex items-center justify-center gap-2">
                                Lihat Hasil <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                        @elseif($isInProgress)
                            <a href="{{ route('siswa.ujian.run', $ujian) }}" class="w-full bg-tertiary text-white py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition-all flex items-center justify-center gap-2">
                                Lanjutkan Ujian <span class="material-symbols-outlined text-[18px]">play_arrow</span>
                            </a>
                        @elseif($isOpen)
                            <a href="{{ route('siswa.ujian.confirm', $ujian) }}" class="w-full bg-primary text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-container transition-all flex items-center justify-center gap-2">
                                Kerjakan Ujian <span class="material-symbols-outlined text-[18px]">play_arrow</span>
                            </a>
                        @else
                            <button disabled class="w-full bg-outline-variant/60 text-on-surface-variant py-2.5 rounded-lg text-sm font-semibold cursor-not-allowed flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">lock</span>
                                Belum Dibuka
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="lg:col-span-2 bg-surface-white p-8 rounded-xl border border-outline-variant/40 text-center text-sm text-text-muted">
                    Belum ada jadwal ujian untuk kelas Anda saat ini.
                </div>
            @endforelse
        </div>
    </section>

    <!-- Exam History Table -->
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-primary">Riwayat Ujian Terakhir</h2>
            <a href="{{ route('siswa.riwayat.index') }}" class="text-sm font-semibold text-secondary hover:underline">Lihat Riwayat</a>
        </div>
        <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/40 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-outline-variant/40 text-xs font-semibold uppercase text-on-surface-variant">
                            <th class="px-6 py-4">Nama Ujian</th>
                            <th class="px-6 py-4">Mata Pelajaran</th>
                            <th class="px-6 py-4 text-center">Tanggal</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20 text-sm">
                        @forelse($riwayat as $r)
                            <tr class="hover:bg-surface-container-low/40 transition-colors">
                                <td class="px-6 py-4 font-semibold text-on-surface">{{ $r->ujian?->nama_ujian }}</td>
                                <td class="px-6 py-4 text-on-surface-variant">{{ $r->ujian?->mataPelajaran?->nama_mapel }}</td>
                                <td class="px-6 py-4 text-center text-xs text-on-surface-variant">{{ $r->selesai_at?->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold">Selesai</span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-lg text-primary">
                                    @if($r->ujian?->tampilkan_nilai)
                                        {{ $r->nilai_akhir }}<span class="text-xs font-normal text-on-surface-variant">/100</span>
                                    @else
                                        <span class="text-xs text-text-muted">Disembunyikan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-text-muted">Belum ada riwayat ujian selesai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
