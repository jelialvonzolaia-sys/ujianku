@extends('layouts.siswa')

@section('title', 'Konfirmasi Ujian | ' . $ujian->nama_ujian)
@section('page-title', 'Konfirmasi Mulai Ujian')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:underline">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke Dashboard
    </a>

    <div class="bg-surface-white rounded-2xl soft-shadow border border-outline-variant/30 overflow-hidden">
        <div class="bg-primary text-white p-6 md:p-8">
            <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-semibold uppercase">{{ $ujian->mataPelajaran?->nama_mapel }}</span>
            <h2 class="text-2xl font-bold mt-3">{{ $ujian->nama_ujian }}</h2>
            <p class="text-sm text-white/80 mt-1">Guru Pengampu: {{ $ujian->guru?->nama_lengkap }}</p>
        </div>

        <div class="p-6 md:p-8 space-y-6">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="p-4 rounded-xl bg-surface-container-low">
                    <p class="text-xs text-text-muted">Jumlah Soal</p>
                    <p class="text-xl font-bold text-primary">{{ $ujian->bankSoal?->soals->count() ?? 0 }}</p>
                </div>
                <div class="p-4 rounded-xl bg-surface-container-low">
                    <p class="text-xs text-text-muted">Durasi</p>
                    <p class="text-xl font-bold text-primary">{{ $ujian->durasi_menit }} mnt</p>
                </div>
                <div class="p-4 rounded-xl bg-surface-container-low">
                    <p class="text-xs text-text-muted">Peserta</p>
                    <p class="text-sm font-bold text-primary mt-1">{{ $siswa?->kelas?->nama_kelas }}</p>
                </div>
            </div>

            <div class="p-5 rounded-xl bg-primary-fixed/30 border-l-4 border-primary space-y-2">
                <h3 class="text-sm font-bold text-primary flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">info</span>
                    Petunjuk Pengerjaan
                </h3>
                <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed whitespace-pre-line">
                    {{ $ujian->bankSoal?->petunjuk_pengerjaan ?: 'Bacalah soal dengan cermat. Waktu berjalan otomatis begitu Anda menekan tombol Mulai Ujian.' }}
                </p>
                @if($ujian->instruksi_tambahan)
                    <p class="text-xs text-primary font-medium pt-1">{{ $ujian->instruksi_tambahan }}</p>
                @endif
            </div>

            <form method="POST" action="{{ route('siswa.ujian.start', $ujian) }}" class="space-y-4">
                @csrf
                @if($ujian->wajib_token)
                    <div>
                        <label class="block text-sm font-semibold text-primary mb-1.5" for="token">Masukkan Token Ujian <span class="text-error">*</span></label>
                        <input id="token" name="token" value="{{ old('token') }}" required maxlength="12" placeholder="Contoh: MTK202"
                               class="w-full h-12 px-4 rounded-xl border border-outline-variant font-mono text-center text-lg font-bold uppercase tracking-widest focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>
                @endif

                <button type="submit" class="w-full h-12 bg-primary text-white rounded-xl font-semibold text-sm hover:bg-primary-container transition-all flex items-center justify-center gap-2 shadow-lg cursor-pointer">
                    <span class="material-symbols-outlined">play_arrow</span>
                    Mulai Kerjakan Ujian Sekarang
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
