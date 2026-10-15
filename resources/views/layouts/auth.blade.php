<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SETUP-GABON' }} | Authentification</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome 6 Free -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    <!-- LOGO EN HAUT, CENTRÉ, HORS DU FORMULAIRE -->
    <div class="flex justify-center pt-10 pb-5">
        <a href="{{ route('home') }}">
            <img src="{{ asset('img/logo.png') }}" alt="SETUP-GABON" class="h-14 w-auto object-contain">
        </a>
    </div>

    <!-- POPUP NOTIFICATIONS (TOASTS EN HAUT À DROITE) -->
    <div id="toastContainer" class="fixed top-6 right-5 z-50 flex flex-col gap-3 max-w-sm w-full px-4 pointer-events-none">
        @if(session('success'))
            <div id="toast-success" class="pointer-events-auto bg-white border border-green-400 text-gray-800 p-4 rounded-lg shadow-lg flex items-center justify-between gap-3 transform translate-x-full opacity-0 transition-all duration-500 ease-out" role="alert">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-check text-green-500 text-lg mt-0.5 shrink-0"></i>
                    <div>
                        <p class="text-sm font-semibold text-green-700">Succès</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
                <button onclick="closeToast('toast-success')" class="text-gray-400 hover:text-gray-700 p-1 text-base leading-none">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="toast-error" class="pointer-events-auto bg-white border border-red-400 text-gray-800 p-4 rounded-lg shadow-lg flex items-center justify-between gap-3 transform translate-x-full opacity-0 transition-all duration-500 ease-out" role="alert">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-xmark text-red-500 text-lg mt-0.5 shrink-0"></i>
                    <div>
                        <p class="text-sm font-semibold text-red-700">Erreur</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
                <button onclick="closeToast('toast-error')" class="text-gray-400 hover:text-gray-700 p-1 text-base leading-none">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- CONTENU PRINCIPAL -->
    <main class="flex-1 flex flex-col items-center px-4 pb-10">
        @yield('content')
    </main>

    <!-- PIED DE PAGE -->
    <footer class="border-t border-gray-300 py-6 text-center">
        <div class="flex flex-wrap justify-center gap-x-4 gap-y-1 text-xs text-blue-700 mb-3">
            <a href="#" class="hover:underline">Conditions d'utilisation</a>
            <a href="#" class="hover:underline">Confidentialité</a>
            <a href="#" class="hover:underline">Aide</a>
        </div>
        <p class="text-xs text-gray-500">&copy; {{ date('Y') }} SETUP-GABON. Tous droits réservés.</p>
    </footer>

    <!-- SCRIPT JS TOASTS -->
    <script>
        function closeToast(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => el.remove(), 500);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            ['toast-success', 'toast-error'].forEach(id => {
                const toast = document.getElementById(id);
                if (toast) {
                    setTimeout(() => {
                        toast.classList.remove('translate-x-full', 'opacity-0');
                    }, 100);

                    setTimeout(() => {
                        closeToast(id);
                    }, 4500);
                }
            });
        });
    </script>
</body>
</html>
