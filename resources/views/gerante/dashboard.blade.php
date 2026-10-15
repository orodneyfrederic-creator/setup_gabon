@extends('layouts.app')

@section('content')

    {{-- EN-TÊTE DU DASHBOARD --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-gauge-high text-blue-700"></i>
                <span>Tableau de bord</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Bienvenue, <span class="font-semibold text-slate-700">{{ Auth::guard('staff')->user()?->email ?? Auth::user()?->prenom . ' ' . Auth::user()?->nom }}</span> — Espace Gérante
            </p>
        </div>
    </div>

    {{-- STATISTIQUES RAPIDES --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Carte : Clients --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $totalClients }}</p>
                <p class="text-xs text-slate-500 font-medium">Clients inscrits</p>
            </div>
        </div>

        {{-- Carte : Commandes --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-box"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $totalCommandes }}</p>
                <p class="text-xs text-slate-500 font-medium">Commandes</p>
            </div>
        </div>

        {{-- Carte : Produits --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $totalProduits }}</p>
                <p class="text-xs text-slate-500 font-medium">Produits actifs</p>
            </div>
        </div>

        {{-- Carte : Chiffre d'affaires --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ number_format($chiffreAffaires, 0, ',', ' ') }} FCFA</p>
                <p class="text-xs text-slate-500 font-medium">Chiffre d'affaires</p>
            </div>
        </div>

    </div>

    {{-- ALERTES STOCK --}}
    @if($alertesStock->count() > 0)
    <div class="mb-8 bg-red-50 border border-red-200 rounded-2xl p-5">
        <h2 class="font-bold text-red-800 flex items-center gap-2 mb-3">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Alerte stock bas ({{ $alertesStock->count() }} produits)
        </h2>
        <div class="space-y-2">
            @foreach($alertesStock as $produit)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white px-4 py-3 rounded-lg border border-red-100 shadow-sm">
                <div>
                    <p class="text-sm font-semibold text-slate-800">{{ $produit->designation }}</p>
                    <p class="text-xs text-slate-500">Réf : {{ $produit->reference }}</p>
                </div>
                <div class="flex items-center justify-between sm:justify-end gap-3 pt-1 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-md">
                        En stock : {{ $produit->quantite }}
                    </span>
                    <a href="{{ route('gerante.stock.index') }}" class="text-xs sm:text-sm text-red-600 hover:text-red-800 font-medium underline">
                        Réapprovisionner
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- SECTIONS DE GESTION --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Gestion des produits --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-violet-600"></i>
                    Gestion des produits
                </h2>
            </div>
            <div class="p-5 space-y-3 flex-1">
                <a href="{{ route('gerante.produits.create') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-slate-100">
                    <div class="w-9 h-9 bg-violet-50 text-violet-600 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-plus text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-violet-700 transition-colors">Ajouter un produit</p>
                        <p class="text-xs text-slate-400">Créer une nouvelle fiche produit</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                </a>
                <a href="{{ route('gerante.produits.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-slate-100">
                    <div class="w-9 h-9 bg-violet-50 text-violet-600 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-list text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-violet-700 transition-colors">Voir tous les produits</p>
                        <p class="text-xs text-slate-400">Modifier, activer ou supprimer</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                </a>
                <a href="{{ route('gerante.stock.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-slate-100">
                    <div class="w-9 h-9 bg-violet-50 text-violet-600 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-boxes-packing text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-violet-700 transition-colors">Gestion du stock</p>
                        <p class="text-xs text-slate-400">Entrées, sorties et ajustements</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                </a>
                <a href="{{ route('gerante.categories.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-slate-100">
                    <div class="w-9 h-9 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-tags text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-amber-700 transition-colors">Catégories</p>
                        <p class="text-xs text-slate-400">Gérer les catégories</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                </a>
                <a href="{{ route('gerante.marques.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-slate-100">
                    <div class="w-9 h-9 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-copyright text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-indigo-700 transition-colors">Marques</p>
                        <p class="text-xs text-slate-400">Gérer les marques</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                </a>
            </div>
        </div>

        {{-- Gestion des clients & facturation --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-users text-blue-600"></i>
                    Clients & Documents
                </h2>
            </div>
            <div class="p-5 space-y-3 flex-1">
                <a href="{{ route('gerante.clients.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-slate-100">
                    <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-users text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-blue-700 transition-colors">Liste des clients</p>
                        <p class="text-xs text-slate-400">Consulter les comptes inscrits</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                </a>
                <a href="{{ route('gerante.devis.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-slate-100">
                    <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file-invoice text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-blue-700 transition-colors">Devis & Factures</p>
                        <p class="text-xs text-slate-400">Créer devis et gérer factures</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                </a>

            </div>
        </div>

        {{-- Caisse & Outils --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-cash-register text-emerald-600"></i>
                    Ventes & Analytics
                </h2>
            </div>
            <div class="p-5 space-y-3 flex-1">
                <a href="{{ route('gerante.pos.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-slate-100">
                    <div class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-cash-register text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-emerald-700 transition-colors">Caisse (POS)</p>
                        <p class="text-xs text-slate-400">Enregistrer une vente en boutique</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                </a>
                <a href="{{ route('gerante.analytics.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-slate-100">
                    <div class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-chart-line text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-emerald-700 transition-colors">Analytics Avancés</p>
                        <p class="text-xs text-slate-400">Graphiques et statistiques</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                </a>
            </div>
        </div>

        {{-- Accès rapides --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-rose-500"></i>
                    Accès rapides
                </h2>
            </div>
            <div class="p-5 space-y-3 flex-1">
                <a href="{{ route('home') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group border border-slate-100">
                    <div class="w-9 h-9 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-shop text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 group-hover:text-rose-600 transition-colors">Voir la boutique</p>
                        <p class="text-xs text-slate-400">Vue client de la boutique</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-rose-50 transition-colors group border border-slate-100 text-left">
                        <div class="w-9 h-9 bg-rose-50 text-rose-500 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 group-hover:text-rose-600 transition-colors">Se déconnecter</p>
                            <p class="text-xs text-slate-400">Fermer la session</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-300 text-xs ml-auto"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>

@endsection

