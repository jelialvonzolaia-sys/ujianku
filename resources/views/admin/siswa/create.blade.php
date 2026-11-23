@extends('layouts.admin')

@section('title', 'Tambah Siswa | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.siswa.index') }}" class="hover:text-primary">Data Siswa</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Tambah Siswa</span>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('admin.siswa.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:underline mb-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Data Siswa
        </a>
        <h2 class="text-2xl font-bold text-primary">Tambah Siswa</h2>
        <p class="text-sm text-on-surface-variant">Tambahkan akun dan identitas peserta didik baru ke sistem Ujianku.</p>
    </div>

    <form method="POST" action="{{ route('admin.siswa.store') }}" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
                <h3 class="text-lg font-bold text-primary border-b border-outline-variant/30 pb-3">Informasi Akun Siswa</h3>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Nama Lengkap <span class="text-error">*</span></label>
                    <input name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Andi Saputra" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="andi@siswa.ujianku.sch.id (opsional, otomatis dari NIS)" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Password <span class="text-error">*</span></label>
                        <input name="password" type="password" required placeholder="Min. 6 karakter" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Konfirmasi Password <span class="text-error">*</span></label>
                        <input name="password_confirmation" type="password" required placeholder="Ulangi password" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
                <h3 class="text-lg font-bold text-primary border-b border-outline-variant/30 pb-3">Informasi Identitas Siswa</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">NIS <span class="text-error">*</span></label>
                        <input name="nis" value="{{ old('nis') }}" required placeholder="2026001" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">NISN <span class="text-error">*</span></label>
                        <input name="nisn" value="{{ old('nisn') }}" required placeholder="0087654321" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Jenis Kelamin <span class="text-error">*</span></label>
                        <select name="jenis_kelamin" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Agama</label>
                        <select name="agama" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                            @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $ag)
                                <option value="{{ $ag }}" {{ old('agama') === $ag ? 'selected' : '' }}>{{ $ag }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Kelas <span class="text-error">*</span></label>
                        <select name="kelas_id" required class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ (string) old('kelas_id', $selectedKelasId) === (string) $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->jurusan?->kode_jurusan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Nomor HP</label>
                        <input name="no_hp" value="{{ old('no_hp') }}" placeholder="081234567890" class="w-full h-12 px-4 rounded-lg border border-outline-variant text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Status Akun</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}>
                            <span class="text-sm">Aktif</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_active" value="0" {{ old('is_active') === '0' ? 'checked' : '' }}>
                            <span class="text-sm">Nonaktif</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.siswa.index') }}" class="h-12 px-7 flex items-center rounded-lg border-2 border-secondary text-secondary text-sm font-semibold">Batal</a>
            <button type="submit" class="h-12 px-7 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Siswa
            </button>
        </div>
    </form>
</div>
@endsection
