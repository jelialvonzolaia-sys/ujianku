@extends('layouts.admin')

@section('title', 'Edit Siswa | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.siswa.index') }}" class="hover:text-primary">Data Siswa</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Edit Siswa</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-primary">Edit Siswa</h2>
            <p class="text-sm text-on-surface-variant">Perbarui data akun, identitas, kelas, dan status siswa dalam sistem Ujianku.</p>
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="inline-flex items-center px-4 py-2 text-secondary font-semibold text-sm border-2 border-secondary rounded-lg hover:bg-secondary/5">
            <span class="material-symbols-outlined mr-1.5 text-[18px]">arrow_back</span>
            Kembali ke Data Siswa
        </a>
    </div>

    <div class="bg-surface-white p-6 rounded-xl soft-shadow border border-outline-variant/20 flex items-center gap-5">
        <div class="w-16 h-16 rounded-full bg-secondary-fixed flex items-center justify-center text-secondary font-bold text-xl">
            {{ $siswa->user?->initials ?? 'S' }}
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-1">
                <h3 class="text-lg font-bold">{{ $siswa->nama_lengkap }}</h3>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $siswa->is_active ? 'bg-success-green/10 text-success-green' : 'bg-error/10 text-error' }}">{{ $siswa->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
            <p class="text-xs text-text-muted">NIS: {{ $siswa->nis }} • NISN: {{ $siswa->nisn }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}" class="space-y-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
                <h4 class="text-lg font-bold border-b border-outline-variant/30 pb-3">Informasi Akun Siswa</h4>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Nama Lengkap</label>
                    <input name="nama_lengkap" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Email</label>
                    <input name="email" type="email" value="{{ old('email', $siswa->user?->email) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Password Baru</label>
                    <input name="password" type="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Konfirmasi Password Baru</label>
                    <input name="password_confirmation" type="password" placeholder="Ulangi password baru" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
            </div>

            <div class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
                <h4 class="text-lg font-bold border-b border-outline-variant/30 pb-3">Informasi Identitas Siswa</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">NIS</label>
                        <input name="nis" value="{{ old('nis', $siswa->nis) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">NISN</label>
                        <input name="nisn" value="{{ old('nisn', $siswa->nisn) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Jenis Kelamin</label>
                        <select name="jenis_kelamin" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                            <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Agama</label>
                        <select name="agama" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                            @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $ag)
                                <option value="{{ $ag }}" {{ old('agama', $siswa->agama) === $ag ? 'selected' : '' }}>{{ $ag }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Kelas</label>
                        <select name="kelas_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ (string) old('kelas_id', $siswa->kelas_id) === (string) $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Nomor HP</label>
                        <input name="no_hp" value="{{ old('no_hp', $siswa->no_hp) }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Status Akun</label>
                    <div class="flex gap-8 p-3 rounded-lg border border-outline-variant">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_active" value="1" {{ old('is_active', $siswa->is_active ? '1' : '0') === '1' ? 'checked' : '' }}>
                            <span class="text-sm">Aktif</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_active" value="0" {{ old('is_active', $siswa->is_active ? '1' : '0') === '0' ? 'checked' : '' }}>
                            <span class="text-sm">Nonaktif</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.siswa.index') }}" class="h-12 px-7 flex items-center rounded-lg border-2 border-outline text-outline text-sm font-semibold">Batal</a>
            <button type="submit" class="h-12 px-7 rounded-lg bg-secondary text-white text-sm font-semibold hover:bg-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
