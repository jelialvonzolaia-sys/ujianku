@extends('layouts.admin')

@section('title', 'Data Guru | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Data Guru</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Data Guru</h2>
            <p class="text-sm text-text-muted mt-1">Kelola akun guru, mata pelajaran, dan status akses guru dalam sistem Ujianku.</p>
        </div>
        <a href="{{ route('admin.guru.create') }}" class="h-12 inline-flex items-center gap-2 bg-primary text-on-primary px-6 rounded-lg text-sm font-semibold hover:bg-primary-container transition-all shadow-md">
            <span class="material-symbols-outlined">add</span>
            Tambah Guru
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20">
            <div class="p-3 bg-primary-fixed text-primary rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">groups</span>
            </div>
            <p class="text-xs font-semibold text-text-muted uppercase">Total Guru</p>
            <h3 class="text-3xl font-bold text-primary mt-1">{{ $totalGuru }}</h3>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20">
            <div class="p-3 bg-success-green/10 text-success-green rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>
            <p class="text-xs font-semibold text-text-muted uppercase">Guru Aktif</p>
            <h3 class="text-3xl font-bold text-primary mt-1">{{ $guruAktif }}</h3>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20">
            <div class="p-3 bg-error-container text-error rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">cancel</span>
            </div>
            <p class="text-xs font-semibold text-text-muted uppercase">Guru Nonaktif</p>
            <h3 class="text-3xl font-bold text-primary mt-1">{{ $guruNonaktif }}</h3>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20">
            <div class="p-3 bg-secondary-fixed text-secondary rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">library_books</span>
            </div>
            <p class="text-xs font-semibold text-text-muted uppercase">Mapel Terhubung</p>
            <h3 class="text-3xl font-bold text-primary mt-1">{{ $mapelTerhubung }}</h3>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <form method="GET" action="{{ route('admin.guru.index') }}" class="p-5 border-b border-outline-variant/20 bg-surface-container-lowest/50 flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input name="search" value="{{ request('search') }}" class="w-full h-11 pl-11 pr-4 bg-white border border-outline-variant rounded-lg text-sm" placeholder="Cari guru berdasarkan nama, NIP, atau email..." type="text">
            </div>
            <div class="flex flex-wrap gap-3">
                <select name="status" onchange="this.form.submit()" class="h-11 px-4 bg-white border border-outline-variant rounded-lg text-sm min-w-[140px]">
                    <option value="">Status: Semua</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <select name="tingkat" onchange="this.form.submit()" class="h-11 px-4 bg-white border border-outline-variant rounded-lg text-sm min-w-[140px]">
                    <option value="">Semua Tingkat</option>
                    <option value="X" {{ request('tingkat') === 'X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="XI" {{ request('tingkat') === 'XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="XII" {{ request('tingkat') === 'XII' ? 'selected' : '' }}>Kelas XII</option>
                </select>
                <button type="submit" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold">Cari</button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/60">
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Nama Guru</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">NIP</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Email</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Nomor HP</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Tingkat</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($gurus as $index => $guru)
                        <tr class="hover:bg-surface-container-low/40 transition-colors">
                            <td class="px-6 py-4 text-sm">{{ $gurus->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.guru.show', $guru) }}" class="font-semibold text-sm text-primary hover:underline">{{ $guru->nama_lengkap }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-text-muted font-mono">{{ $guru->nip }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $guru->user?->email ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $guru->no_hp ?: '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-1 flex-wrap">
                                    @foreach($guru->tingkat_kelas ?? [] as $t)
                                        <span class="px-2 py-0.5 bg-primary-fixed text-primary text-[11px] font-bold rounded">{{ $t }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $guru->is_active ? 'bg-success-green/10 text-success-green' : 'bg-error-container text-error' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $guru->is_active ? 'bg-success-green' : 'bg-error' }}"></span>
                                    {{ $guru->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.guru.show', $guru) }}" class="p-2 hover:bg-primary-fixed text-primary rounded-lg transition-colors" title="Detail">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>
                                    <a href="{{ route('admin.guru.edit', $guru) }}" class="p-2 hover:bg-secondary-fixed text-secondary rounded-lg transition-colors" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.guru.destroy', $guru) }}" onsubmit="return confirm('Hapus data guru ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 hover:bg-error-container text-error rounded-lg transition-colors" title="Hapus">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-sm text-text-muted">Belum ada data guru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($gurus->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $gurus->links() }}</div>
        @endif
    </div>
</div>
@endsection
