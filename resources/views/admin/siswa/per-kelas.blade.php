@extends('layouts.admin')

@section('title', 'Siswa Kelas ' . $kelas->nama_kelas . ' | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.siswa.index') }}" class="hover:text-primary">Data Siswa</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Kelas {{ $kelas->nama_kelas }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.siswa.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:underline mb-1">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Data Siswa
            </a>
            <h2 class="text-2xl md:text-3xl font-bold text-primary">Siswa Kelas {{ $kelas->nama_kelas }}</h2>
            <p class="text-sm text-on-surface-variant">Manajemen data peserta didik kelas {{ $kelas->nama_kelas }} ({{ $kelas->jurusan?->kode_jurusan }})</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="flex items-center px-5 h-11 border-2 border-secondary text-secondary rounded-lg text-sm font-semibold hover:bg-secondary/5">
                <span class="material-symbols-outlined mr-2 text-[20px]">upload_file</span>
                Import Excel
            </button>
            <a href="{{ route('admin.siswa.create', ['kelas_id' => $kelas->id]) }}" class="flex items-center px-5 h-11 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-container shadow-sm">
                <span class="material-symbols-outlined mr-2 text-[20px]">person_add</span>
                Tambah Siswa
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-surface-white p-5 rounded-xl custom-shadow flex items-center gap-4 border-l-4 border-primary">
            <div class="w-11 h-11 rounded-full bg-primary/10 flex items-center justify-center text-primary"><span class="material-symbols-outlined">group</span></div>
            <div>
                <p class="text-xs text-on-surface-variant uppercase font-semibold">Total Siswa</p>
                <h3 class="text-2xl font-bold text-primary">{{ $totalSiswa }}</h3>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl custom-shadow flex items-center gap-4 border-l-4 border-success-green">
            <div class="w-11 h-11 rounded-full bg-success-green/10 flex items-center justify-center text-success-green"><span class="material-symbols-outlined">check_circle</span></div>
            <div>
                <p class="text-xs text-on-surface-variant uppercase font-semibold">Siswa Aktif</p>
                <h3 class="text-2xl font-bold text-success-green">{{ $siswaAktif }}</h3>
            </div>
        </div>
        <div class="bg-surface-white p-5 rounded-xl custom-shadow flex items-center gap-4 border-l-4 border-error">
            <div class="w-11 h-11 rounded-full bg-error/10 flex items-center justify-center text-error"><span class="material-symbols-outlined">person_off</span></div>
            <div>
                <p class="text-xs text-on-surface-variant uppercase font-semibold">Nonaktif</p>
                <h3 class="text-2xl font-bold text-error">{{ $siswaNonaktif }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-surface-white rounded-xl custom-shadow overflow-hidden border border-outline-variant/20">
        <div class="p-5 border-b border-outline-variant/20 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h4 class="text-base font-bold text-primary">Daftar Siswa</h4>
            <form method="GET" action="{{ route('admin.siswa.per-kelas', $kelas) }}" class="flex gap-2 w-full sm:w-auto">
                <input name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, NISN..." class="h-10 px-3 rounded-lg border border-outline-variant text-sm w-full sm:w-64">
                <button type="submit" class="h-10 px-4 bg-primary text-white rounded-lg text-sm font-semibold">Cari</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant text-xs font-semibold uppercase">
                        <th class="px-5 py-3.5">No</th>
                        <th class="px-5 py-3.5">NIS</th>
                        <th class="px-5 py-3.5">NISN</th>
                        <th class="px-5 py-3.5">Nama Siswa</th>
                        <th class="px-5 py-3.5">Jenis Kelamin</th>
                        <th class="px-5 py-3.5">Agama</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($siswas as $idx => $s)
                        <tr class="hover:bg-primary-container/5">
                            <td class="px-5 py-3.5 text-sm">{{ $siswas->firstItem() + $idx }}</td>
                            <td class="px-5 py-3.5 text-sm font-semibold text-primary font-mono">{{ $s->nis }}</td>
                            <td class="px-5 py-3.5 text-sm font-mono">{{ $s->nisn }}</td>
                            <td class="px-5 py-3.5 text-sm font-semibold">{{ $s->nama_lengkap }}</td>
                            <td class="px-5 py-3.5 text-sm">{{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td class="px-5 py-3.5 text-sm">{{ $s->agama ?: '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $s->is_active ? 'bg-success-green/10 text-success-green' : 'bg-error/10 text-error' }}">
                                    {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex justify-center gap-1.5">
                                    <a href="{{ route('admin.siswa.edit', $s) }}" class="p-1.5 rounded-lg text-on-tertiary-fixed-variant bg-tertiary-fixed/40 hover:bg-tertiary-fixed" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                                    <form method="POST" action="{{ route('admin.siswa.toggle', $s) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="p-1.5 rounded-lg {{ $s->is_active ? 'text-error bg-error-container/40' : 'text-success-green bg-success-green/10' }}" title="{{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <span class="material-symbols-outlined text-[18px]">{{ $s->is_active ? 'person_off' : 'person_check' }}</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.siswa.destroy', $s) }}" onsubmit="return confirm('Hapus siswa ini secara permanen?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error/10" title="Hapus"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-8 text-center text-sm text-text-muted">Belum ada siswa di kelas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswas->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $siswas->links() }}</div>
        @endif
    </div>
</div>

@include('admin.siswa.partials.import-modal', ['kelasId' => $kelas->id])
@endsection
