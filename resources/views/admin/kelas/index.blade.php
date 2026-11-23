@extends('layouts.admin')

@section('title', 'Kelas | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Kelas</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Kelas</h2>
            <p class="text-sm text-text-muted mt-1">Kelola data kelas, tingkat, jurusan, wali kelas, dan jumlah siswa dalam sistem Ujianku.</p>
        </div>
        <a href="{{ route('admin.kelas.create') }}" class="h-12 px-6 bg-primary text-white rounded-lg font-semibold text-sm inline-flex items-center gap-2 hover:bg-primary-container shadow-md">
            <span class="material-symbols-outlined">add_circle</span>
            Tambah Kelas
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-surface-white p-5 rounded-xl border border-outline-variant/20 soft-shadow flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">meeting_room</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-text-muted uppercase">Total Kelas</p>
                <p class="text-2xl font-bold text-primary">{{ $totalKelas }}</p>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl border border-outline-variant/20 soft-shadow flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-success-green/10 flex items-center justify-center text-success-green">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-text-muted uppercase">Kelas Aktif</p>
                <p class="text-2xl font-bold text-text-main">{{ $kelasAktif }}</p>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl border border-outline-variant/20 soft-shadow flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-secondary-fixed flex items-center justify-center text-secondary">
                <span class="material-symbols-outlined">groups</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-text-muted uppercase">Total Siswa</p>
                <p class="text-2xl font-bold text-text-main">{{ $totalSiswa }}</p>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl border border-outline-variant/20 soft-shadow flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-tertiary-fixed flex items-center justify-center text-tertiary">
                <span class="material-symbols-outlined">person</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-text-muted uppercase">Wali Kelas</p>
                <p class="text-2xl font-bold text-text-main">{{ $totalWaliKelas }}</p>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-surface-white rounded-xl border border-outline-variant/20 soft-shadow overflow-hidden">
        <form method="GET" action="{{ route('admin.kelas.index') }}" class="p-5 border-b border-outline-variant/20 flex flex-col xl:flex-row gap-4">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted">search</span>
                <input name="search" value="{{ request('search') }}" class="w-full h-11 pl-10 pr-4 border border-outline-variant rounded-lg text-sm" placeholder="Cari kelas berdasarkan nama kelas, tingkat, jurusan, atau wali kelas..." type="text">
            </div>
            <div class="flex flex-wrap gap-3">
                <select name="jurusan_id" onchange="this.form.submit()" class="h-11 px-4 border border-outline-variant rounded-lg text-sm min-w-[150px]">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}" {{ (string) request('jurusan_id') === (string) $j->id ? 'selected' : '' }}>{{ $j->kode_jurusan }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()" class="h-11 px-4 border border-outline-variant rounded-lg text-sm min-w-[140px]">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <button type="submit" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold">Cari</button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant/30">
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Nama Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Jurusan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Wali Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase text-center">Jumlah Siswa</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($kelasList as $index => $kelas)
                        <tr class="hover:bg-surface-container-low/40 transition-colors">
                            <td class="px-6 py-4 text-sm">{{ $kelasList->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.kelas.show', $kelas) }}" class="text-sm font-bold text-primary hover:underline">{{ $kelas->nama_kelas }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $kelas->jurusan?->kode_jurusan ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $kelas->waliKelas?->nama_lengkap ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $kelas->tahunAjaran?->tahun }} ({{ $kelas->tahunAjaran?->semester }})</td>
                            <td class="px-6 py-4 text-sm text-center font-semibold">{{ $kelas->siswas_count }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $kelas->is_active ? 'bg-success-green/10 text-success-green' : 'bg-error-container text-error' }}">
                                    {{ $kelas->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('admin.kelas.show', $kelas) }}" class="p-2 text-primary hover:bg-primary-fixed rounded-lg" title="Kelola Kelas">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>
                                    <a href="{{ route('admin.kelas.edit', $kelas) }}" class="p-2 text-secondary hover:bg-secondary-fixed rounded-lg" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.kelas.destroy', $kelas) }}" onsubmit="return confirm('Hapus kelas ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-error hover:bg-error-container rounded-lg" title="Hapus">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-sm text-text-muted">Belum ada data kelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kelasList->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $kelasList->links() }}</div>
        @endif
    </div>
</div>
@endsection
