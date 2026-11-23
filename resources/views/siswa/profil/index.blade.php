@extends('layouts.siswa')

@section('title', 'Profil Saya | Ujianku')
@section('page-title', 'Profil Akun Siswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-success-green text-green-800 p-4 rounded-r-xl text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-surface-white rounded-2xl soft-shadow border border-outline-variant/40 p-6 md:p-8 flex flex-col sm:flex-row items-center gap-6">
        <div class="w-20 h-20 rounded-full bg-primary-fixed text-primary font-bold text-2xl flex items-center justify-center border-2 border-primary-container shrink-0">
            {{ $user->initials }}
        </div>
        <div class="text-center sm:text-left">
            <h2 class="text-2xl font-bold text-primary">{{ $siswa?->nama_lengkap ?? $user->name }}</h2>
            <p class="text-sm text-secondary font-medium mt-0.5">
                NIS {{ $siswa?->nis ?? '-' }} • {{ $siswa?->kelas?->nama_kelas ?? 'Belum ada kelas' }}
            </p>
            <p class="text-xs text-text-muted mt-1">{{ $siswa?->jurusan?->nama_jurusan ?? 'Umum' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-2 bg-surface-white rounded-2xl soft-shadow border border-outline-variant/40 p-6 space-y-4 h-fit">
            <h3 class="text-base font-bold text-primary">Informasi Akademik</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs text-text-muted">NIS / NISN</p>
                    <p class="font-semibold text-on-surface">{{ $siswa?->nis ?? '-' }} / {{ $siswa?->nisn ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-text-muted">Kelas Saat Ini</p>
                    <p class="font-semibold text-on-surface">{{ $siswa?->kelas?->nama_kelas ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-text-muted">Jurusan</p>
                    <p class="font-semibold text-on-surface">{{ $siswa?->jurusan?->nama_jurusan ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-text-muted">Jenis Kelamin</p>
                    <p class="font-semibold text-on-surface">{{ $siswa?->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa?->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('siswa.profil.update') }}" class="lg:col-span-3 bg-surface-white rounded-2xl soft-shadow border border-outline-variant/40 p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <h3 class="text-base font-bold text-primary">Perbarui Kontak & Keamanan</h3>
                <p class="text-xs text-text-muted mt-0.5">Kosongkan kolom password jika tidak ingin mengganti kata sandi.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Nomor HP / WhatsApp</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $siswa?->no_hp) }}" placeholder="08xxxxxxxxxx"
                       class="w-full px-4 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-outline-variant/30">
                <div>
                    <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Password Baru</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter"
                           class="w-full px-4 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-on-surface-variant mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                           class="w-full px-4 py-2.5 rounded-xl border border-outline-variant/60 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-container transition-colors cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
