@extends('layouts.admin')

@section('title', 'Edit Guru | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.guru.index') }}" class="hover:text-primary">Data Guru</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Edit Guru</span>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('admin.guru.index') }}" class="inline-flex items-center gap-1.5 text-secondary font-semibold text-sm hover:underline mb-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Data Guru
        </a>
        <h2 class="text-2xl font-bold text-primary">Edit Guru — {{ $guru->nama_lengkap }}</h2>
    </div>

    <form method="POST" action="{{ route('admin.guru.update', $guru) }}" class="space-y-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
                <div class="flex items-center gap-2 text-primary border-b border-outline-variant/30 pb-3">
                    <span class="material-symbols-outlined">lock_open</span>
                    <h3 class="text-lg font-bold">Informasi Akun Guru</h3>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Nama Lengkap <span class="text-error">*</span></label>
                    <input name="nama_lengkap" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Email <span class="text-error">*</span></label>
                    <input name="email" type="email" value="{{ old('email', $guru->user?->email) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Password Baru</label>
                        <input name="password" type="password" placeholder="Kosongkan jika tetap" class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Konfirmasi Password</label>
                        <input name="password_confirmation" type="password" placeholder="Ulangi password baru" class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
                <div class="flex items-center gap-2 text-primary border-b border-outline-variant/30 pb-3">
                    <span class="material-symbols-outlined">badge</span>
                    <h3 class="text-lg font-bold">Informasi Identitas Guru</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">NIP <span class="text-error">*</span></label>
                        <input name="nip" value="{{ old('nip', $guru->nip) }}" required class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Nomor HP</label>
                        <input name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}" class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Tingkat Kelas (Multi-select)</label>
                    @php $selectedTingkat = old('tingkat_kelas', $guru->tingkat_kelas ?? []); @endphp
                    <div class="flex flex-wrap gap-2">
                        @foreach(['X', 'XI', 'XII'] as $tingkat)
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="tingkat_kelas[]" value="{{ $tingkat }}" class="hidden peer"
                                       {{ in_array($tingkat, $selectedTingkat, true) ? 'checked' : '' }}>
                                <span class="px-4 py-2 rounded-full border border-outline-variant text-sm text-on-surface-variant peer-checked:bg-secondary-container peer-checked:text-white peer-checked:border-secondary transition-all">
                                    Kelas {{ $tingkat }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Status Akun</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_active" value="1" {{ old('is_active', $guru->is_active ? '1' : '0') === '1' ? 'checked' : '' }}>
                            <span class="text-sm">Aktif</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_active" value="0" {{ old('is_active', $guru->is_active ? '1' : '0') === '0' ? 'checked' : '' }}>
                            <span class="text-sm">Nonaktif</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.guru.index') }}" class="h-12 px-7 flex items-center rounded-lg border-2 border-secondary text-secondary text-sm font-semibold">Batal</a>
            <button type="submit" class="h-12 px-7 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary-container flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
