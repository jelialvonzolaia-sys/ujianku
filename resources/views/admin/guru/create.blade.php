@extends('layouts.admin')

@section('title', 'Tambah Guru | Ujianku Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <a href="{{ route('admin.guru.index') }}" class="hover:text-primary">Data Guru</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Tambah Guru</span>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('admin.guru.index') }}" class="inline-flex items-center gap-1.5 text-secondary font-semibold text-sm hover:underline mb-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Data Guru
        </a>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-3xl">person_add</span>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-primary">Tambah Guru</h2>
                <p class="text-sm text-on-surface-variant">Tambahkan data guru baru ke sistem Ujianku.</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.guru.store') }}" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Informasi Akun Guru -->
            <div class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
                <div class="flex items-center gap-2 text-primary border-b border-outline-variant/30 pb-3">
                    <span class="material-symbols-outlined">lock_open</span>
                    <h3 class="text-lg font-bold">Informasi Akun Guru</h3>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Nama Lengkap <span class="text-error">*</span></label>
                    <input name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Budi Santoso, S.Pd" class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Email <span class="text-error">*</span></label>
                    <input name="email" type="email" value="{{ old('email') }}" required placeholder="budi@ujianku.sch.id" class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1.5">Password <span class="text-error">*</span></label>
                        <input name="password" type="password" required placeholder="Min. 6 karakter" class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1.5">Konfirmasi Password <span class="text-error">*</span></label>
                        <input name="password_confirmation" type="password" required placeholder="Ulangi password" class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                    </div>
                </div>
            </div>

            <!-- Informasi Identitas Guru -->
            <div class="bg-surface-white p-6 md:p-8 rounded-xl soft-shadow border border-outline-variant/20 space-y-5">
                <div class="flex items-center gap-2 text-primary border-b border-outline-variant/30 pb-3">
                    <span class="material-symbols-outlined">badge</span>
                    <h3 class="text-lg font-bold">Informasi Identitas Guru</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1.5">NIP <span class="text-error">*</span></label>
                        <input name="nip" value="{{ old('nip') }}" required placeholder="198706122010011002" class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-1.5">Nomor HP</label>
                        <input name="no_hp" value="{{ old('no_hp') }}" placeholder="081234567890" class="w-full h-12 px-4 rounded-lg border border-outline-variant bg-surface text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Tingkat Kelas (Multi-select)</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['X', 'XI', 'XII'] as $tingkat)
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="tingkat_kelas[]" value="{{ $tingkat }}" class="hidden peer"
                                       {{ in_array($tingkat, old('tingkat_kelas', []), true) ? 'checked' : '' }}>
                                <span class="px-4 py-2 rounded-full border border-outline-variant text-sm text-on-surface-variant peer-checked:bg-secondary-container peer-checked:text-white peer-checked:border-secondary transition-all">
                                    Kelas {{ $tingkat }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Status Akun</label>
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

        <div class="bg-surface-white p-5 rounded-xl border border-outline-variant/20 soft-shadow flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 text-on-surface-variant text-xs italic">
                <span class="material-symbols-outlined text-secondary">info</span>
                <p>Data guru akan digunakan untuk membuat akun login dan menghubungkan guru dengan mata pelajaran yang relevan.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.guru.index') }}" class="h-12 px-7 flex items-center rounded-lg border-2 border-secondary text-secondary text-sm font-semibold hover:bg-secondary/5">Batal</a>
                <button type="submit" class="h-12 px-7 rounded-lg bg-secondary text-white text-sm font-semibold hover:bg-primary flex items-center gap-2 shadow-md">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    Simpan Guru
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
