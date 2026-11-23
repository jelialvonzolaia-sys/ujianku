@extends('layouts.siswa')

@section('title', 'Hasil Ujian | ' . $ujian->nama_ujian)
@section('page-title', 'Hasil Pengerjaan Ujian')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-surface-white rounded-2xl soft-shadow border border-outline-variant/30 overflow-hidden text-center p-8 md:p-10 space-y-6">
        <div class="w-20 h-20 rounded-full bg-success-green/10 text-success-green flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">task_alt</span>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-primary">Ujian Telah Diselesaikan!</h2>
            <p class="text-sm text-text-muted mt-1">{{ $ujian->nama_ujian }} • {{ $ujian->mataPelajaran?->nama_mapel }}</p>
        </div>

        @if($ujian->tampilkan_nilai)
            <div class="p-6 rounded-2xl bg-primary-fixed/30 border border-primary/20">
                <p class="text-xs font-semibold text-primary uppercase tracking-wider">Nilai Akhir Anda</p>
                <p class="text-5xl font-bold text-primary mt-2">{{ $peserta->nilai_akhir }}</p>
                @if($peserta->status_koreksi === 'menunggu_essay')
                    <span class="inline-block mt-3 px-3 py-1 rounded-full bg-tertiary-fixed text-tertiary text-xs font-semibold">
                        Menunggu Penilaian Essay dari Guru
                    </span>
                @else
                    <span class="inline-block mt-3 px-3 py-1 rounded-full bg-success-green/10 text-success-green text-xs font-semibold">
                        Penilaian Lengkap
                    </span>
                @endif
            </div>
        @else
            <div class="p-6 rounded-2xl bg-surface-container-low">
                <p class="text-sm font-medium text-on-surface-variant">Jawaban Anda berhasil direkam. Nilai akan diumumkan oleh guru pengampu.</p>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-4 text-left text-sm">
            <div class="p-4 rounded-xl bg-surface-container-low">
                <p class="text-xs text-text-muted">Waktu Selesai</p>
                <p class="font-semibold">{{ $peserta->selesai_at?->translatedFormat('d M Y, H:i') }}</p>
            </div>
            <div class="p-4 rounded-xl bg-surface-container-low">
                <p class="text-xs text-text-muted">Soal Dijawab</p>
                <p class="font-semibold">{{ $peserta->jawabans->count() }} / {{ $ujian->bankSoal?->soals->count() ?? 0 }}</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <a href="{{ route('siswa.dashboard') }}" class="flex-1 py-3 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-container">Kembali ke Dashboard</a>
            <a href="{{ route('siswa.nilai.index') }}" class="flex-1 py-3 border border-secondary text-secondary rounded-xl text-sm font-semibold hover:bg-secondary/5">Lihat Semua Nilai</a>
        </div>
    </div>
</div>
@endsection
