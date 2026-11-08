@extends('layouts.app')

@section('title', 'Mes informations')

@section('content')
@php
    $champ = 'w-full h-12 px-3.5 bg-white border rounded-lg text-[15px] text-slate-900 placeholder-slate-400 focus:outline-hidden focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition';
    $bordure = fn (string $nom) => $errors->has($nom) ? 'border-rose-500' : 'border-slate-300';
@endphp
<div class="max-w-4xl mx-auto">
    {{-- Le titre « Mon Compte Personnel » est conservé pour les liens et repères existants --}}
    @include('account.partials.entete', ['titre' => 'Mon Compte Personnel', 'sousTitre' => 'Vos coordonnées servent à la boutique pour confirmer et livrer vos commandes.'])

    <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-6" novalidate>
        @csrf

        <section class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 grid lg:grid-cols-3 gap-6">
            <div>
                <h2 class="font-bold text-slate-900">Coordonnées</h2>
                <p class="text-sm text-slate-500 mt-1">Le téléphone est utilisé pour vous joindre sur WhatsApp.</p>
            </div>
            <div class="lg:col-span-2 grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="prenom" class="block text-sm font-semibold text-slate-800 mb-1.5">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required autocomplete="given-name" class="{{ $champ }} {{ $bordure('prenom') }}">
                    @error('prenom') <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="nom" class="block text-sm font-semibold text-slate-800 mb-1.5">Nom</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required autocomplete="family-name" class="{{ $champ }} {{ $bordure('nom') }}">
                    @error('nom') <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-800 mb-1.5">Adresse e-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email" inputmode="email" class="{{ $champ }} {{ $bordure('email') }}">
                    @error('email') <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tel" class="block text-sm font-semibold text-slate-800 mb-1.5">Téléphone WhatsApp</label>
                    <input type="tel" id="tel" name="tel" value="{{ old('tel', $user->tel) }}" placeholder="+241 07 00 00 00" autocomplete="tel" inputmode="tel" class="chiffres {{ $champ }} {{ $bordure('tel') }}">
                    @error('tel') <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 grid lg:grid-cols-3 gap-6">
            <div>
                <h2 class="font-bold text-slate-900">Mot de passe</h2>
                <p class="text-sm text-slate-500 mt-1">Laissez vide pour garder le mot de passe actuel.</p>
            </div>
            <div class="lg:col-span-2 grid sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2 sm:max-w-[calc(50%-0.625rem)]">
                    <label for="current_password" class="block text-sm font-semibold text-slate-800 mb-1.5">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password" class="{{ $champ }} {{ $bordure('current_password') }}">
                    @error('current_password') <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-semibold text-slate-800 mb-1.5">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" autocomplete="new-password" class="{{ $champ }} {{ $bordure('new_password') }}">
                    @error('new_password') <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-semibold text-slate-800 mb-1.5">Confirmer le nouveau</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" autocomplete="new-password" class="{{ $champ }} border-slate-300">
                </div>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="h-12 px-6 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg transition-colors w-full sm:w-auto">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
