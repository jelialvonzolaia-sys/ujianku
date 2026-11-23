@extends('layouts.base')

@section('body-class', 'bg-surface text-on-surface min-h-screen')

@php
    $activeTa = \App\Models\TahunAjaran::where('is_active', true)->first();
    $schoolName = \App\Models\SchoolSetting::getValue('school_name', 'Ujianku Academic');
    $adminUser = auth()->user();
    $navItems = [
        ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.guru.index', 'pattern' => 'admin.guru.*', 'icon' => 'person_book', 'label' => 'Data Guru'],
        ['route' => 'admin.siswa.index', 'pattern' => 'admin.siswa.*', 'icon' => 'group', 'label' => 'Data Siswa'],
        ['route' => 'admin.kelas.index', 'pattern' => 'admin.kelas.*', 'icon' => 'school', 'label' => 'Kelas'],
        ['route' => 'admin.jurusan.index', 'pattern' => 'admin.jurusan.*', 'icon' => 'account_tree', 'label' => 'Jurusan'],
        ['route' => 'admin.mapel.index', 'pattern' => 'admin.mapel.*', 'icon' => 'book', 'label' => 'Mata Pelajaran'],
        ['route' => 'admin.tahun-ajaran.index', 'pattern' => 'admin.tahun-ajaran.*', 'icon' => 'calendar_today', 'label' => 'Tahun Ajaran'],
        ['route' => 'admin.ujian.index', 'pattern' => 'admin.ujian.*', 'icon' => 'quiz', 'label' => 'Ujian'],
        ['route' => 'admin.hasil-ujian.index', 'pattern' => 'admin.hasil-ujian.*', 'icon' => 'assignment_turned_in', 'label' => 'Hasil Ujian'],
        ['route' => 'admin.laporan.index', 'pattern' => 'admin.laporan.*', 'icon' => 'assessment', 'label' => 'Laporan'],
        ['route' => 'admin.pengaturan.index', 'pattern' => 'admin.pengaturan.*', 'icon' => 'settings', 'label' => 'Pengaturan'],
    ];
@endphp

@section('body')
<div class="flex min-h-screen bg-surface">
    <!-- Mobile Backdrop -->
    <div id="sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/30 z-40 hidden lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="app-sidebar" class="w-64 fixed inset-y-0 left-0 bg-surface-white border-r border-outline-variant/60 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 soft-shadow">
        <div class="px-6 py-5 flex items-center gap-3 border-b border-outline-variant/30">
            <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-white shrink-0">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">school</span>
            </div>
            <div class="min-w-0">
                <h1 class="text-xl font-bold text-primary leading-tight truncate">Ujianku</h1>
                <p class="text-[11px] font-medium text-text-muted truncate">Admin Portal</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4 space-y-1">
            @foreach($navItems as $item)
                @php $isActive = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm transition-all duration-150 {{ $isActive ? 'bg-primary-fixed/80 text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-medium' }}">
                    <span class="material-symbols-outlined text-[20px]" @if($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="p-4 border-t border-outline-variant/40 space-y-3">
            <div class="flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs shrink-0">
                    {{ $adminUser->initials }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-on-surface truncate">{{ $adminUser->name }}</p>
                    <p class="text-[11px] text-text-muted truncate">{{ $schoolName }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 text-xs font-semibold text-error hover:bg-error-container/40 rounded-lg transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 lg:ml-64 flex flex-col min-w-0">
        <!-- TopAppBar -->
        <header class="sticky top-0 z-30 h-16 bg-surface-white border-b border-outline-variant/50 px-4 md:px-8 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button type="button" onclick="toggleMobileSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-surface-container-low text-on-surface-variant">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div class="hidden sm:flex items-center gap-2 text-sm text-text-muted truncate">
                    @yield('breadcrumb')
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($activeTa)
                    <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-fixed/60 text-primary text-xs font-semibold">
                        <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                        TA {{ $activeTa->tahun }} • {{ $activeTa->semester }}
                    </span>
                @endif
                <a href="{{ route('admin.pengaturan.index') }}" class="p-2 rounded-full text-on-surface-variant hover:bg-surface-container-low hover:text-primary transition-colors" title="Pengaturan">
                    <span class="material-symbols-outlined text-[20px]">settings</span>
                </a>
                <div class="h-6 w-px bg-outline-variant/50 hidden sm:block"></div>
                <div class="flex items-center gap-2.5">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-semibold text-on-surface leading-tight">{{ $adminUser->name }}</p>
                        <p class="text-[10px] text-text-muted uppercase tracking-wider">Administrator</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs">
                        {{ $adminUser->initials }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 md:p-8 max-w-[1280px] w-full mx-auto">
            @if($errors->any())
                <div class="mb-6 bg-error-container/70 border-l-4 border-error text-on-error-container p-4 rounded-r-xl text-sm">
                    <div class="font-semibold flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        Terdapat kesalahan pada input Anda:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        sidebar.classList.toggle('-translate-x-full');
        backdrop.classList.toggle('hidden');
    }
</script>
@endpush
