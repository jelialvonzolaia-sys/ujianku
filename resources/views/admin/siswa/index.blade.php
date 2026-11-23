@extends('layouts.admin')

@section('title', 'Data Siswa | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Data Siswa</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Data Siswa</h2>
            <p class="text-sm text-on-surface-variant mt-1">Kelola data siswa, kelas, akun login, dan status peserta ujian dalam sistem Ujianku.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="h-12 px-5 border-2 border-secondary text-secondary rounded-lg text-sm font-semibold flex items-center gap-2 hover:bg-secondary/5">
                <span class="material-symbols-outlined text-[20px]">upload_file</span>
                Import Excel
            </button>
            <a href="{{ route('admin.siswa.create') }}" class="h-12 px-6 bg-primary hover:bg-primary-container text-white rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined">add</span>
                Tambah Siswa
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-surface-white p-5 rounded-xl soft-shadow flex items-start gap-4 border border-outline-variant/20">
            <div class="p-3 bg-primary-fixed text-primary rounded-lg"><span class="material-symbols-outlined">groups</span></div>
            <div>
                <p class="text-xs font-semibold text-on-surface-variant uppercase">Total Siswa</p>
                <h3 class="text-2xl font-bold text-on-surface mt-0.5">{{ $totalSiswa }}</h3>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow flex items-start gap-4 border border-outline-variant/20">
            <div class="p-3 bg-emerald-50 text-success-green rounded-lg"><span class="material-symbols-outlined">check_circle</span></div>
            <div>
                <p class="text-xs font-semibold text-on-surface-variant uppercase">Siswa Aktif</p>
                <h3 class="text-2xl font-bold text-on-surface mt-0.5">{{ $siswaAktif }}</h3>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow flex items-start gap-4 border border-outline-variant/20">
            <div class="p-3 bg-red-50 text-error rounded-lg"><span class="material-symbols-outlined">block</span></div>
            <div>
                <p class="text-xs font-semibold text-on-surface-variant uppercase">Siswa Nonaktif</p>
                <h3 class="text-2xl font-bold text-on-surface mt-0.5">{{ $siswaNonaktif }}</h3>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl soft-shadow flex items-start gap-4 border border-outline-variant/20">
            <div class="p-3 bg-amber-50 text-tertiary rounded-lg"><span class="material-symbols-outlined">school</span></div>
            <div>
                <p class="text-xs font-semibold text-on-surface-variant uppercase">Kelas Aktif</p>
                <h3 class="text-2xl font-bold text-on-surface mt-0.5">{{ $totalKelas }}</h3>
            </div>
        </div>
    </div>

    <!-- Persebaran Agama & Quick Pilih Kelas -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-5 bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20">
            <h3 class="text-base font-bold text-primary mb-1">Persebaran Agama</h3>
            <p class="text-xs text-outline mb-4">Distribusi agama siswa terdaftar</p>
            <div class="space-y-3">
                @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                    @php
                        $cnt = $agamaStats[$agama] ?? 0;
                        $pct = $totalSiswa > 0 ? round(($cnt / $totalSiswa) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span>{{ $agama }}</span>
                            <span class="text-primary">{{ $cnt }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-1.5 bg-surface-container rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-7 bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-base font-bold text-primary">Pilih Kelas (Kelola Per Kelas)</h3>
                    <p class="text-xs text-outline">Klik kelas untuk melihat daftar siswa dan import per kelas</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-64 overflow-y-auto custom-scrollbar pr-1">
                @forelse($kelasList as $kl)
                    <a href="{{ route('admin.siswa.per-kelas', $kl) }}" class="p-3.5 rounded-xl border border-outline-variant/40 hover:border-primary hover:bg-primary-fixed/15 transition-all flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-primary">{{ $kl->nama_kelas }}</p>
                            <p class="text-xs text-text-muted">{{ $kl->jurusan?->kode_jurusan }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-secondary-fixed text-secondary text-xs font-bold">{{ $kl->siswas_count }}</span>
                    </a>
                @empty
                    <p class="text-sm text-text-muted col-span-full">Belum ada kelas terdaftar.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <form method="GET" action="{{ route('admin.siswa.index') }}" class="bg-surface-white p-5 rounded-xl soft-shadow border border-outline-variant/20 flex flex-col lg:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input name="search" value="{{ request('search') }}" class="w-full h-11 pl-10 pr-4 rounded-lg bg-background border border-outline-variant text-sm" placeholder="Cari siswa berdasarkan nama, NIS, NISN, atau email..." type="text">
        </div>
        <div class="flex flex-wrap gap-3">
            <select name="kelas_id" onchange="this.form.submit()" class="h-11 px-3 rounded-lg bg-background border border-outline-variant text-sm">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ (string) request('kelas_id') === (string) $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
            <select name="jurusan_id" onchange="this.form.submit()" class="h-11 px-3 rounded-lg bg-background border border-outline-variant text-sm">
                <option value="">Semua Jurusan</option>
                @foreach($jurusans as $j)
                    <option value="{{ $j->id }}" {{ (string) request('jurusan_id') === (string) $j->id ? 'selected' : '' }}>{{ $j->kode_jurusan }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()" class="h-11 px-3 rounded-lg bg-background border border-outline-variant text-sm">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="h-11 px-5 bg-primary text-white rounded-lg text-sm font-semibold">Cari</button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-surface-white rounded-xl soft-shadow overflow-hidden border border-outline-variant/20">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant/30">
                        <th class="px-5 py-4 text-xs font-semibold text-on-surface uppercase">No</th>
                        <th class="px-5 py-4 text-xs font-semibold text-on-surface uppercase">Nama Siswa</th>
                        <th class="px-5 py-4 text-xs font-semibold text-on-surface uppercase">NIS</th>
                        <th class="px-5 py-4 text-xs font-semibold text-on-surface uppercase">NISN</th>
                        <th class="px-5 py-4 text-xs font-semibold text-on-surface uppercase">Email</th>
                        <th class="px-5 py-4 text-xs font-semibold text-on-surface uppercase">Kelas</th>
                        <th class="px-5 py-4 text-xs font-semibold text-on-surface uppercase">Status</th>
                        <th class="px-5 py-4 text-xs font-semibold text-on-surface uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($siswas as $idx => $s)
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-5 py-4 text-sm">{{ $siswas->firstItem() + $idx }}</td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.siswa.show', $s) }}" class="flex items-center gap-3 group">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-primary">{{ $s->user?->initials ?? 'S' }}</div>
                                    <span class="text-sm font-semibold text-on-surface group-hover:text-primary">{{ $s->nama_lengkap }}</span>
                                </a>
                            </td>
                            <td class="px-5 py-4 text-sm font-mono">{{ $s->nis }}</td>
                            <td class="px-5 py-4 text-sm font-mono">{{ $s->nisn }}</td>
                            <td class="px-5 py-4 text-sm text-text-muted">{{ $s->user?->email }}</td>
                            <td class="px-5 py-4 text-sm">{{ $s->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $s->is_active ? 'bg-emerald-50 text-success-green' : 'bg-red-50 text-error' }}">
                                    {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.siswa.show', $s) }}" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-fixed rounded-md"><span class="material-symbols-outlined text-[18px]">visibility</span></a>
                                    <a href="{{ route('admin.siswa.edit', $s) }}" class="p-1.5 text-on-surface-variant hover:text-secondary hover:bg-secondary-fixed rounded-md"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                                    <form method="POST" action="{{ route('admin.siswa.destroy', $s) }}" onsubmit="return confirm('Hapus siswa ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container rounded-md"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-sm text-text-muted">Belum ada data siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswas->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $siswas->links() }}</div>
        @endif
    </div>
</div>

@include('admin.siswa.partials.import-modal', ['kelasId' => null])
@endsection
