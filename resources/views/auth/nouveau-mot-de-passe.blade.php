@extends('layouts.auth', ['title' => 'Nouveau mot de passe'])

@section('content')
@php
    $champ = 'w-full h-12 px-3.5 bg-white border rounded-lg text-[15px] text-slate-900 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition';
@endphp
<div class="w-full max-w-md">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 sm:p-8">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Choisir un nouveau mot de passe</h1>
        <p class="text-sm text-slate-500 mt-1 mb-6">Au moins 8 caractères.</p>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-800 mb-1.5">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email" inputmode="email"
                       class="{{ $champ }} @error('email') border-rose-500 @else border-slate-300 @enderror">
                @error('email')
                    <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-800 mb-1.5">Nouveau mot de passe</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                           class="{{ $champ }} pr-12 @error('password') border-rose-500 @else border-slate-300 @enderror">
                    <button type="button" onclick="togglePasswordVisibility('password', 'iconeMotDePasse')" class="absolute inset-y-0 right-0 w-12 flex items-center justify-center text-slate-400 hover:text-slate-700" aria-label="Afficher ou masquer le mot de passe">
                        <i class="fa-solid fa-eye text-sm" id="iconeMotDePasse"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-800 mb-1.5">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="{{ $champ }} border-slate-300">
            </div>

            <button type="submit" class="w-full h-12 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg transition-colors">
                Enregistrer le mot de passe
            </button>
        </form>
    </div>
</div>
@endsection
