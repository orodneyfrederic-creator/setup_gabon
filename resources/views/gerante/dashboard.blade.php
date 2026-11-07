@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
@php
    $nbATraiter = $commandesEnAttente + $negociationsEnAttente + $alertesStock->count();
@endphp

    {{-- EN-TÊTE --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Tableau de bord</h1>
            <p class="chiffres mt-1 text-slate-500">
                {{ number_format($chiffreAffaires, 0, ',', ' ') }} FCFA de chiffre d'affaires sur {{ $periodeJours }} jours ·
                <a href="{{ route('gerante.analytics.index') }}" class="font-semibold text-blue-700 hover:underline">voir les statistiques</a>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('gerante.produits.create') }}" class="h-11 px-4 rounded-lg border border-slate-300 bg-white text-slate-800 hover:bg-slate-50 text-sm font-semibold flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i> Ajouter un produit
            </a>
            <a href="{{ route('gerante.pos.index') }}" class="h-11 px-4 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold flex items-center gap-2">
                <i class="fa-solid fa-cash-register text-xs"></i> Nouvelle vente
            </a>
        </div>
    </div>

    {{-- À TRAITER --}}
    <section class="mb-8" aria-labelledby="titre-a-traiter">
        <h2 id="titre-a-traiter" class="text-sm font-semibold text-slate-900 mb-3">
            À traiter <span class="font-normal text-slate-500 first-letter:uppercase">· {{ now()->translatedFormat('l d F') }}</span>
            @if($nbATraiter === 0)
                <span class="font-normal text-slate-500">· tout est à jour</span>
            @endif
        </h2>
        <div class="grid sm:grid-cols-3 gap-3">
            <a href="{{ route('gerante.commandes.index', ['statut' => 'en_attente']) }}" class="group bg-white border rounded-xl p-4 flex items-center gap-4 transition-colors {{ $commandesEnAttente ? 'border-amber-300 hover:border-amber-400' : 'border-slate-200 hover:border-slate-300' }}">
                <span class="w-10 h-10 rounded-lg flex items-center justify-center {{ $commandesEnAttente ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-400' }}"><i class="fa-solid fa-bag-shopping"></i></span>
                <span class="flex-1">
                    <span class="block text-sm font-semibold text-slate-900">Commandes en ligne</span>
                    <span class="chiffres block text-sm {{ $commandesEnAttente ? 'text-amber-800' : 'text-slate-500' }}">{{ $commandesEnAttente }} en attente</span>
                </span>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
            </a>
            <a href="{{ route('gerante.negociations.index', ['statut' => 'en_attente_gerante']) }}" class="group bg-white border rounded-xl p-4 flex items-center gap-4 transition-colors {{ $negociationsEnAttente ? 'border-rose-300 hover:border-rose-400' : 'border-slate-200 hover:border-slate-300' }}">
                <span class="w-10 h-10 rounded-lg flex items-center justify-center {{ $negociationsEnAttente ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-400' }}"><i class="fa-solid fa-handshake-simple"></i></span>
                <span class="flex-1">
                    <span class="block text-sm font-semibold text-slate-900">Négociations</span>
                    <span class="chiffres block text-sm {{ $negociationsEnAttente ? 'text-rose-700' : 'text-slate-500' }}">{{ $negociationsEnAttente }} à décider</span>
                </span>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
            </a>
            <a href="#stock-bas" class="group bg-white border rounded-xl p-4 flex items-center gap-4 transition-colors {{ $alertesStock->count() ? 'border-amber-300 hover:border-amber-400' : 'border-slate-200 hover:border-slate-300' }}">
                <span class="w-10 h-10 rounded-lg flex items-center justify-center {{ $alertesStock->count() ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-400' }}"><i class="fa-solid fa-boxes-stacked"></i></span>
                <span class="flex-1">
                    <span class="block text-sm font-semibold text-slate-900">Stock bas</span>
                    <span class="chiffres block text-sm {{ $alertesStock->count() ? 'text-amber-800' : 'text-slate-500' }}">{{ $alertesStock->count() }} {{ $alertesStock->count() > 1 ? 'produits' : 'produit' }} sous le seuil</span>
                </span>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
            </a>
        </div>
    </section>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- DERNIÈRES COMMANDES --}}
        <section class="bg-white border border-slate-200 rounded-xl">
            <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                <h2 class="font-semibold text-slate-900">Dernières commandes en ligne</h2>
                <a href="{{ route('gerante.commandes.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">Tout voir</a>
            </div>
            @forelse($dernieresCommandes as $cmd)
                <a href="{{ route('gerante.commandes.show', $cmd) }}" class="px-5 py-3 flex items-center gap-4 border-t border-slate-100 hover:bg-slate-50">
                    <span class="chiffres text-sm font-semibold text-slate-900 w-12">#{{ $cmd->id }}</span>
                    <span class="flex-1 min-w-0 text-sm text-slate-700 truncate">{{ $cmd->user?->prenom }} {{ $cmd->user?->nom }}</span>
                    @include('account.partials.statut', ['statut' => $cmd->statut])
                    <span class="chiffres text-sm font-semibold text-slate-900 w-32 text-right">{{ number_format($cmd->montant_c, 0, ',', ' ') }} FCFA</span>
                </a>
            @empty
                <p class="px-5 py-8 border-t border-slate-100 text-sm text-slate-500 text-center">Aucune commande en ligne pour le moment.</p>
            @endforelse
        </section>

        {{-- STOCK BAS --}}
        <section id="stock-bas" class="bg-white border border-slate-200 rounded-xl scroll-mt-24">
            <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                <h2 class="font-semibold text-slate-900">Stock bas</h2>
                <a href="{{ route('gerante.stock.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">Gérer le stock</a>
            </div>
            @forelse($alertesStock as $produit)
                <div class="px-5 py-3 flex items-center gap-4 border-t border-slate-100">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 truncate">{{ $produit->designation }}</p>
                        <p class="text-xs text-slate-500">Réf. {{ $produit->reference }}</p>
                    </div>
                    <span class="chiffres text-sm font-semibold {{ $produit->quantite === 0 ? 'text-rose-700' : 'text-amber-800' }}">
                        {{ $produit->quantite === 0 ? 'Épuisé' : $produit->quantite.' restant'.($produit->quantite > 1 ? 's' : '') }}
                    </span>
                    <a href="{{ route('gerante.stock.index') }}" class="text-sm font-semibold text-blue-700 hover:underline shrink-0">Réapprovisionner</a>
                </div>
            @empty
                <p class="px-5 py-8 border-t border-slate-100 text-sm text-slate-500 text-center">Aucun produit sous son seuil d'alerte.</p>
            @endforelse
        </section>
    </div>

@endsection
