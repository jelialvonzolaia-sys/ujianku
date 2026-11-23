@extends('layouts.base')

@section('body-class', 'bg-surface-bright text-on-surface min-h-screen')

@php
    $guruUser = auth()->user();
    $guru = $guruUser?->guru;
    $pendingEssayCount = $guru
        ? \App\Models\UjianPeserta::whereHas('ujian', fn($q) => $q->where('guru_id', $guru->id))
            ->where('status_koreksi', 'menunggu_essay')
            ->count()
        : 0;
    $navItems = [
        ['route' => 'guru.dashboard', 'pattern' => 'guru.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'guru.ujian.index', 'pattern' => 'guru.ujian.index|guru.ujian.show|guru.ujian.edit', 'icon' => 'description', 'label' => 'Ujian Saya'],
        ['route' => 'guru.ujian.create', 'pattern' => 'guru.ujian.create', 'icon' => 'add_box', 'label' => 'Buat Ujian'],
        ['route' => 'guru.bank-soal.index', 'pattern' => 'guru.bank-soal.*', 'icon' => 'database', 'label' => 'Bank Soal'],
        ['route' => 'guru.hasil.index', 'pattern' => 'guru.hasil.*', 'icon' => 'analytics', 'label' => 'Hasil Ujian'],
        ['route' => 'guru.essay.index', 'pattern' => 'guru.essay.*', 'icon' => 'edit_note', 'label' => 'Penilaian Essay', 'badge' => $pendingEssayCount],
        ['route' => 'guru.profil.index', 'pattern' => 'guru.profil.*', 'icon' => 'person', 'label' => 'Profil'],
    ];
@endphp

@section('body')
<div class="flex min-h-screen bg-surface-bright">
    <div id="sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/30 z-40 hidden lg:hidden"></div>

    <aside id="app-sidebar" class="w-64 fixed inset-y-0 left-0 bg-surface-white border-r border-outline-variant/60 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 soft-shadow">
        <div class="p-6 flex items-center gap-3 border-b border-outline-variant/30">
            <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-white shrink-0">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">school</span>
            </div>
            <div class="min-w-0">
                <h1 class="text-primary font-bold text-lg leading-tight">Ujianku</h1>
                <p class="text-text-muted text-xs">Teacher Portal</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">
            @foreach($navItems as $item)
                @php $isActive = request()->routeIs(explode('|', $item['pattern'])); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm transition-colors {{ $isActive ? 'bg-primary-fixed text-on-primary-fixed font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-medium' }}">
                    <span class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]" @if($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </span>
                    @if(!empty($item['badge']) && $item['badge'] > 0)
                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-error text-white">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="p-4 border-t border-outline-variant/40">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 text-error font-semibold text-sm hover:bg-error-container/30 rounded-lg transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 lg:ml-64 flex flex-col min-w-0">
        <header class="sticky top-0 z-30 h-16 bg-surface-white border-b border-outline-variant/50 px-4 md:px-8 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button type="button" onclick="toggleMobileSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-surface-container-low text-on-surface-variant">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div class="flex items-center gap-2 text-sm text-text-muted truncate">
                    @yield('breadcrumb')
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('guru.ujian.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white rounded-lg text-xs font-semibold hover:bg-primary-container transition-colors">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Buat Ujian
                </a>
                <div class="h-6 w-px bg-outline-variant/50 hidden sm:block"></div>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-on-surface leading-tight">{{ $guru?->nama_lengkap ?? $guruUser->name }}</p>
                        <p class="text-[11px] text-text-muted">NIP. {{ $guru?->nip ?? '-' }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold text-xs">
                        {{ $guruUser->initials }}
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-8 max-w-[1280px] w-full mx-auto">
            @if($errors->any())
                <div class="mb-6 bg-error-container/70 border-l-4 border-error text-on-error-container p-4 rounded-r-xl text-sm">
                    <div class="font-semibold flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        Periksa kembali data yang diinput:
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
        document.getElementById('app-sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebar-backdrop').classList.toggle('hidden');
    }
</script>
@endpush
