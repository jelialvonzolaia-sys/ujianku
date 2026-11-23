@extends('layouts.admin')

@section('title', 'Dashboard Admin | Ujianku')

@section('breadcrumb')
    <span class="text-primary font-semibold">Dashboard</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div>
        <h2 class="text-2xl md:text-3xl font-bold text-primary">Selamat Datang di Ujianku</h2>
        <p class="text-sm text-on-surface-variant mt-1">Ringkasan aktivitas akademik hari ini, {{ now()->translatedFormat('d F Y') }}.</p>
    </div>

    <!-- Statistical Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('admin.siswa.index') }}" class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20 transition-transform hover:scale-[1.02] duration-200 block">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-primary-fixed rounded-lg flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">person</span>
                </div>
                <span class="text-success-green text-xs font-semibold flex items-center bg-success-green/10 px-2.5 py-1 rounded-full">
                    Terdaftar
                </span>
            </div>
            <h3 class="text-on-surface-variant text-sm font-medium">Jumlah Siswa</h3>
            <p class="text-primary text-4xl font-bold mt-1">{{ number_format($totalSiswa) }}</p>
        </a>

        <a href="{{ route('admin.guru.index') }}" class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20 transition-transform hover:scale-[1.02] duration-200 block">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-secondary-fixed rounded-lg flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">school</span>
                </div>
                <span class="text-primary text-xs font-semibold bg-primary-fixed px-2.5 py-1 rounded-full">Aktif</span>
            </div>
            <h3 class="text-on-surface-variant text-sm font-medium">Jumlah Guru</h3>
            <p class="text-primary text-4xl font-bold mt-1">{{ number_format($totalGuru) }}</p>
        </a>

        <a href="{{ route('admin.kelas.index') }}" class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20 transition-transform hover:scale-[1.02] duration-200 block">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-tertiary-fixed rounded-lg flex items-center justify-center text-tertiary">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">group</span>
                </div>
                <span class="text-on-surface-variant text-xs font-semibold bg-surface-container px-2.5 py-1 rounded-full">Aktif</span>
            </div>
            <h3 class="text-on-surface-variant text-sm font-medium">Jumlah Kelas</h3>
            <p class="text-primary text-4xl font-bold mt-1">{{ number_format($totalKelas) }}</p>
        </a>

        <a href="{{ route('admin.ujian.index') }}" class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20 transition-transform hover:scale-[1.02] duration-200 block">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-error-container rounded-lg flex items-center justify-center text-error">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">quiz</span>
                </div>
                <span class="text-error text-xs font-semibold bg-error-container/60 px-2.5 py-1 rounded-full">Live</span>
            </div>
            <h3 class="text-on-surface-variant text-sm font-medium">Ujian Aktif</h3>
            <p class="text-primary text-4xl font-bold mt-1">{{ number_format($ujianAktif) }}</p>
        </a>
    </div>

    <!-- Main Layout Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Performance Chart -->
            <div class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-primary">Ringkasan Hasil Ujian</h3>
                        <p class="text-xs text-on-surface-variant">Rata-rata nilai ujian 6 bulan terakhir</p>
                    </div>
                    <span class="bg-surface-container-low rounded-lg text-xs font-semibold px-3 py-1.5 text-primary">
                        Tahun {{ now()->year }}
                    </span>
                </div>
                <div class="h-56 flex items-end justify-between gap-4 px-2">
                    @foreach($months as $m)
                        <div class="flex-1 flex flex-col items-center gap-2 group">
                            <span class="text-[11px] font-semibold text-primary">{{ $m['avg'] > 0 ? $m['avg'] : '-' }}</span>
                            <div class="w-full rounded-t-lg transition-all duration-300 {{ $m['isCurrent'] ? 'bg-primary hover:bg-primary-container' : 'bg-primary-fixed hover:bg-primary' }}"
                                 style="height: {{ $m['height'] }}%;"></div>
                            <span class="text-xs {{ $m['isCurrent'] ? 'font-bold text-primary' : 'text-on-surface-variant' }}">{{ $m['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
                <div class="p-6 border-b border-surface-container flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-primary">Ujian Terbaru</h3>
                    <a href="{{ route('admin.ujian.index') }}" class="text-primary text-sm font-semibold hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container-low">
                                <th class="px-6 py-3.5 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Nama Ujian</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Mata Pelajaran</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Guru</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container">
                            @forelse($ujianTerbaru as $ujian)
                                <tr class="hover:bg-surface-container-low/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-primary">{{ $ujian->nama_ujian }}</td>
                                    <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $ujian->mataPelajaran?->nama_mapel ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $ujian->guru?->nama_lengkap ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        @if($ujian->status === 'aktif')
                                            <span class="px-3 py-1 rounded-full bg-success-green/10 text-success-green text-xs font-bold">Aktif</span>
                                        @elseif($ujian->status === 'selesai')
                                            <span class="px-3 py-1 rounded-full bg-primary-fixed text-primary text-xs font-bold">Selesai</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-surface-container text-on-surface-variant text-xs font-bold">Draft</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-text-muted">Belum ada data ujian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activity Section -->
        <div class="lg:col-span-1">
            <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 h-full flex flex-col">
                <div class="p-6 border-b border-surface-container">
                    <h3 class="text-lg font-semibold text-primary">Aktivitas Terbaru</h3>
                </div>
                <div class="p-6 space-y-5 flex-1">
                    @forelse($aktivitasTerbaru as $log)
                        <div class="flex gap-3.5">
                            <div class="w-9 h-9 rounded-full bg-primary-fixed flex items-center justify-center text-primary shrink-0">
                                <span class="material-symbols-outlined text-[18px]">
                                    {{ $log->tipe === 'ujian' ? 'quiz' : ($log->tipe === 'auth' ? 'login' : 'history') }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-on-surface">
                                    <span class="font-bold">{{ $log->aktor_nama }}</span> — {{ $log->aksi }}
                                </p>
                                @if($log->deskripsi)
                                    <p class="text-xs text-on-surface-variant truncate">{{ $log->deskripsi }}</p>
                                @endif
                                <p class="text-[11px] text-outline mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-text-muted text-center py-8">Belum ada aktivitas tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
