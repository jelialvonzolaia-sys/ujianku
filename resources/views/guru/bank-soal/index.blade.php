@extends('layouts.guru')

@section('title', 'Manajemen Bank Soal | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Bank Soal</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Manajemen Bank Soal</h2>
            <p class="text-sm text-on-surface-variant mt-1">Kelola dan organisasikan paket soal Anda untuk berbagai kebutuhan evaluasi.</p>
        </div>
        <a href="{{ route('guru.bank-soal.create') }}" class="h-12 bg-secondary text-white px-6 rounded-lg text-sm font-semibold flex items-center gap-2 hover:bg-primary transition-all shadow-md">
            <span class="material-symbols-outlined">add</span>
            Tambah Baru
        </a>
    </div>

    <!-- Information Card -->
    <div class="bg-secondary-fixed/35 border-l-4 border-secondary p-4 rounded-r-xl flex items-start gap-3">
        <span class="material-symbols-outlined text-secondary mt-0.5">info</span>
        <p class="text-xs md:text-sm text-on-secondary-fixed-variant leading-relaxed">
            Daftar di bawah adalah paket bank soal milik Anda. Klik pada nama bank soal atau pilih <strong>Kelola Soal</strong> untuk menambah soal secara manual maupun melakukan <strong>Import Soal</strong> dari Excel.
        </p>
    </div>

    <!-- Filter & Search -->
    <form method="GET" action="{{ route('guru.bank-soal.index') }}" class="bg-surface-white rounded-xl p-4 soft-shadow border border-outline-variant/20 flex flex-wrap items-center gap-3">
        <select name="mata_pelajaran_id" onchange="this.form.submit()" class="h-11 bg-surface-container-low border border-outline-variant/40 rounded-lg px-4 text-sm">
            <option value="">Semua Mata Pelajaran</option>
            @foreach($mapels as $m)
                <option value="{{ $m->id }}" {{ (string) request('mata_pelajaran_id') === (string) $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
            @endforeach
        </select>
        <select name="tingkat_kelas" onchange="this.form.submit()" class="h-11 bg-surface-container-low border border-outline-variant/40 rounded-lg px-4 text-sm">
            <option value="">Semua Tingkat</option>
            @foreach(['X', 'XI', 'XII'] as $t)
                <option value="{{ $t }}" {{ request('tingkat_kelas') === $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
            @endforeach
        </select>
        <div class="relative flex-1 min-w-[220px]">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input name="search" value="{{ request('search') }}" class="w-full h-11 bg-surface-container-low border border-outline-variant/40 rounded-lg pl-10 pr-4 text-sm" placeholder="Cari Nama Bank Soal, Kode, atau Materi..." type="text">
        </div>
        <button type="submit" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold">Cari</button>
        <a href="{{ route('guru.bank-soal.index') }}" class="h-11 px-4 flex items-center gap-1.5 text-sm font-semibold text-secondary hover:bg-secondary/5 rounded-lg border border-secondary/20">
            <span class="material-symbols-outlined text-[18px]">filter_list</span>
            Reset
        </a>
    </form>

    <!-- Table -->
    <div class="bg-surface-white rounded-xl overflow-hidden soft-shadow border border-outline-variant/20">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant/30">
                        <th class="px-5 py-4 text-xs font-semibold text-primary uppercase w-14">No</th>
                        <th class="px-5 py-4 text-xs font-semibold text-primary uppercase">Nama Bank Soal</th>
                        <th class="px-5 py-4 text-xs font-semibold text-primary uppercase">Mapel & Tingkat</th>
                        <th class="px-5 py-4 text-xs font-semibold text-primary uppercase">Kode Soal</th>
                        <th class="px-5 py-4 text-xs font-semibold text-primary uppercase">Diubah</th>
                        <th class="px-5 py-4 text-xs font-semibold text-primary uppercase text-center">Jumlah Soal</th>
                        <th class="px-5 py-4 text-xs font-semibold text-primary uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-sm">
                    @forelse($bankSoals as $idx => $bs)
                        <tr class="hover:bg-surface-container-low/40 transition-colors">
                            <td class="px-5 py-4 text-on-surface-variant">{{ $bankSoals->firstItem() + $idx }}</td>
                            <td class="px-5 py-4">
                                <a href="{{ route('guru.bank-soal.soal.index', $bs) }}" class="font-bold text-primary hover:underline">{{ $bs->nama_bank_soal }}</a>
                                @if($bs->materi)
                                    <p class="text-xs text-text-muted">{{ $bs->materi }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $bs->mataPelajaran?->nama_mapel }}</p>
                                <p class="text-xs text-text-muted">Kelas {{ $bs->tingkat_kelas }} {{ $bs->kelas ? '(' . $bs->kelas->nama_kelas . ')' : '' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-mono bg-surface-container-low px-2.5 py-1 rounded text-primary font-semibold">{{ $bs->kode_soal }}</span>
                            </td>
                            <td class="px-5 py-4 text-xs text-on-surface-variant">{{ $bs->updated_at->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-secondary/10 text-secondary">
                                    {{ $bs->soals_count }} Soal
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end items-center gap-1.5">
                                    <a href="{{ route('guru.bank-soal.soal.index', $bs) }}" class="px-3 py-1.5 bg-primary text-white rounded-lg text-xs font-semibold hover:bg-primary-container">Kelola Soal</a>
                                    <a href="{{ route('guru.bank-soal.edit', $bs) }}" class="p-1.5 text-secondary hover:bg-secondary-fixed rounded-lg" title="Edit Konfigurasi"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                                    <form method="POST" action="{{ route('guru.bank-soal.duplicate', $bs) }}">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-primary hover:bg-primary-fixed rounded-lg" title="Duplikasi"><span class="material-symbols-outlined text-[18px]">content_copy</span></button>
                                    </form>
                                    <form method="POST" action="{{ route('guru.bank-soal.destroy', $bs) }}" onsubmit="return confirm('Hapus bank soal ini beserta semua soalnya?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-error hover:bg-error-container rounded-lg" title="Hapus"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-text-muted">Belum ada bank soal. Klik "Tambah Baru" untuk membuat paket soal pertama.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bankSoals->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $bankSoals->links() }}</div>
        @endif
    </div>
</div>
@endsection
