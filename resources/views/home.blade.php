@extends('layouts.app')

@section('title', request('search') ? 'Recherche « '.request('search').' »' : 'Matériel informatique à Libreville')

@section('content')
@php
    $categorieActive = request('category') ? $categories->firstWhere('id', request('category')) : null;
    $estAccueil = ! request('search') && ! $categorieActive && $produits->currentPage() === 1;
@endphp

    {{-- INTRODUCTION : la vraie boutique, et les faits qui comptent pour commander --}}
    @if($estAccueil)
        <section class="mb-8 sm:mb-10 bg-blue-700 text-white rounded-2xl overflow-hidden grid lg:grid-cols-12">
            <div class="lg:col-span-7 px-5 py-7 sm:px-10 sm:py-12 flex flex-col justify-between gap-6 sm:gap-10">
                <div class="max-w-xl">
                    <h1 class="text-[1.75rem] leading-[1.12] sm:text-5xl sm:leading-[1.05] font-extrabold tracking-tight text-balance">
                        Le matériel informatique qu'il vous faut, en stock à Libreville.
                    </h1>
                    <p class="mt-3 sm:mt-4 text-[15px] sm:text-lg text-blue-100 max-w-md">
                        Ordinateurs, imprimantes, écrans et accessoires de marques HP, Dell, Lenovo, Apple et Canon. Commandez en ligne, la boutique vous confirme tout sur WhatsApp.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-3">
                    <a href="#catalogue" class="h-12 px-6 rounded-lg bg-white text-blue-800 font-semibold flex items-center gap-2 hover:bg-blue-50 transition-colors">
                        Voir les produits
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                    </a>
                    <a href="https://wa.me/24166131270?text={{ rawurlencode('Bonjour SETUP-Gabon, je cherche :') }}" target="_blank" rel="noopener" class="font-semibold flex items-center gap-2 underline underline-offset-4 decoration-blue-300 hover:decoration-white">
                        <i class="fa-brands fa-whatsapp"></i>
                        Demander un produit
                    </a>
                </div>
            </div>
            <figure class="lg:col-span-5 relative h-48 sm:h-64 lg:h-auto order-first lg:order-none">
                <img src="{{ asset('img/boutique-setup.jpg') }}" alt="Les étagères de la boutique SETUP-Gabon à Petit Paris, Libreville" class="absolute inset-0 w-full h-full object-cover" fetchpriority="high">
                <figcaption class="absolute left-3 bottom-3 px-2.5 py-1 rounded-md bg-slate-950/70 text-white text-xs">La boutique, galerie Mouenga</figcaption>
            </figure>
        </section>
    @endif

    {{-- FAITS CLÉS --}}
    @if($estAccueil)
    <ul class="mb-8 grid grid-cols-1 sm:grid-cols-3 gap-px bg-slate-200 border border-slate-200 rounded-xl overflow-hidden text-sm">
        <li class="bg-white px-4 py-3 flex items-center gap-3">
            <i class="fa-solid fa-truck-fast w-5 text-center text-blue-700"></i>
            <span><strong class="font-semibold text-slate-900">Livraison gratuite</strong> <span class="text-slate-600">à Libreville</span></span>
        </li>
        <li class="bg-white px-4 py-3 flex items-center gap-3">
            <i class="fa-solid fa-wallet w-5 text-center text-blue-700"></i>
            <span><strong class="font-semibold text-slate-900">Espèces ou mobile money</strong> <span class="text-slate-600">rien n'est débité en ligne</span></span>
        </li>
        <li class="bg-white px-4 py-3 flex items-center gap-3">
            <i class="fa-solid fa-comment-dots w-5 text-center text-blue-700"></i>
            <span><strong class="font-semibold text-slate-900">Confirmation sur WhatsApp</strong> <span class="text-slate-600">par la boutique</span></span>
        </li>
    </ul>
    @endif

    <div id="catalogue" class="scroll-mt-28"></div>

    {{-- CATÉGORIES --}}
    @if($categories->count() > 0)
        <nav class="-mx-4 px-4 mb-6 overflow-x-auto scrollbar-none" aria-label="Catégories">
            <div class="flex items-center gap-2 min-w-max">
                <a href="{{ route('home', array_filter(['search' => request('search')])) }}#catalogue"
                   class="h-10 px-4 rounded-full text-sm font-medium flex items-center border transition-colors {{ ! $categorieActive ? 'bg-blue-700 text-white border-blue-700' : 'bg-white text-slate-700 border-slate-200 hover:border-blue-300 hover:text-blue-700' }}">
                    Tout
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('home', array_filter(['category' => $cat->id, 'search' => request('search')])) }}#catalogue"
                       class="h-10 px-4 rounded-full text-sm font-medium flex items-center border transition-colors {{ $categorieActive?->id === $cat->id ? 'bg-blue-700 text-white border-blue-700' : 'bg-white text-slate-700 border-slate-200 hover:border-blue-300 hover:text-blue-700' }}">
                        {{ $cat->nom }}
                    </a>
                @endforeach
                <a href="{{ route('categories.index') }}" class="h-10 px-4 rounded-full text-sm font-semibold flex items-center gap-1.5 text-blue-700 hover:text-blue-800">
                    Toutes les catégories <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </nav>
    @endif

    {{-- EN-TÊTE DE LISTE --}}
    <div class="mb-5 flex flex-wrap items-end justify-between gap-x-4 gap-y-1">
        <div>
            @if(request('search'))
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Résultats pour « {{ request('search') }} »</h2>
            @elseif($categorieActive)
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">{{ $categorieActive->nom }}</h2>
            @else
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Tous nos produits</h2>
            @endif
        </div>
        <p class="chiffres text-sm text-slate-500">
            {{ $produits->total() }} {{ $produits->total() > 1 ? 'produits' : 'produit' }}
            @if(request('search') || $categorieActive)
                · <a href="{{ route('home') }}#catalogue" class="text-blue-700 hover:text-blue-800 underline">Effacer les filtres</a>
            @endif
        </p>
    </div>

    {{-- GRILLE PRODUITS --}}
    @if($produits->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($produits as $produit)
                @php
                    $enStock = $produit->quantite > 0;
                    $stockFaible = $enStock && $produit->quantite <= 3;
                @endphp
                <article class="group bg-white rounded-xl border border-slate-200 hover:border-slate-300 hover:shadow-lg transition-[box-shadow,border-color] duration-200 flex flex-col overflow-hidden">
                    <a href="{{ route('produits.show', $produit->id) }}" class="flex-1 flex flex-col">
                        <div class="aspect-[4/5] bg-slate-100 relative overflow-hidden">
                            @if($produit->image)
                                <img src="{{ asset('storage/' . $produit->image) }}" alt="{{ $produit->designation }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500 ease-out">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i class="fa-solid fa-laptop text-4xl"></i>
                                </div>
                            @endif
                            @unless($enStock)
                                <span class="absolute top-2 left-2 px-2 py-1 rounded-md bg-slate-900/80 text-white text-[11px] font-semibold">Rupture</span>
                            @endunless
                        </div>

                        <div class="p-3 sm:p-4 flex-1 flex flex-col">
                            @if($produit->category)
                                <p class="text-xs text-slate-500 truncate">{{ $produit->category->nom }}</p>
                            @endif
                            <h3 class="mt-0.5 text-sm sm:text-[15px] font-semibold text-slate-900 leading-snug line-clamp-2 group-hover:text-blue-700 transition-colors">
                                {{ $produit->designation }}
                            </h3>
                            <div class="mt-auto pt-3">
                                <p class="chiffres text-base sm:text-lg font-bold text-slate-900">
                                    {{ number_format($produit->prix_unitaire, 0, ',', ' ') }} <span class="text-xs font-semibold text-slate-500">FCFA</span>
                                </p>
                                <p class="mt-1 text-xs flex items-center gap-1.5 {{ ! $enStock ? 'text-slate-500' : ($stockFaible ? 'text-amber-700' : 'text-emerald-700') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ ! $enStock ? 'bg-slate-400' : ($stockFaible ? 'bg-amber-500' : 'bg-emerald-600') }}"></span>
                                    @if(! $enStock)
                                        Bientôt de retour
                                    @elseif($stockFaible)
                                        Plus que {{ $produit->quantite }} en stock
                                    @else
                                        En stock
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>

                    <div class="px-3 pb-3 sm:px-4 sm:pb-4">
                        @if($enStock)
                            <button type="button"
                                    onclick='ajouterAuPanierBoutique({{ $produit->id }}, {{ json_encode($produit->designation) }}, {{ $produit->prix_unitaire }}, {{ $produit->quantite }})'
                                    class="w-full h-10 rounded-lg bg-blue-50 text-blue-800 hover:bg-blue-700 hover:text-white text-sm font-semibold flex items-center justify-center gap-2 transition-colors">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Ajouter<span class="hidden sm:inline"> au panier</span></span>
                            </button>
                        @else
                            <a href="https://wa.me/24166131270?text={{ rawurlencode('Bonjour, le produit « '.$produit->designation.' » sera-t-il bientôt disponible ?') }}" target="_blank" rel="noopener"
                               class="w-full h-10 rounded-lg border border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50 text-sm font-semibold flex items-center justify-center gap-2 transition-colors">
                                <i class="fa-brands fa-whatsapp"></i>
                                <span>Me prévenir</span>
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $produits->links() }}
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-xl px-6 py-14 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900">Aucun produit ne correspond</h2>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Essayez un autre mot-clé, ou demandez-nous directement : nous pouvons souvent commander le produit pour vous.
            </p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="h-11 px-5 rounded-lg border border-slate-200 text-slate-800 font-semibold text-sm flex items-center hover:bg-slate-50">Voir tous les produits</a>
                <a href="https://wa.me/24166131270?text={{ rawurlencode('Bonjour SETUP-Gabon, je cherche : '.request('search')) }}" target="_blank" rel="noopener" class="h-11 px-5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp"></i>
                    Demander sur WhatsApp
                </a>
            </div>
        </div>
    @endif

@endsection
