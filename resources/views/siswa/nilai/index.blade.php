@extends('layouts.siswa')

@section('title', 'Nilai Saya | Ujianku')
@section('page-title', 'Rekap Nilai & Umpan Balik')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-primary">Nilai Saya</h2>
        <p class="text-sm text-on-surface-variant mt-1">Ringkasan capaian nilai ujian dan catatan penilaian essay dari guru pengampu.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/40 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">fact_check</span>
            </div>
            <div>
                <p class="text-xs text-text-muted font-medium">Ujian Selesai</p>
                <p class="text-2xl font-bold text-primary">{{ $nilais->count() }}</p>
            </div>
        </div>

        <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/40 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">analytics</span>
            </div>
            <div>
                <p class="text-xs text-text-muted font-medium">Rata-rata Nilai</p>
                <p class="text-2xl font-bold text-secondary">{{ number_format((float) $rataRata, 1) }}</p>
            </div>
        </div>

        <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/40 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 text-green-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">emoji_events</span>
            </div>
            <div>
                <p class="text-xs text-text-muted font-medium">Nilai Tertinggi</p>
                <p class="text-2xl font-bold text-green-700">{{ number_format((float) $tertinggi, 1) }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($nilais as $peserta)
            @php
                $ujian = $peserta->ujian;
                $essayFeedbacks = $peserta->jawabans->filter(function ($j) {
                    return $j->soal?->jenis_soal === 'essay' || ! empty($j->catatan_guru);
                });
            @endphp
            <div class="bg-surface-white rounded-2xl soft-shadow border border-outline-variant/40 p-6 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs px-2 py-0.5 rounded bg-surface-container text-on-surface-variant">{{ $ujian?->kode_ujian }}</span>
                            <span class="text-xs text-text-muted">{{ $ujian?->mataPelajaran?->nama_mapel }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-primary">{{ $ujian?->nama_ujian }}</h3>
                        <p class="text-xs text-text-muted mt-0.5">Diselesaikan pada {{ $peserta->selesai_at?->translatedFormat('d M Y, H:i') ?? '-' }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        @if($ujian?->tampilkan_nilai)
                            <div class="text-right">
                                <p class="text-xs text-text-muted">Nilai Akhir</p>
                                <p class="text-3xl font-bold text-primary">{{ number_format((float) $peserta->nilai_akhir, 1) }}</p>
                                <p class="text-[11px] text-text-muted">
                                    Objektif: {{ number_format((float) $peserta->nilai_pg, 1) }} • Essay: {{ number_format((float) $peserta->nilai_essay, 1) }}
                                </p>
                            </div>
                        @else
                            <span class="px-3 py-1.5 rounded-lg bg-surface-container text-xs font-medium text-on-surface-variant">
                                Nilai belum dipublikasikan guru
                            </span>
                        @endif
                    </div>
                </div>

                @if($ujian?->tampilkan_nilai && $essayFeedbacks->isNotEmpty())
                    <div class="pt-4 border-t border-outline-variant/30 space-y-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Penilaian & Catatan Jawaban Essay</p>
                        @foreach($essayFeedbacks as $jawaban)
                            <div class="p-4 rounded-xl bg-surface-container-low/80 border border-outline-variant/30 space-y-1.5">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-xs font-semibold text-primary">
                                        Soal #{{ $jawaban->soal?->nomor_soal }}: {{ \Illuminate\Support\Str::limit(strip_tags($jawaban->soal?->pertanyaan ?? ''), 120) }}
                                    </p>
                                    <span class="shrink-0 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $jawaban->is_dikoreksi ? 'bg-green-100 text-green-700' : 'bg-tertiary-fixed text-tertiary' }}">
                                        {{ $jawaban->is_dikoreksi ? 'Skor: ' . $jawaban->skor_diperoleh . ' / ' . $jawaban->soal?->bobot : 'Belum Dikoreksi' }}
                                    </span>
                                </div>
                                @if($jawaban->catatan_guru)
                                    <p class="text-xs text-on-surface-variant">
                                        <span class="font-semibold text-secondary">Catatan Guru:</span> {{ $jawaban->catatan_guru }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-surface-white rounded-2xl p-10 text-center text-text-muted border border-outline-variant/30">
                Belum ada nilai ujian yang tercatat.
            </div>
        @endforelse
    </div>
</div>
@endsection
