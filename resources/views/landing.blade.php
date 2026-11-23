@extends('layouts.base')

@section('title', 'Ujianku - Platform Ujian Online Profesional')
@section('body-class', 'bg-background text-text-main')

@section('body')
<header class="fixed top-0 left-0 w-full z-50 bg-surface-white shadow-[0px_16px_24px_rgba(0,52,97,0.05)] h-[72px]">
    <div class="max-w-[1280px] mx-auto px-4 md:px-12 h-full flex justify-between items-center">
        <a href="{{ route('landing') }}" class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">school</span>
            <span class="text-2xl font-bold text-primary tracking-tight">Ujianku</span>
        </a>
        <nav class="hidden md:flex gap-8 items-center text-sm">
            <a class="text-secondary font-bold border-b-2 border-secondary pb-1 transition-colors" href="#beranda">Beranda</a>
            <a class="text-text-main hover:text-secondary transition-colors" href="#features">Fitur</a>
            <a class="text-text-main hover:text-secondary transition-colors" href="#how-it-works">Cara Kerja</a>
            <a class="text-text-main hover:text-secondary transition-colors" href="#benefits">Manfaat</a>
            <a class="text-text-main hover:text-secondary transition-colors" href="#roles">Peran</a>
        </nav>
        <div class="flex items-center gap-3">
            @auth
                @php
                    $dashRoute = match(auth()->user()->role) {
                        'admin' => route('admin.dashboard'),
                        'guru' => route('guru.dashboard'),
                        default => route('siswa.dashboard'),
                    };
                @endphp
                <a href="{{ $dashRoute }}" class="bg-primary text-on-primary px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-container transition-all">
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="hidden sm:block text-sm font-semibold text-primary hover:opacity-80 px-3 py-2">Login</a>
                <a href="{{ route('login') }}" class="bg-primary text-on-primary px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-container transition-all shadow-sm">
                    Login Sekarang
                </a>
            @endauth
        </div>
    </div>
</header>

<main class="pt-[72px]">
    <!-- Hero Section -->
    <section id="beranda" class="relative overflow-hidden py-16 md:py-28 bg-surface-white">
        <div class="max-w-[1280px] mx-auto px-4 md:px-12 flex flex-col lg:flex-row items-center gap-12">
            <div class="flex-1 space-y-6 text-center lg:text-left z-10">
                <span class="inline-block px-4 py-1.5 rounded-full bg-primary-fixed text-on-primary-fixed text-xs font-semibold uppercase tracking-wider">Masa Depan Evaluasi</span>
                <h2 class="text-4xl md:text-5xl font-bold leading-tight text-primary tracking-tight">Ujian Online Sekolah Jadi Lebih Mudah</h2>
                <p class="text-base md:text-lg text-text-muted max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Optimalkan proses evaluasi belajar dengan platform ujian digital yang aman, efisien, dan otomatis. Ujianku dirancang khusus untuk meningkatkan standar integritas akademik sekolah Anda.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-2 justify-center lg:justify-start">
                    <a href="{{ route('login') }}" class="bg-primary text-on-primary px-8 py-4 rounded-lg text-sm font-semibold hover:bg-primary-container transition-all shadow-lg flex items-center justify-center gap-2">
                        Login Sekarang <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    <a href="#features" class="border-2 border-secondary text-secondary px-8 py-4 rounded-lg text-sm font-semibold hover:bg-secondary-fixed/50 transition-all flex items-center justify-center">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
            <div class="flex-1 relative w-full">
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-secondary/5 rounded-full blur-3xl"></div>
                <div class="relative z-10 bg-surface-container-low p-5 rounded-2xl border border-outline-variant/40 shadow-2xl">
                    <!-- Clean Interactive UI Preview Card -->
                    <div class="bg-surface-white rounded-xl p-6 border border-outline-variant/30 space-y-5">
                        <div class="flex items-center justify-between border-b border-outline-variant/30 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-primary flex items-center justify-center text-white">
                                    <span class="material-symbols-outlined text-xl">quiz</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-primary">UAS Matematika Ganjil</p>
                                    <p class="text-xs text-text-muted">Kelas XII MIPA 1 • 40 Soal</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-error-container text-on-error-container text-xs font-mono font-bold">01:24:04</span>
                        </div>
                        <div class="w-full bg-surface-container-highest rounded-full h-1.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-secondary-container to-success-green h-full w-3/4"></div>
                        </div>
                        <p class="text-sm font-medium text-on-surface leading-relaxed">
                            Diketahui sebuah segitiga siku-siku dengan panjang sisi miring 13 cm dan salah satu sisi tegak 5 cm. Berapakah luas segitiga tersebut?
                        </p>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-lg border-2 border-primary bg-primary-fixed/20 font-semibold text-primary">A. 30 cm²</div>
                            <div class="p-3 rounded-lg border border-outline-variant text-on-surface-variant">B. 60 cm²</div>
                            <div class="p-3 rounded-lg border border-outline-variant text-on-surface-variant">C. 65 cm²</div>
                            <div class="p-3 rounded-lg border border-outline-variant text-on-surface-variant">D. 120 cm²</div>
                        </div>
                    </div>
                </div>
                <div class="hidden sm:flex absolute -bottom-6 -left-6 bg-surface-white p-4 rounded-xl shadow-xl items-center gap-4 z-20 border border-outline-variant/20">
                    <div class="w-11 h-11 bg-success-green/10 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-success-green">check_circle</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-on-surface">98.5% Efisiensi</p>
                        <p class="text-xs text-text-muted">Penilaian Otomatis</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-background" id="features">
        <div class="max-w-[1280px] mx-auto px-4 md:px-12">
            <div class="text-center mb-14">
                <h3 class="text-3xl font-bold text-primary mb-3">Fitur Unggulan</h3>
                <p class="text-base text-text-muted max-w-2xl mx-auto">Dirancang secara holistik untuk memenuhi kebutuhan guru, siswa, dan manajemen sekolah.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    ['quiz', 'Ujian Online', 'Lingkungan ujian yang aman dengan token sesi, timer otomatis, dan kontrol akses ketat per kelas.'],
                    ['edit_note', 'Manajemen Soal', 'Bank soal terorganisir mendukung Pilihan Ganda, PG Kompleks, Benar/Salah, Isian Singkat, hingga Essay.'],
                    ['bolt', 'Penilaian Otomatis', 'Sistem mengoreksi soal objektif secara instan serta mendukung penilaian dan feedback manual untuk essay.'],
                    ['leaderboard', 'Dashboard Guru', 'Pantau ujian aktif, kelola bank soal, import soal dari Excel, dan analisis hasil siswa.'],
                    ['person', 'Dashboard Siswa', 'Halaman personal siswa untuk melihat ujian tersedia, mengerjakan soal tanpa distraksi, dan melihat nilai.'],
                    ['summarize', 'Laporan Hasil Ujian', 'Ekspor hasil ujian dan data peserta ke format Excel siap pakai untuk keperluan evaluasi akademik.'],
                ] as [$icon, $title, $desc])
                    <div class="bg-surface-white p-8 rounded-xl soft-shadow border border-outline-variant/20 group hover:border-secondary transition-all">
                        <div class="w-14 h-14 bg-secondary/10 rounded-lg flex items-center justify-center mb-5 group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-secondary text-3xl">{{ $icon }}</span>
                        </div>
                        <h4 class="text-xl font-semibold text-on-surface mb-2.5">{{ $title }}</h4>
                        <p class="text-sm text-text-muted leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-surface-white" id="how-it-works">
        <div class="max-w-[1280px] mx-auto px-4 md:px-12">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold text-primary mb-3">Bagaimana Ujianku Bekerja?</h3>
                <p class="text-base text-text-muted">Alur kerja sederhana dari pembuatan bank soal hingga analisis nilai.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach([
                    ['1', 'Guru Membuat Ujian', 'Susun bank soal atau import dari Excel, lalu jadwalkan ujian dengan token.'],
                    ['2', 'Siswa Login', 'Siswa masuk ke dashboard menggunakan Email atau NIS/NISN dan mulai mengerjakan.'],
                    ['3', 'Penilaian Otomatis', 'Jawaban tersimpan real-time dan sistem mengoreksi soal objektif seketika.'],
                    ['4', 'Analisis Hasil', 'Lihat rekap nilai kelas, beri skor & feedback essay, lalu unduh laporan.'],
                ] as [$num, $title, $desc])
                    <div class="bg-surface-bright p-6 rounded-xl text-center border border-outline-variant/20">
                        <div class="w-14 h-14 bg-primary text-on-primary rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg font-bold text-xl">{{ $num }}</div>
                        <h4 class="text-lg font-semibold text-on-surface mb-2">{{ $title }}</h4>
                        <p class="text-sm text-text-muted leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Benefits -->
    <section class="py-20 bg-background" id="benefits">
        <div class="max-w-[1280px] mx-auto px-4 md:px-12 flex flex-col lg:flex-row gap-14 items-center">
            <div class="w-full lg:w-1/2 grid grid-cols-2 gap-4">
                <div class="space-y-4">
                    <div class="h-44 rounded-2xl bg-secondary-container flex flex-col justify-end p-6 text-white">
                        <span class="material-symbols-outlined text-4xl mb-2">verified_user</span>
                        <p class="text-2xl font-bold">99% Aman</p>
                    </div>
                    <div class="h-48 rounded-2xl bg-surface-white p-6 border border-outline-variant/30 flex flex-col justify-between soft-shadow">
                        <span class="material-symbols-outlined text-primary text-3xl">shuffle</span>
                        <div>
                            <p class="font-bold text-primary">Acak Soal & Opsi</p>
                            <p class="text-xs text-text-muted mt-1">Urutan soal unik per siswa</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-4 pt-6">
                    <div class="h-48 rounded-2xl bg-surface-white p-6 border border-outline-variant/30 flex flex-col justify-between soft-shadow">
                        <span class="material-symbols-outlined text-secondary text-3xl">table_view</span>
                        <div>
                            <p class="font-bold text-primary">Import Excel</p>
                            <p class="text-xs text-text-muted mt-1">Upload data siswa & soal massal</p>
                        </div>
                    </div>
                    <div class="h-44 rounded-2xl bg-success-green flex flex-col justify-end p-6 text-white">
                        <span class="material-symbols-outlined text-4xl mb-2">bolt</span>
                        <p class="text-2xl font-bold">Efisien</p>
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-1/2 space-y-5">
                <h3 class="text-3xl font-bold text-primary">Mengapa Sekolah Memilih Ujianku?</h3>
                <p class="text-base text-text-muted">Kami memahami tantangan dunia pendidikan modern dan menghadirkan solusi tepat guna.</p>
                <div class="space-y-5 pt-2">
                    @foreach([
                        ['verified', 'Mudah Digunakan', 'Antarmuka intuitif dan bersih untuk admin, guru, maupun siswa.'],
                        ['security', 'Aman dan Terpercaya', 'Dilengkapi validasi token sesi, batas waktu, dan pencatatan aktivitas.'],
                        ['timer', 'Hemat Waktu', 'Mengurangi waktu administrasi ujian dan koreksi hingga 80%.'],
                        ['devices', 'Aksesibilitas Multi-Device', 'Nyaman digunakan di PC lab komputer, laptop, tablet, maupun smartphone.'],
                    ] as [$icon, $title, $desc])
                        <div class="flex items-start gap-4">
                            <div class="bg-secondary/10 p-2.5 rounded-lg mt-0.5">
                                <span class="material-symbols-outlined text-secondary">{{ $icon }}</span>
                            </div>
                            <div>
                                <h5 class="text-lg font-semibold text-on-surface">{{ $title }}</h5>
                                <p class="text-sm text-text-muted">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- User Roles -->
    <section class="py-20 bg-surface-white" id="roles">
        <div class="max-w-[1280px] mx-auto px-4 md:px-12 text-center">
            <h3 class="text-3xl font-bold text-primary mb-14">Pengalaman Khusus Untuk Anda</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                <div class="bg-background p-9 rounded-2xl border border-outline-variant/30">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">admin_panel_settings</span>
                    </div>
                    <h4 class="text-xl font-bold text-on-surface mb-3">Admin Sekolah</h4>
                    <p class="text-sm text-text-muted mb-6">Kelola tahun ajaran, jurusan, kelas, mapel, akun guru/siswa, serta pantau seluruh ujian.</p>
                    <a href="{{ route('login') }}" class="text-primary font-semibold text-sm hover:underline">Masuk sebagai Admin →</a>
                </div>
                <div class="bg-primary p-10 rounded-2xl shadow-xl text-on-primary relative">
                    <div class="absolute -top-3.5 right-8 bg-success-green px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Portal Utama</div>
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-white text-4xl" style="font-variation-settings: 'FILL' 1;">co_present</span>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Guru Pengajar</h4>
                    <p class="text-sm text-white/80 mb-6">Susun bank soal, jadwalkan ujian, pantau progres peserta, dan beri nilai serta feedback essay.</p>
                    <a href="{{ route('login') }}" class="inline-block bg-white text-primary px-6 py-3 rounded-lg font-semibold text-sm hover:bg-surface-dim transition-all">Mulai Mengajar</a>
                </div>
                <div class="bg-background p-9 rounded-2xl border border-outline-variant/30">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">person_book</span>
                    </div>
                    <h4 class="text-xl font-bold text-on-surface mb-3">Siswa</h4>
                    <p class="text-sm text-text-muted mb-6">Kerjakan ujian tanpa distraksi, lihat riwayat pengerjaan, dan pantau hasil nilai secara transparan.</p>
                    <a href="{{ route('login') }}" class="text-primary font-semibold text-sm hover:underline">Kerjakan Ujian →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16">
        <div class="max-w-[1280px] mx-auto px-4 md:px-12">
            <div class="bg-primary rounded-3xl p-10 md:p-16 text-center relative overflow-hidden">
                <h3 class="text-3xl md:text-4xl font-bold text-on-primary mb-4">Siap Mentransformasi Ujian di Sekolah Anda?</h3>
                <p class="text-base text-on-primary/80 mb-8 max-w-2xl mx-auto">Masuk ke portal Ujianku untuk mulai mengelola bank soal, jadwal ujian, dan penilaian digital.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-white text-primary px-8 py-4 rounded-full font-semibold hover:bg-surface-dim transition-all shadow-xl">
                    Mulai Gunakan Ujianku Sekarang
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
</main>

<footer class="bg-surface-container-lowest border-t border-outline-variant">
    <div class="max-w-[1280px] mx-auto px-4 md:px-12 py-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">school</span>
            <span class="font-bold text-primary">Ujianku</span>
        </div>
        <p class="text-sm text-text-muted">© {{ date('Y') }} Ujianku. Platform Ujian Online Profesional untuk Masa Depan Pendidikan.</p>
    </div>
</footer>
@endsection
