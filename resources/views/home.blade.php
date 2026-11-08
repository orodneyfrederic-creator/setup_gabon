@extends('layouts.app')

@section('title', request('search') ? 'Recherche « '.request('search').' »' : 'Matériel informatique à Libreville')

@if(! request('search') && ! request('category'))
    @push('donnees_structurees')
        {{-- « @@context » : le double @ empêche Blade de le prendre pour sa directive @@context (Laravel 13) --}}
        <script type="application/ld+json">
            {!! json_encode([
                '@@context' => 'https://schema.org',
                '@type' => 'Store',
                'name' => 'SETUP-Gabon',
                'url' => route('home'),
                'image' => asset('img/partage-setup.jpg'),
                'logo' => asset('img/logo.png'),
                'telephone' => config('boutique.telephone'),
                'email' => config('boutique.email'),
                'priceRange' => 'FCFA',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Petit Paris, galerie Mouenga',
                    'addressLocality' => 'Libreville',
                    'addressCountry' => 'GA',
                ],
                'openingHoursSpecification' => [[
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    'opens' => '07:30',
                    'closes' => '20:00',
                ]],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}
        </script>
    @endpush
@endif

@section('content')
@php
    $categorieActive = request('category') ? $categories->firstWhere('id', request('category')) : null;
    $marqueActive = request('marque') ? $marques->firstWhere('id', (int) request('marque')) : null;
    $filtresActifs = array_filter([
        'marque' => $marqueActive ? 'Marque : '.$marqueActive->nom : null,
        'prix_min' => $prixMin !== null ? 'À partir de '.number_format($prixMin, 0, ',', ' ').' FCFA' : null,
        'prix_max' => $prixMax !== null ? 'Jusqu\'à '.number_format($prixMax, 0, ',', ' ').' FCFA' : null,
        'en_stock' => request()->boolean('en_stock') ? 'En stock uniquement' : null,
    ]);
    $triActif = array_key_exists((string) request('tri'), $tris) ? request('tri') : 'recent';
    $estAccueil = ! request('search') && ! $categorieActive && $filtresActifs === [] && $triActif === 'recent' && $produits->currentPage() === 1;
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
    <div class="mb-4 flex flex-wrap items-end justify-between gap-x-4 gap-y-1">
        @php
            $titreListe = request('search') ? 'Résultats pour « '.request('search').' »' : ($categorieActive?->nom ?? 'Tous nos produits');
        @endphp
        @if($estAccueil)
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">{{ $titreListe }}</h2>
        @else
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">{{ $titreListe }}</h1>
        @endif
        <p class="chiffres text-sm text-slate-500" aria-live="polite">
            {{ $produits->total() }} {{ $produits->total() > 1 ? 'produits' : 'produit' }}
            @if(request('search') || $categorieActive || $filtresActifs !== [])
                · <a href="{{ route('home') }}#catalogue" class="text-blue-700 hover:text-blue-800 underline">Tout effacer</a>
            @endif
        </p>
    </div>

    {{-- FILTRES ET TRI --}}
    <form method="GET" action="{{ route('home') }}#catalogue" class="mb-6" x-data="{ ouvert: {{ $filtresActifs !== [] ? 'true' : 'false' }} }" aria-label="Filtrer et trier les produits">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if($categorieActive)
            <input type="hidden" name="category" value="{{ $categorieActive->id }}">
        @endif

        <div class="flex items-center justify-between gap-3">
            <button type="button" @click="ouvert = !ouvert" :aria-expanded="ouvert" aria-controls="panneau-filtres"
                    class="h-10 px-4 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-800 hover:border-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-xs text-slate-500"></i>
                Filtrer
                @if(count($filtresActifs) > 0)
                    <span class="chiffres min-w-[1.25rem] h-5 px-1 rounded-full bg-blue-700 text-white text-[11px] font-bold flex items-center justify-center">{{ count($filtresActifs) }}</span>
                @endif
            </button>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <span class="hidden sm:inline">Trier par</span>
                <select name="tri" onchange="this.form.submit()" aria-label="Trier par"
                        class="h-10 pl-3 pr-8 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-800 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-hidden">
                    @foreach($tris as $cle => $libelle)
                        <option value="{{ $cle }}" @selected($triActif === $cle)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div id="panneau-filtres" x-show="ouvert" x-cloak x-collapse>
            <div class="mt-3 bg-white border border-slate-200 rounded-xl p-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">
                <label class="block">
                    <span class="block text-sm font-semibold text-slate-800 mb-1.5">Marque</span>
                    <select name="marque" class="w-full h-11 pl-3 pr-8 rounded-lg border border-slate-300 bg-white text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-hidden">
                        <option value="">Toutes les marques</option>
                        @foreach($marques as $marque)
                            <option value="{{ $marque->id }}" @selected($marqueActive?->id === $marque->id)>{{ $marque->nom }}</option>
                        @endforeach
                    </select>
                </label>
                <fieldset>
                    <legend class="block text-sm font-semibold text-slate-800 mb-1.5">Prix (FCFA)</legend>
                    <div class="flex items-center gap-2">
                        <input type="number" name="prix_min" value="{{ $prixMin }}" min="0" step="1000" inputmode="numeric" placeholder="Min." aria-label="Prix minimum en FCFA"
                               class="chiffres w-full h-11 px-3 rounded-lg border border-slate-300 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-hidden">
                        <span class="text-slate-400" aria-hidden="true">–</span>
                        <input type="number" name="prix_max" value="{{ $prixMax }}" min="0" step="1000" inputmode="numeric" placeholder="Max." aria-label="Prix maximum en FCFA"
                               class="chiffres w-full h-11 px-3 rounded-lg border border-slate-300 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-hidden">
                    </div>
                </fieldset>
                <label class="flex items-center gap-3 h-11 cursor-pointer">
                    <input type="checkbox" name="en_stock" value="1" @checked(request()->boolean('en_stock')) class="w-5 h-5 rounded-sm border-slate-300 text-blue-700 focus:ring-blue-600">
                    <span class="text-sm font-medium text-slate-800">En stock uniquement</span>
                </label>
                <button type="submit" class="h-11 px-5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold">
                    Appliquer
                </button>
            </div>
        </div>

        @if($filtresActifs !== [])
            <ul class="mt-3 flex flex-wrap gap-2" aria-label="Filtres actifs">
                @foreach($filtresActifs as $cle => $libelle)
                    <li>
                        <a href="{{ route('home', request()->except([$cle, 'page'])) }}#catalogue"
                           class="h-8 pl-3 pr-2 rounded-full bg-blue-50 text-blue-800 text-sm font-medium inline-flex items-center gap-1.5 hover:bg-blue-100"
                           aria-label="Retirer le filtre {{ $libelle }}">
                            {{ $libelle }}
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </form>

    {{-- GRILLE PRODUITS --}}
    @if($produits->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($produits as $produit)
                @include('produits.partials.carte', ['produit' => $produit])
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
