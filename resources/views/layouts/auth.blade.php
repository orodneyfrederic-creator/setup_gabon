<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#283888">
    <title>{{ $title ?? 'Mon compte' }} | SETUP-Gabon</title>
    @include('layouts.partials.theme')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">

    <div class="h-1 flex" aria-hidden="true">
        <span class="flex-1 bg-emerald-600"></span>
        <span class="flex-1 bg-amber-400"></span>
        <span class="flex-1 bg-blue-700"></span>
    </div>

    <header class="max-w-md w-full mx-auto px-4 pt-8 pb-6 flex items-center justify-between">
        <a href="{{ route('home') }}" aria-label="SETUP-Gabon, retour à la boutique">
            <img src="{{ asset('img/logo.png') }}" alt="SETUP-Gabon" class="h-11 w-auto object-contain">
        </a>
        <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 hover:text-blue-700 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Boutique</span>
        </a>
    </header>

    <!-- NOTIFICATIONS -->
    <div class="fixed top-4 inset-x-4 sm:inset-x-auto sm:right-5 z-50 flex flex-col gap-3 sm:w-96 pointer-events-none" aria-live="polite">
        @if(session('success'))
            <div id="toast-success" class="pointer-events-auto bg-white border border-slate-200 text-slate-800 pl-4 pr-2 py-3 rounded-xl shadow-xl flex items-start gap-3 -translate-y-2 opacity-0 transition-all duration-300 ease-out" role="status">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5 shrink-0"></i>
                <p class="flex-1 text-sm leading-snug pt-0.5">{{ session('success') }}</p>
                <button onclick="closeToast('toast-success')" class="w-8 h-8 shrink-0 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center" aria-label="Fermer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="toast-error" class="pointer-events-auto bg-white border border-rose-200 text-slate-800 pl-4 pr-2 py-3 rounded-xl shadow-xl flex items-start gap-3 -translate-y-2 opacity-0 transition-all duration-300 ease-out" role="alert">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg mt-0.5 shrink-0"></i>
                <p class="flex-1 text-sm leading-snug pt-0.5">{{ session('error') }}</p>
                <button onclick="closeToast('toast-error')" class="w-8 h-8 shrink-0 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center" aria-label="Fermer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    </div>

    <main class="flex-1 flex flex-col items-center px-4 pb-12">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-md mx-auto px-4 py-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
            <p>© {{ date('Y') }} SETUP-Gabon</p>
            <a href="https://wa.me/24166131270" target="_blank" rel="noopener" class="flex items-center gap-1.5 hover:text-blue-700">
                <i class="fa-brands fa-whatsapp"></i>
                <span>Besoin d'aide ? <span class="chiffres">+241 66 13 12 70</span></span>
            </a>
        </div>
    </footer>

    <script>
        function closeToast(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('-translate-y-2', 'opacity-0');
                setTimeout(() => el.remove(), 300);
            }
        }

        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const estMasque = input.type === 'password';
            input.type = estMasque ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !estMasque);
            icon.classList.toggle('fa-eye-slash', estMasque);
        }

        document.addEventListener('DOMContentLoaded', () => {
            ['toast-success', 'toast-error'].forEach(id => {
                const toast = document.getElementById(id);
                if (toast) {
                    setTimeout(() => toast.classList.remove('-translate-y-2', 'opacity-0'), 100);
                    setTimeout(() => closeToast(id), id === 'toast-error' ? 9000 : 5000);
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
