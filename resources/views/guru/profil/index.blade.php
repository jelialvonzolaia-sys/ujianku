@extends('layouts.guru')

@section('title', 'Profil Guru | Ujianku')

@section('breadcrumb')
    <a href="{{ route('guru.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
    <span class="text-primary font-semibold">Profil</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-primary">Profil Akun Guru</h2>
        <p class="text-sm text-text-muted">Perbarui informasi kontak atau ganti password akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('guru.profil.update') }}" class="bg-surface-white rounded-xl soft-shadow border border-outline-variant/20 p-6 md:p-8 space-y-5">
        @csrf @method('PUT')
        <div class="flex items-center gap-4 pb-4 border-b border-outline-variant/30">
            <div class="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xl">{{ $user->initials }}</div>
            <div>
                <p class="font-bold text-lg text-primary">{{ $guru?->nama_lengkap ?? $user->name }}</p>
                <p class="text-xs text-text-muted">NIP. {{ $guru?->nip ?? '-' }}</p>
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1.5">Nama Lengkap</label>
            <input name="nama_lengkap" value="{{ old('nama_lengkap', $guru?->nama_lengkap ?? $user->name) }}" required class="w-full h-11 px-4 rounded-lg border border-outline-variant text-sm">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1.5">Email</label>
                <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full h-11 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Nomor HP</label>
                <input name="no_hp" value="{{ old('no_hp', $guru?->no_hp) }}" class="w-full h-11 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-outline-variant/20">
            <div>
                <label class="block text-sm font-semibold mb-1.5">Password Baru (Opsional)</label>
                <input name="password" type="password" placeholder="Kosongkan jika tetap" class="w-full h-11 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Konfirmasi Password</label>
                <input name="password_confirmation" type="password" placeholder="Ulangi password baru" class="w-full h-11 px-4 rounded-lg border border-outline-variant text-sm">
            </div>
        </div>
        <div class="flex justify-end pt-3">
            <button type="submit" class="h-11 px-6 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-container">Simpan Profil</button>
        </div>
    </form>
</div>
@endsection
