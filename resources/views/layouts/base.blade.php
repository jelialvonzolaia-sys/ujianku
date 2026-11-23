<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ujianku - Platform Ujian Online Profesional')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="@yield('body-class', 'bg-background text-on-surface min-h-screen')">
    @yield('body')

    @if(session('success') || session('error') || $errors->any())
        <div id="global-toast" class="fixed bottom-6 right-6 z-[100] max-w-md space-y-2">
            @if(session('success'))
                <div class="bg-success-green text-white px-5 py-3.5 rounded-xl shadow-lg flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="ml-2 opacity-80 hover:opacity-100">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-error text-white px-5 py-3.5 rounded-xl shadow-lg flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-[20px]">error</span>
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="ml-2 opacity-80 hover:opacity-100">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif
        </div>
        <script>
            setTimeout(() => {
                const t = document.getElementById('global-toast');
                if (t) {
                    t.style.transition = 'opacity 0.4s ease';
                    t.style.opacity = '0';
                    setTimeout(() => t.remove(), 400);
                }
            }, 4500);
        </script>
    @endif

    @stack('scripts')
</body>
</html>
