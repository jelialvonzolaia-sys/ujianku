@extends('layouts.admin')

@section('title', 'Tahun Pelajaran | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Tahun Ajaran</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Tahun Pelajaran</h2>
            <p class="text-sm text-on-surface-variant mt-1">Kelola data tahun pelajaran dan semester yang digunakan dalam sistem akademik.</p>
        </div>
        <a href="{{ route('admin.tahun-ajaran.create') }}" class="h-12 px-6 bg-primary text-on-primary rounded-lg flex items-center gap-2 hover:bg-primary-container transition-colors academic-shadow text-sm font-semibold">
            <span class="material-symbols-outlined">add</span>
            Tambah Tahun Pelajaran
        </a>
    </div>

    <!-- Filter Section -->
    <form method="GET" action="{{ route('admin.tahun-ajaran.index') }}" class="bg-surface-white rounded-xl academic-shadow p-5 border border-outline-variant/20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Cari Tahun Pelajaran</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                    <input name="search" value="{{ request('search') }}" class="w-full h-11 pl-10 pr-4 bg-background border border-outline-variant rounded-lg focus:ring-2 focus:ring-secondary/20 focus:border-secondary text-sm" placeholder="Contoh: 2025/2026..." type="text">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Semester</label>
                <select name="semester" onchange="this.form.submit()" class="w-full h-11 px-3 bg-background border border-outline-variant rounded-lg text-sm">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil" {{ request('semester') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ request('semester') === 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>
            <div class="flex gap-2">
                <select name="status" onchange="this.form.submit()" class="flex-1 h-11 px-3 bg-background border border-outline-variant rounded-lg text-sm">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <button type="submit" class="h-11 px-4 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-container">Filter</button>
            </div>
        </div>
    </form>

    <!-- Data Table -->
    <div class="bg-surface-white rounded-xl academic-shadow overflow-hidden border border-outline-variant/20">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant/30 bg-surface-container-low">
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase w-16">No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Tahun Pelajaran</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Semester</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Jumlah Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-primary uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($tahunAjarans as $index => $ta)
                        <tr class="hover:bg-background transition-colors">
                            <td class="px-6 py-4 text-sm">{{ $tahunAjarans->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-primary">{{ $ta->tahun }}</td>
                            <td class="px-6 py-4 text-sm">{{ $ta->semester }}</td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $ta->kelas_count }} Kelas</td>
                            <td class="px-6 py-4">
                                @if($ta->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-success-green/10 text-success-green text-xs font-semibold border border-success-green/20">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-surface-variant text-on-surface-variant text-xs font-semibold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.tahun-ajaran.toggle', $ta) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $ta->is_active ? 'border border-outline-variant text-on-surface-variant hover:bg-surface-container-high' : 'bg-secondary/10 text-secondary border border-secondary/20 hover:bg-secondary/20' }}">
                                            {{ $ta->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.tahun-ajaran.edit', $ta) }}" class="p-2 text-tertiary hover:bg-tertiary-fixed/40 rounded-lg transition-colors" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.tahun-ajaran.destroy', $ta) }}" onsubmit="return confirm('Hapus tahun pelajaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-error hover:bg-error-container/40 rounded-lg transition-colors" title="Hapus">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-text-muted">Belum ada data tahun pelajaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tahunAjarans->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">
                {{ $tahunAjarans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
