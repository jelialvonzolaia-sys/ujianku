@extends('layouts.admin')

@section('title', 'Mata Pelajaran | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Mata Pelajaran</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Mata Pelajaran</h2>
            <p class="text-sm text-on-surface-variant mt-1">Kelola master mata pelajaran sekolah untuk jadwal kelas, bank soal, dan ujian.</p>
        </div>
        <a href="{{ route('admin.mapel.create') }}" class="h-12 inline-flex items-center gap-2 px-6 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-container shadow-md">
            <span class="material-symbols-outlined">add</span>
            Tambah Mata Pelajaran
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">book</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-on-surface-variant uppercase">Total Mata Pelajaran</p>
                <h3 class="text-2xl font-bold text-primary">{{ $totalMapel }}</h3>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-success-green/10 flex items-center justify-center text-success-green">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-on-surface-variant uppercase">Mata Pelajaran Aktif</p>
                <h3 class="text-2xl font-bold text-success-green">{{ $totalAktif }}</h3>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.mapel.index') }}" class="bg-surface-white p-4 rounded-xl soft-shadow border border-outline-variant/20 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input name="search" value="{{ request('search') }}" class="w-full h-11 pl-10 pr-4 border border-outline-variant rounded-lg bg-surface-bright text-sm" placeholder="Cari kode atau nama mata pelajaran..." type="text">
        </div>
        <select name="status" onchange="this.form.submit()" class="h-11 px-4 border border-outline-variant rounded-lg bg-surface-bright text-sm md:w-48">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold">Cari</button>
    </form>

    <div class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant/40">
                        <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase">No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase">Kode</th>
                        <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase text-center">Kelas Terhubung</th>
                        <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase text-center">Bank Soal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-on-surface-variant uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-high">
                    @forelse($mapels as $index => $mapel)
                        <tr class="hover:bg-surface-container-low/40 transition-colors">
                            <td class="px-6 py-4 text-sm">{{ $mapels->firstItem() + $index }}</td>
                            <td class="px-6 py-4"><span class="px-2.5 py-1 rounded bg-secondary-fixed text-secondary text-xs font-bold">{{ $mapel->kode_mapel }}</span></td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-primary">{{ $mapel->nama_mapel }}</p>
                                @if($mapel->deskripsi)
                                    <p class="text-xs text-text-muted truncate max-w-xs">{{ $mapel->deskripsi }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-center font-semibold">{{ $mapel->kelas_mata_pelajarans_count }}</td>
                            <td class="px-6 py-4 text-sm text-center font-semibold">{{ $mapel->bank_soals_count }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $mapel->is_active ? 'bg-success-green/10 text-success-green' : 'bg-error-container text-error' }}">
                                    {{ $mapel->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.mapel.edit', $mapel) }}" class="p-1.5 text-tertiary hover:bg-tertiary-fixed rounded-lg"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                                    <form method="POST" action="{{ route('admin.mapel.destroy', $mapel) }}" onsubmit="return confirm('Hapus mata pelajaran ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-error hover:bg-error-container rounded-lg"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-text-muted">Belum ada mata pelajaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mapels->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $mapels->links() }}</div>
        @endif
    </div>
</div>
@endsection
