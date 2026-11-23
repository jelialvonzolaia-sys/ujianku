@extends('layouts.guru')

@section('title', 'Dashboard Guru | Ujianku')

@section('breadcrumb')
    <span class="text-primary font-semibold">Teacher Dashboard</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Selamat Datang, {{ $guru?->nama_lengkap ?? auth()->user()->name }}</h2>
            <p class="text-sm text-text-muted mt-1">Berikut adalah ringkasan bank soal dan jadwal ujian Anda hari ini.</p>
        </div>
        <a href="{{ route('guru.ujian.create') }}" class="flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-lg text-sm font-semibold hover:bg-primary-container transition-all shadow-lg shadow-primary/20">
            <span class="material-symbols-outlined">add</span>
            Buat Ujian Baru
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20 flex items-center gap-4">
            <div class="w-13 h-13 rounded-full bg-blue-50 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-3xl">assignment</span>
            </div>
            <div>
                <p class="text-xs text-text-muted font-semibold uppercase">Ujian Dibuat</p>
                <h4 class="text-3xl font-bold text-on-surface">{{ $ujianDibuat }}</h4>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20 flex items-center gap-4">
            <div class="w-13 h-13 rounded-full bg-green-50 flex items-center justify-center text-success-green">
                <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">play_circle</span>
            </div>
            <div>
                <p class="text-xs text-text-muted font-semibold uppercase">Ujian Aktif</p>
                <h4 class="text-3xl font-bold text-on-surface">{{ $ujianAktif }}</h4>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20 flex items-center gap-4">
            <div class="w-13 h-13 rounded-full bg-amber-50 flex items-center justify-center text-tertiary">
                <span class="material-symbols-outlined text-3xl">quiz</span>
            </div>
            <div>
                <p class="text-xs text-text-muted font-semibold uppercase">Jumlah Soal</p>
                <h4 class="text-3xl font-bold text-on-surface">{{ $jumlahSoal }}</h4>
            </div>
        </div>
        <a href="{{ route('guru.essay.index') }}" class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20 flex items-center gap-4 hover:border-error/40 transition-colors">
            <div class="w-13 h-13 rounded-full bg-red-50 flex items-center justify-center text-error">
                <span class="material-symbols-outlined text-3xl">pending_actions</span>
            </div>
            <div>
                <p class="text-xs text-text-muted font-semibold uppercase">Menunggu Nilai Essay</p>
                <h4 class="text-3xl font-bold text-on-surface">{{ $menungguNilai }}</h4>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Exams -->
        <div class="lg:col-span-2 bg-surface-white rounded-xl border border-outline-variant/30 overflow-hidden soft-shadow">
            <div class="px-6 py-4 border-b border-outline-variant/30 flex justify-between items-center">
                <h3 class="font-bold text-on-surface">Daftar Ujian Terbaru</h3>
                <a class="text-primary text-sm font-semibold hover:underline" href="{{ route('guru.ujian.index') }}">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold">Nama Ujian</th>
                            <th class="px-6 py-3.5 font-semibold">Mata Pelajaran</th>
                            <th class="px-6 py-3.5 font-semibold">Token</th>
                            <th class="px-6 py-3.5 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20 text-sm">
                        @forelse($ujianTerbaru as $u)
                            <tr class="hover:bg-surface-container-low/40 transition-colors">
                                <td class="px-6 py-4 font-semibold text-primary">
                                    <a href="{{ route('guru.ujian.show', $u) }}" class="hover:underline">{{ $u->nama_ujian }}</a>
                                </td>
                                <td class="px-6 py-4 text-text-muted">{{ $u->mataPelajaran?->nama_mapel }}</td>
                                <td class="px-6 py-4 font-mono font-bold text-secondary">{{ $u->wajib_token ? $u->token : '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($u->status === 'aktif')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Aktif</span>
                                    @elseif($u->status === 'selesai')
                                        <span class="px-3 py-1 bg-surface-dim text-on-surface-variant text-xs font-bold rounded-full">Selesai</span>
                                    @else
                                        <span class="px-3 py-1 bg-primary-fixed text-primary text-xs font-bold rounded-full">Draft</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-text-muted">Belum ada ujian yang dibuat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bank Soal Quick Card -->
        <div class="bg-surface-white rounded-xl border border-outline-variant/30 overflow-hidden soft-shadow flex flex-col">
            <div class="px-6 py-4 border-b border-outline-variant/30 flex justify-between items-center">
                <h3 class="font-bold text-on-surface">Bank Soal Anda</h3>
                <a class="text-primary text-sm font-semibold hover:underline" href="{{ route('guru.bank-soal.index') }}">Kelola</a>
            </div>
            <div class="p-5 space-y-3 flex-1">
                @forelse($bankSoalTerbaru as $bs)
                    <a href="{{ route('guru.bank-soal.soal.index', $bs) }}" class="block p-3.5 rounded-xl border border-outline-variant/30 hover:border-primary hover:bg-primary-fixed/15 transition-all">
                        <div class="flex justify-between items-start gap-2">
                            <p class="font-semibold text-sm text-primary">{{ $bs->nama_bank_soal }}</p>
                            <span class="px-2 py-0.5 rounded bg-secondary/10 text-secondary text-xs font-bold shrink-0">{{ $bs->soals_count }} Soal</span>
                        </div>
                        <p class="text-xs text-text-muted mt-1">{{ $bs->mataPelajaran?->nama_mapel }} • Kelas {{ $bs->tingkat_kelas }}</p>
                    </a>
                @empty
                    <p class="text-sm text-text-muted text-center py-6">Belum ada bank soal.</p>
                @endforelse
            </div>
            <div class="p-4 border-t border-outline-variant/20 bg-surface-container-low/40">
                <a href="{{ route('guru.bank-soal.create') }}" class="w-full py-2.5 bg-secondary text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 hover:bg-primary">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Bank Soal Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Banner Quick Action -->
    <div class="p-7 bg-primary-container text-on-primary rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="relative z-10">
            <h4 class="text-xl font-bold mb-1.5">Penilaian Essay & Feedback Langsung</h4>
            <p class="text-sm text-on-primary/80 max-w-xl">Anda dapat memeriksa jawaban uraian siswa per butir soal, memberikan skor sesuai bobot, dan menuliskan catatan feedback langsung.</p>
        </div>
        <div class="flex gap-3 relative z-10 shrink-0">
            <a href="{{ route('guru.essay.index') }}" class="px-6 py-3 bg-surface-white text-primary rounded-lg text-sm font-bold hover:bg-surface-bright shadow-lg">Buka Penilaian Essay</a>
        </div>
    </div>
</div>
@endsection
