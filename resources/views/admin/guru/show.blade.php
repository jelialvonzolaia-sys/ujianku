@extends('layouts.admin')

@section('title', 'Detail Guru | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.guru.index') }}" class="hover:text-primary">Data Guru</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Detail Guru</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.guru.index') }}" class="inline-flex items-center gap-1.5 text-primary font-semibold text-sm hover:underline mb-2">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Data Guru
            </a>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Detail Guru</h2>
            <p class="text-sm text-outline">Informasi lengkap akun dan aktivitas guru dalam sistem Ujianku.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.guru.index') }}" class="h-11 px-5 flex items-center border border-outline rounded-lg text-sm font-semibold text-on-surface hover:bg-surface-container">Kembali</a>
            <form method="POST" action="{{ route('admin.guru.toggle', $guru) }}">
                @csrf @method('PATCH')
                <button type="submit" class="h-11 px-5 border rounded-lg text-sm font-semibold transition-all {{ $guru->is_active ? 'border-error/40 bg-error/5 text-error hover:bg-error hover:text-white' : 'border-success-green bg-success-green/10 text-success-green hover:bg-success-green hover:text-white' }}">
                    {{ $guru->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                </button>
            </form>
            <a href="{{ route('admin.guru.edit', $guru) }}" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Edit Guru
            </a>
        </div>
    </div>

    <!-- Profile Hero Card -->
    <div class="bg-surface-white rounded-xl p-6 md:p-8 soft-shadow border border-outline-variant/20 flex flex-col md:flex-row items-center gap-6">
        <div class="relative">
            <div class="w-24 h-24 rounded-full bg-primary-fixed text-primary flex items-center justify-center text-3xl font-bold border-4 border-primary/10">
                {{ $guru->user?->initials ?? 'G' }}
            </div>
            <div class="absolute bottom-1 right-1 w-5 h-5 rounded-full border-4 border-white {{ $guru->is_active ? 'bg-success-green' : 'bg-error' }}"></div>
        </div>
        <div class="text-center md:text-left flex-1">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-1">
                <h3 class="text-2xl font-bold text-on-surface">{{ $guru->nama_lengkap }}</h3>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $guru->is_active ? 'bg-success-green/10 text-success-green' : 'bg-error-container text-error' }}">
                    {{ $guru->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <p class="text-sm text-outline mb-3">NIP: {{ $guru->nip }}</p>
            <div class="flex flex-wrap justify-center md:justify-start gap-5 text-xs text-outline">
                <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-primary text-[18px]">calendar_today</span>Bergabung: {{ $guru->created_at->translatedFormat('d F Y') }}</span>
                <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-primary text-[18px]">mail</span>{{ $guru->user?->email }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left -->
        <div class="lg:col-span-7 space-y-6">
            <section class="bg-surface-white rounded-xl p-6 md:p-8 soft-shadow border border-outline-variant/20">
                <h4 class="text-lg font-bold text-primary mb-5 flex items-center gap-2 border-b border-outline-variant/30 pb-3">
                    <span class="material-symbols-outlined">badge</span>
                    Informasi Kontak & Identitas
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <label class="text-xs text-outline uppercase">Nama Lengkap</label>
                        <p class="font-medium mt-1">{{ $guru->nama_lengkap }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-outline uppercase">Nomor NIP</label>
                        <p class="font-medium mt-1">{{ $guru->nip }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-outline uppercase">Email</label>
                        <p class="font-medium mt-1">{{ $guru->user?->email }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-outline uppercase">Nomor HP</label>
                        <p class="font-medium mt-1">{{ $guru->no_hp ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-outline uppercase">Kelas / Tingkat Diampu</label>
                        <div class="flex gap-2 mt-1.5">
                            @forelse($guru->tingkat_kelas ?? [] as $t)
                                <span class="px-2.5 py-1 bg-primary-fixed text-primary rounded text-xs font-bold">{{ $t }}</span>
                            @empty
                                <span class="text-text-muted">-</span>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-outline uppercase">Mapel & Kelas</label>
                        <div class="mt-1.5 space-y-1">
                            @forelse($guru->kelasMataPelajarans as $kmp)
                                <p class="text-xs font-medium text-on-surface">{{ $kmp->mataPelajaran?->nama_mapel }} ({{ $kmp->kelas?->nama_kelas }})</p>
                            @empty
                                <p class="text-xs text-text-muted">Belum ditugaskan di kelas</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-surface-white rounded-xl p-6 md:p-8 soft-shadow border border-outline-variant/20">
                <h4 class="text-lg font-bold text-primary mb-5 flex items-center gap-2 border-b border-outline-variant/30 pb-3">
                    <span class="material-symbols-outlined">history</span>
                    Aktivitas Terbaru
                </h4>
                <div class="space-y-4">
                    @forelse($recentLogs as $log)
                        <div class="flex gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">history</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-on-surface">{{ $log->aksi }} @if($log->deskripsi) — <span class="font-semibold">{{ $log->deskripsi }}</span> @endif</p>
                                <p class="text-xs text-outline mt-0.5">{{ $log->created_at->translatedFormat('d M Y, H:i') }} @if($log->ip_address) • IP {{ $log->ip_address }} @endif</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-text-muted">Belum ada catatan aktivitas.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <!-- Right: Summary -->
        <div class="lg:col-span-5">
            <section class="bg-surface-white rounded-xl p-6 md:p-8 soft-shadow border border-outline-variant/20 space-y-4">
                <h4 class="text-lg font-bold text-primary mb-4 flex items-center gap-2 border-b border-outline-variant/30 pb-3">
                    <span class="material-symbols-outlined">analytics</span>
                    Ringkasan Aktivitas
                </h4>
                <div class="flex items-center p-4 rounded-xl bg-surface-container-low">
                    <div class="w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center mr-4">
                        <span class="material-symbols-outlined">quiz</span>
                    </div>
                    <div>
                        <p class="text-xs text-text-muted uppercase font-semibold">Total Ujian Dibuat</p>
                        <p class="text-2xl font-bold text-primary">{{ $totalUjian }}</p>
                    </div>
                </div>
                <div class="flex items-center p-4 rounded-xl bg-surface-container-low">
                    <div class="w-12 h-12 rounded-lg bg-secondary text-white flex items-center justify-center mr-4">
                        <span class="material-symbols-outlined">database</span>
                    </div>
                    <div>
                        <p class="text-xs text-text-muted uppercase font-semibold">Bank Soal Dimiliki</p>
                        <p class="text-2xl font-bold text-secondary">{{ $totalBankSoal }}</p>
                    </div>
                </div>
                <div class="flex items-center p-4 rounded-xl bg-surface-container-low">
                    <div class="w-12 h-12 rounded-lg bg-success-green text-white flex items-center justify-center mr-4">
                        <span class="material-symbols-outlined">book</span>
                    </div>
                    <div>
                        <p class="text-xs text-text-muted uppercase font-semibold">Mata Pelajaran Diampu</p>
                        <p class="text-2xl font-bold text-success-green">{{ $totalMapel }}</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
