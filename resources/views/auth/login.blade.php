@extends('layouts.base')

@section('title', 'Login | Ujianku Academic System')
@section('body-class', 'min-h-screen flex flex-col items-center justify-center p-4 md:p-12 bg-surface')

@section('body')
<main class="w-full max-w-md bg-surface-white rounded-2xl soft-shadow border border-outline-variant/40 overflow-hidden">
    <section class="w-full flex flex-col justify-center px-6 md:px-10 py-10">
        <!-- Brand Identity -->
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-primary flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">school</span>
                </div>
                <span class="text-2xl font-bold text-primary tracking-tight">Ujianku</span>
            </a>
            <a href="{{ route('landing') }}" class="text-xs font-medium text-text-muted hover:text-primary flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Beranda
            </a>
        </div>

        <!-- Title & Description -->
        <div class="mb-7">
            <h1 class="text-2xl md:text-3xl font-bold text-primary mb-2">Masuk ke Akun Ujianku</h1>
            <p class="text-sm text-on-surface-variant leading-relaxed">
                Masuk menggunakan Email Institusi, NIP (Guru), atau NIS/NISN (Siswa) untuk mengakses portal ujian secara aman.
            </p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3.5 rounded-lg bg-error-container text-on-error-container text-xs font-medium flex items-start gap-2">
                <span class="material-symbols-outlined text-[18px] shrink-0">error</span>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
            @csrf

            <div class="flex flex-col space-y-1.5">
                <label class="text-sm font-semibold text-primary" for="login">Email / NIP / NIS</label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px]">mail</span>
                    <input class="w-full h-12 pl-11 pr-4 rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-secondary-container/30 focus:border-secondary-container outline-none transition-all text-sm"
                           id="login" name="login" value="{{ old('login') }}" placeholder="nama@sekolah.sch.id / NIS / NIP" type="text" required autofocus>
                </div>
            </div>

            <div class="flex flex-col space-y-1.5">
                <label class="text-sm font-semibold text-primary" for="password">Password</label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px]">lock</span>
                    <input class="w-full h-12 pl-11 pr-11 rounded-lg border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-secondary-container/30 focus:border-secondary-container outline-none transition-all text-sm"
                           id="password" name="password" placeholder="••••••••" type="password" required>
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 text-outline hover:text-primary">
                        <span id="eye-icon" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs font-medium">
                <label class="flex items-center cursor-pointer group">
                    <input name="remember" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary-container" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                    <span class="ml-2 text-on-surface-variant group-hover:text-primary transition-colors">Ingat saya</span>
                </label>
                <button type="button" onclick="document.getElementById('help-modal').classList.remove('hidden')" class="text-secondary hover:underline">Lupa password?</button>
            </div>

            <button class="w-full h-12 bg-primary text-on-primary font-semibold text-sm rounded-lg shadow-md hover:bg-primary-container active:scale-[0.99] transition-all duration-150 cursor-pointer" type="submit">
                Login
            </button>
        </form>

        <div class="mt-6 p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40 text-xs text-on-surface-variant space-y-1">
            <p class="font-semibold text-primary">Akun Demo Tersedia (Password: <code class="font-mono bg-white px-1.5 py-0.5 rounded">password</code>):</p>
            <p>• Admin: <code class="font-mono">admin@ujianku.sch.id</code></p>
            <p>• Guru: <code class="font-mono">budi@ujianku.sch.id</code></p>
            <p>• Siswa: <code class="font-mono">andi@ujianku.sch.id</code> (atau NIS <code class="font-mono">2026001</code>)</p>
        </div>

        <div class="mt-6 text-center">
            <p class="text-xs text-outline">
                Butuh bantuan akses? <button type="button" onclick="document.getElementById('help-modal').classList.remove('hidden')" class="text-primary font-semibold hover:underline">Hubungi Admin Sekolah</button>
            </p>
        </div>
    </section>
</main>

<footer class="mt-6 text-center">
    <p class="text-xs text-outline">© {{ date('Y') }} Ujianku Academic System. Seluruh hak cipta dilindungi.</p>
</footer>

<!-- Help Modal -->
<div id="help-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 modal-overlay">
    <div class="bg-surface-white rounded-2xl p-6 max-w-sm w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-primary">Bantuan Akses Akun</h3>
            <button type="button" onclick="document.getElementById('help-modal').classList.add('hidden')" class="text-outline hover:text-error">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <p class="text-sm text-on-surface-variant leading-relaxed">
            Untuk reset password atau aktivasi akun guru/siswa, silakan hubungi Administrator sekolah atau operator CBT di ruang kurikulum.
        </p>
        <button type="button" onclick="document.getElementById('help-modal').classList.add('hidden')" class="w-full py-2.5 bg-primary text-white rounded-lg text-sm font-semibold">Mengerti</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>
@endpush
