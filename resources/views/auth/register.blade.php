@extends('layouts.auth', ['title' => 'Créer un compte'])

@section('content')
@php
    $champ = 'w-full h-12 px-3.5 bg-white border rounded-lg text-[15px] text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition';
@endphp
<div class="w-full max-w-md">

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 sm:p-8">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Créer un compte</h1>
        <p class="text-sm text-slate-500 mt-1 mb-6">Un compte suffit pour commander, suivre vos commandes et négocier un prix.</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-5" novalidate>
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-3">
                <div>
                    <label for="prenom" class="block text-sm font-semibold text-slate-800 mb-1.5">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required autocomplete="given-name"
                           class="{{ $champ }} @error('prenom') border-rose-500 @else border-slate-300 @enderror">
                    @error('prenom')
                        <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nom" class="block text-sm font-semibold text-slate-800 mb-1.5">Nom</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required autocomplete="family-name"
                           class="{{ $champ }} @error('nom') border-rose-500 @else border-slate-300 @enderror">
                    @error('nom')
                        <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-800 mb-1.5">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" inputmode="email"
                       class="{{ $champ }} @error('email') border-rose-500 @else border-slate-300 @enderror">
                @error('email')
                    <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tel" class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Téléphone WhatsApp <span class="font-normal text-slate-500">(facultatif)</span>
                </label>
                <input type="tel" id="tel" name="tel" value="{{ old('tel') }}" placeholder="+241 07 00 00 00" autocomplete="tel" inputmode="tel"
                       class="{{ $champ }} chiffres @error('tel') border-rose-500 @else border-slate-300 @enderror">
                <p class="text-xs text-slate-500 mt-1.5">La boutique vous recontacte sur ce numéro pour confirmer la livraison.</p>
                @error('tel')
                    <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-800 mb-1.5">Mot de passe</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required autocomplete="new-password" aria-describedby="password-aide"
                           class="{{ $champ }} pr-12 @error('password') border-rose-500 @else border-slate-300 @enderror">
                    <button type="button" onclick="togglePasswordVisibility('password', 'togglePassIcon')"
                            class="absolute inset-y-0 right-0 w-12 flex items-center justify-center text-slate-400 hover:text-slate-700" aria-label="Afficher ou masquer le mot de passe">
                        <i class="fa-solid fa-eye text-sm" id="togglePassIcon"></i>
                    </button>
                </div>
                <p id="password-aide" class="text-xs text-slate-500 mt-1.5">Au moins 8 caractères.</p>
                @error('password')
                    <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-800 mb-1.5">Confirmer le mot de passe</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                           class="{{ $champ }} pr-12 border-slate-300">
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPassIcon')"
                            class="absolute inset-y-0 right-0 w-12 flex items-center justify-center text-slate-400 hover:text-slate-700" aria-label="Afficher ou masquer la confirmation">
                        <i class="fa-solid fa-eye text-sm" id="toggleConfirmPassIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full h-12 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg transition-colors">
                Créer mon compte
            </button>
            <p class="text-xs text-slate-500 leading-relaxed">
                Vos informations servent uniquement à gérer votre compte et vos commandes. Vous pouvez les consulter, les corriger ou les faire supprimer à tout moment :
                voir notre <a href="{{ route('legal.confidentialite') }}" class="font-semibold text-blue-700 underline">politique de confidentialité</a>.
            </p>
        </form>
    </div>

    <p class="text-center text-sm text-slate-600 mt-6">
        Déjà inscrit ?
        <a href="{{ route('login') }}" class="font-semibold text-blue-700 hover:text-blue-800 underline">Se connecter</a>
    </p>
</div>
@endsection
