@extends('layouts.base')

@section('body-class', 'bg-background text-on-background min-h-screen')

@php
    $siswaUser = auth()->user();
    $siswa = $siswaUser?->siswa;
    $navItems = [
        ['route' => 'siswa.dashboard', 'pattern' => 'siswa.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'siswa.ujian.index', 'pattern' => 'siswa.ujian.*', 'icon' => 'assignment', 'label' => 'Ujian Tersedia'],
        ['route' => 'siswa.riwayat.index', 'pattern' => 'siswa.riwayat.*', 'icon' => 'history', 'label' => 'Riwayat Ujian'],
        ['route' => 'siswa.nilai.index', 'pattern' => 'siswa.nilai.*', 'icon' => 'grade', 'label' => 'Nilai Saya'],
        ['route' => 'siswa.profil.index', 'pattern' => 'siswa.profil.*', 'icon' => 'person', 'label' => 'Profil'],
    ];
@endphp

@section('body')
<div class="flex min-h-screen bg-background">
    <div id="sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/30 z-40 hidden lg:hidden"></div>

    <aside id="app-sidebar" class="w-64 fixed inset-y-0 left-0 flex flex-col p-4 border-r border-outline-variant/60 bg-surface-container-lowest shadow-md z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-200">
        <div class="flex items-center gap-3 px-2 mb-8 pt-2">
            <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">school</span>
            </div>
            <div>
                <h1 class="text-xl font-bold text-primary leading-tight">Ujianku</h1>
                <p class="text-xs text-on-surface-variant">Portal Siswa</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1.5">
            @foreach($navItems as $item)
                @php $isActive = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-lg text-sm transition-all duration-200 {{ $isActive ? 'bg-primary-container text-white font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:translate-x-0.5 font-medium' }}">
                    <span class="material-symbols-outlined text-[20px]" @if($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="mt-auto pt-4 border-t border-outline-variant/40">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 text-error text-sm font-semibold rounded-lg hover:bg-error-container/50 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 lg:ml-64 flex flex-col min-h-screen min-w-0">
        <header class="sticky top-0 z-30 flex justify-between items-center w-full px-4 md:px-10 h-16 bg-surface border-b border-outline-variant/50 shadow-sm">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleMobileSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-surface-container-low text-on-surface-variant">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h2 class="text-lg font-semibold text-primary">@yield('page-title', 'Dashboard Overview')</h2>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-on-surface leading-tight">{{ $siswa?->nama_lengkap ?? $siswaUser->name }}</p>
                    <p class="text-xs text-on-surface-variant">{{ $siswa?->kelas?->nama_kelas ?? 'Belum ada kelas' }} • NIS {{ $siswa?->nis ?? '-' }}</p>
                </div>
                <div class="w-10 h-10 rounded-full border-2 border-primary-container bg-primary-fixed text-primary font-bold text-xs flex items-center justify-center">
                    {{ $siswaUser->initials }}
                </div>
            </div>
        </header>

        <div class="p-4 md:p-8 max-w-[1280px] mx-auto w-full flex-1">
            @if($errors->any())
                <div class="mb-6 bg-error-container/70 border-l-4 border-error text-on-error-container p-4 rounded-r-xl text-sm">
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="mt-auto p-6 text-center border-t border-outline-variant/50 bg-surface">
            <p class="text-on-surface-variant text-xs">© {{ date('Y') }} Ujianku Academic Portal. Modern Assessment Solutions.</p>
        </footer>
    </main>
</div>
@endsection

@push('scripts')
<script>
    function toggleMobileSidebar() {
        document.getElementById('app-sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebar-backdrop').classList.toggle('hidden');
    }
</script>
@endpush
