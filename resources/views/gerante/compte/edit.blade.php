@extends('layouts.app')

@section('title', 'Mon compte')

@section('content')
@php
    $champ = 'w-full h-11 px-3 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-hidden focus:ring-4 focus:ring-blue-100 focus:border-blue-600';
@endphp
<div class="max-w-xl space-y-6">
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Mon compte</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $membre->email }} · {{ $membre->libelleRole() }}</p>
    </div>

    <form method="POST" action="{{ route('gerante.compte.update') }}" class="bg-white border border-slate-200 rounded-xl p-5 space-y-5">
        @csrf
        @method('PUT')

        <label class="block">
            <span class="block text-sm font-semibold text-slate-800 mb-1.5">Nom affiché</span>
            <input type="text" name="nom" required maxlength="120" value="{{ old('nom', $membre->nom) }}" class="{{ $champ }}">
            @error('nom') <span class="text-rose-600 text-sm mt-1 block">{{ $message }}</span> @enderror
        </label>

        <fieldset class="pt-5 border-t border-slate-100 space-y-4">
            <legend class="font-bold text-slate-900">Changer de mot de passe</legend>
            <p class="text-sm text-slate-500">Laissez vide pour garder votre mot de passe actuel.</p>
            <label class="block">
                <span class="block text-sm font-semibold text-slate-800 mb-1.5">Mot de passe actuel</span>
                <input type="password" name="mot_de_passe_actuel" autocomplete="current-password" class="{{ $champ }}">
                @error('mot_de_passe_actuel') <span class="text-rose-600 text-sm mt-1 block">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="block text-sm font-semibold text-slate-800 mb-1.5">Nouveau mot de passe</span>
                <input type="password" name="nouveau_mot_de_passe" autocomplete="new-password" aria-describedby="aide-nouveau" class="{{ $champ }}">
                <span id="aide-nouveau" class="text-xs text-slate-500 mt-1 block">8 caractères minimum, avec des lettres et des chiffres.</span>
                @error('nouveau_mot_de_passe') <span class="text-rose-600 text-sm mt-1 block">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="block text-sm font-semibold text-slate-800 mb-1.5">Confirmer le nouveau mot de passe</span>
                <input type="password" name="nouveau_mot_de_passe_confirmation" autocomplete="new-password" class="{{ $champ }}">
            </label>
        </fieldset>

        <div class="flex justify-end">
            <button type="submit" class="h-11 px-5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
