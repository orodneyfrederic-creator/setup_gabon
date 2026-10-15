@extends('layouts.auth')

@section('content')
<div class="w-full max-w-sm">

    <!-- CARTE FORMULAIRE -->
    <div class="bg-white border border-gray-300 rounded-lg p-6 mb-4">

        <h1 class="text-2xl font-medium text-gray-900 mb-5">Créer un compte</h1>

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

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Nom & Prénom -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="nom" class="block text-sm font-bold text-gray-800 mb-1">Nom</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required placeholder="Mboumba"
                           class="w-full px-3 py-2 border @error('nom') border-red-500 @else border-gray-400 @enderror rounded focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm bg-white">
                    @error('nom')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="prenom" class="block text-sm font-bold text-gray-800 mb-1">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required placeholder="Jean"
                           class="w-full px-3 py-2 border @error('prenom') border-red-500 @else border-gray-400 @enderror rounded focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm bg-white">
                    @error('prenom')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-bold text-gray-800 mb-1">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="jean@exemple.ga"
                       class="w-full px-3 py-2 border @error('email') border-red-500 @else border-gray-400 @enderror rounded focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm bg-white">
                @error('email')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Téléphone -->
            <div>
                <label for="tel" class="block text-sm font-bold text-gray-800 mb-1">
                    Téléphone <span class="font-normal text-gray-500">(facultatif)</span>
                </label>
                <input type="text" id="tel" name="tel" value="{{ old('tel') }}" placeholder="+241 07 00 00 00"
                       class="w-full px-3 py-2 border @error('tel') border-red-500 @else border-gray-400 @enderror rounded focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm bg-white">
                @error('tel')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mot de passe -->
            <div>
                <label for="password" class="block text-sm font-bold text-gray-800 mb-1">Mot de passe</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required placeholder="Au moins 8 caractères"
                           class="w-full px-3 py-2 pr-10 border @error('password') border-red-500 @else border-gray-400 @enderror rounded focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm bg-white">
                    <button type="button" onclick="togglePasswordVisibility('password', 'togglePassIcon')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-800">
                        <i class="fa-solid fa-eye text-sm" id="togglePassIcon"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirmer mot de passe -->
            <div>
                <label for="password_confirmation" class="block text-sm font-bold text-gray-800 mb-1">Confirmer le mot de passe</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••"
                           class="w-full px-3 py-2 pr-10 border border-gray-400 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-sm bg-white">
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPassIcon')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-800">
                        <i class="fa-solid fa-eye text-sm" id="toggleConfirmPassIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Bouton -->
            <button type="submit"
                    class="w-full bg-blue-400 hover:bg-blue-500 text-gray-900 font-semibold py-2 px-4 rounded border border-blue-500 transition-colors text-sm">
                <i class="fa-solid fa-user-plus text-xs mr-1"></i>
                Créer mon compte
            </button>
        </form>
    </div>

    <!-- SÉPARATEUR -->
    <div class="relative text-center mb-4">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <span class="relative bg-gray-100 px-3 text-xs text-gray-500">Vous avez déjà un compte ?</span>
    </div>

    <!-- LIEN CONNEXION -->
    <a href="{{ route('login') }}"
       class="block w-full text-center bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 rounded border border-gray-300 transition-colors text-sm">
        <i class="fa-solid fa-right-to-bracket text-xs mr-1"></i>
        Se connecter
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