@extends('layouts.auth')

@section('content')
<div class="w-full max-w-sm">

    <!-- CARTE FORMULAIRE -->
    <div class="bg-white border border-gray-300 rounded-lg p-6 mb-4">

        <h1 class="text-2xl font-medium text-gray-900 mb-5">Se connecter</h1>

        @if (session('info'))
            <div class="bg-blue-50 border border-blue-300 text-blue-800 p-3 rounded mb-4 text-xs flex items-start gap-2">
                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 shrink-0"></i>
                <div>{{ session('info') }}</div>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-3 rounded mb-4 text-xs flex items-start gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 p-3 rounded mb-4 text-sm flex items-start gap-2">
                <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
                <ul class="space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-bold text-gray-800 mb-1">
                    Adresse e-mail
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-3 py-2 border @error('email') border-red-500 @else border-gray-400 @enderror rounded focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm bg-white">
                @error('email')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mot de passe -->
            <div>
                <label for="password" class="block text-sm font-bold text-gray-800 mb-1">
                    Mot de passe
                </label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 pr-10 border @error('password') border-red-500 @else border-gray-400 @enderror rounded focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm bg-white">
                    <button type="button" onclick="togglePasswordVisibility('password', 'toggleIcon')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-800">
                        <i class="fa-solid fa-eye text-sm" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Se souvenir -->
            <!-- <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember"
                       class="w-4 h-4 border border-gray-400 rounded text-blue-400 focus:ring-blue-400">
                <label for="remember" class="text-sm text-gray-700 cursor-pointer select-none">Se souvenir de moi</label>
            </div> -->

            <!-- Bouton -->
            <button type="submit"
                    class="w-full bg-blue-400 hover:bg-blue-500 text-gray-900 font-semibold py-2 px-4 rounded border border-blue-500 transition-colors text-sm">
                Se connecter
            </button>
        </form>
    </div>

    <!-- SÉPARATEUR -->
    <div class="relative text-center mb-4">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <span class="relative bg-gray-100 px-3 text-xs text-gray-500">Nouveau sur SETUP-GABON ?</span>
    </div>

    <!-- LIEN INSCRIPTION -->
    <a href="{{ route('register') }}"
       class="block w-full text-center bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 rounded border border-gray-300 transition-colors text-sm">
        <i class="fa-solid fa-user-plus text-xs mr-1"></i>
        Créer un compte
    </a>

</div>
@endsection

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>