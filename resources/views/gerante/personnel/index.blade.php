@extends('layouts.app')

@section('title', 'Personnel')

@section('content')
@php
    $champ = 'w-full h-11 px-3 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-hidden focus:ring-4 focus:ring-blue-100 focus:border-blue-600';
    $moi = auth('staff')->user();
@endphp
<div class="space-y-6 max-w-5xl">

    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Personnel</h1>
        <p class="text-sm text-slate-500 mt-1 max-w-2xl">
            Chaque employé a son propre compte : vous savez qui a encaissé ou modifié le stock. Un vendeur accède à la caisse,
            aux commandes en ligne, au stock et au SAV, mais pas aux prix, aux statistiques ni aux factures.
        </p>
    </div>

    <section class="bg-white border border-slate-200 rounded-xl overflow-hidden" aria-labelledby="titre-membres">
        <h2 id="titre-membres" class="px-5 pt-5 font-bold text-slate-900">Comptes ({{ $membres->count() }})</h2>
        <ul class="mt-3 divide-y divide-slate-100">
            @foreach($membres as $membre)
                <li class="px-5 py-4" x-data="{ ouvert: false }">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                        <span class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm {{ $membre->estActif() ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-400' }}">
                            {{ mb_strtoupper(mb_substr($membre->nom ?: $membre->email, 0, 1)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-900 truncate">
                                @if($membre->nom)
                                    {{ $membre->nom }}
                                @elseif($membre->is($moi))
                                    <a href="{{ route('gerante.compte.edit') }}" class="text-blue-700 underline font-medium">Ajouter votre nom</a>
                                @else
                                    <span class="text-slate-500 font-medium">Nom non renseigné</span>
                                @endif
                                @if($membre->is($moi))
                                    <span class="text-xs font-normal text-slate-500">(vous)</span>
                                @endif
                            </p>
                            <p class="text-sm text-slate-500 truncate">{{ $membre->email }}</p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $membre->estGerante() ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700' }}">{{ $membre->libelleRole() }}</span>
                        @unless($membre->estActif())
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-rose-100 text-rose-800">Désactivé</span>
                        @endunless
                        <button type="button" @click="ouvert = !ouvert" :aria-expanded="ouvert" class="h-9 px-3.5 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Modifier
                        </button>
                    </div>

                    <form method="POST" action="{{ route('gerante.personnel.update', $membre) }}" x-show="ouvert" x-cloak class="mt-4 pt-4 border-t border-slate-100 grid sm:grid-cols-2 gap-4">
                        @csrf
                        @method('PUT')
                        <label class="block">
                            <span class="block text-sm font-semibold text-slate-800 mb-1.5">Rôle</span>
                            <select name="role" class="{{ $champ }}">
                                @foreach(\App\Models\Staff::ROLES as $role => $description)
                                    <option value="{{ $role }}" @selected($membre->role === $role)>{{ $description }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="block text-sm font-semibold text-slate-800 mb-1.5">Accès</span>
                            <select name="actif" class="{{ $champ }}">
                                <option value="1" @selected($membre->estActif())>Actif : peut se connecter</option>
                                <option value="0" @selected(! $membre->estActif())>Désactivé : ne peut plus se connecter</option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="block text-sm font-semibold text-slate-800 mb-1.5">Nouveau mot de passe <span class="font-normal text-slate-500">(facultatif)</span></span>
                            <input type="password" name="mot_de_passe" autocomplete="new-password" class="{{ $champ }}">
                        </label>
                        <label class="block">
                            <span class="block text-sm font-semibold text-slate-800 mb-1.5">Confirmer le mot de passe</span>
                            <input type="password" name="mot_de_passe_confirmation" autocomplete="new-password" class="{{ $champ }}">
                        </label>
                        <div class="sm:col-span-2 flex justify-end">
                            <button type="submit" class="h-11 px-5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold">Enregistrer</button>
                        </div>
                    </form>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="bg-white border border-slate-200 rounded-xl p-5" aria-labelledby="titre-nouveau">
        <h2 id="titre-nouveau" class="font-bold text-slate-900">Ajouter un compte</h2>
        <form method="POST" action="{{ route('gerante.personnel.store') }}" class="mt-4 grid sm:grid-cols-2 gap-4">
            @csrf
            <label class="block">
                <span class="block text-sm font-semibold text-slate-800 mb-1.5">Nom</span>
                <input type="text" name="nom" required maxlength="120" value="{{ old('nom') }}" class="{{ $champ }}">
                @error('nom') <span class="text-rose-600 text-sm mt-1 block">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="block text-sm font-semibold text-slate-800 mb-1.5">E-mail (identifiant de connexion)</span>
                <input type="email" name="email" required maxlength="255" value="{{ old('email') }}" autocomplete="off" class="{{ $champ }}">
                @error('email') <span class="text-rose-600 text-sm mt-1 block">{{ $message }}</span> @enderror
            </label>
            <label class="block sm:col-span-2">
                <span class="block text-sm font-semibold text-slate-800 mb-1.5">Rôle</span>
                <select name="role" class="{{ $champ }}">
                    @foreach(\App\Models\Staff::ROLES as $role => $description)
                        <option value="{{ $role }}" @selected(old('role', 'vendeur') === $role)>{{ $description }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="block text-sm font-semibold text-slate-800 mb-1.5">Mot de passe</span>
                <input type="password" name="mot_de_passe" required autocomplete="new-password" aria-describedby="aide-mdp" class="{{ $champ }}">
                <span id="aide-mdp" class="text-xs text-slate-500 mt-1 block">8 caractères minimum, avec des lettres et des chiffres.</span>
                @error('mot_de_passe') <span class="text-rose-600 text-sm mt-1 block">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="block text-sm font-semibold text-slate-800 mb-1.5">Confirmer le mot de passe</span>
                <input type="password" name="mot_de_passe_confirmation" required autocomplete="new-password" class="{{ $champ }}">
            </label>
            <div class="sm:col-span-2 flex justify-end">
                <button type="submit" class="h-11 px-5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold">Créer le compte</button>
            </div>
        </form>
    </section>
</div>
@endsection
