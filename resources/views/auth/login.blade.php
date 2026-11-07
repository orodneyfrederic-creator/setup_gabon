@extends('layouts.auth', ['title' => 'Se connecter'])

@section('content')
@php
    $champ = 'w-full h-12 px-3.5 bg-white border rounded-lg text-[15px] text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition';
@endphp
<div class="w-full max-w-md">

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 sm:p-8">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Se connecter</h1>
        <p class="text-sm text-slate-500 mt-1 mb-6">Retrouvez votre panier, vos commandes et vos offres négociées.</p>

        @if (session('info'))
            <div class="bg-blue-50 text-blue-800 px-3.5 py-3 rounded-lg mb-5 text-sm flex items-start gap-2.5">
                <i class="fa-solid fa-circle-info text-blue-600 mt-0.5 shrink-0"></i>
                <p>{{ session('info') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5" novalidate>
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-800 mb-1.5">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" inputmode="email"
                       class="{{ $champ }} @error('email') border-rose-500 @else border-slate-300 @enderror"
                       @error('email') aria-invalid="true" aria-describedby="email-erreur" @enderror>
                @error('email')
                    <p id="email-erreur" class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-800 mb-1.5">Mot de passe</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                           class="{{ $champ }} pr-12 @error('password') border-rose-500 @else border-slate-300 @enderror"
                           @error('password') aria-invalid="true" aria-describedby="password-erreur" @enderror>
                    <button type="button" onclick="togglePasswordVisibility('password', 'toggleIcon')"
                            class="absolute inset-y-0 right-0 w-12 flex items-center justify-center text-slate-400 hover:text-slate-700" aria-label="Afficher ou masquer le mot de passe">
                        <i class="fa-solid fa-eye text-sm" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <p id="password-erreur" class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full h-12 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg transition-colors">
                Se connecter
            </button>
        </form>
    </div>

    <p class="text-center text-sm text-slate-600 mt-6">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="font-semibold text-blue-700 hover:text-blue-800 underline">Créer un compte</a>
    </p>
</div>
@endsection
