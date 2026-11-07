<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#283888">
    <title>@yield('title', 'Matériel informatique à Libreville') | SETUP-Gabon</title>
    <meta name="description" content="@yield('meta_description', 'Ordinateurs, imprimantes et accessoires informatiques en stock à Libreville. Commandez en ligne et finalisez sur WhatsApp avec SETUP-Gabon.')">
    @php
        $titrePartage = trim(View::yieldContent('title', 'Matériel informatique à Libreville')).' | SETUP-Gabon';
        $descriptionPartage = trim(View::yieldContent('meta_description', 'Ordinateurs, imprimantes et accessoires informatiques en stock à Libreville. Commandez en ligne et finalisez sur WhatsApp avec SETUP-Gabon.'));
        $urlCanonique = trim(View::yieldContent('url_canonique', url()->current()));
    @endphp
    <link rel="canonical" href="{{ $urlCanonique }}">
    {{-- Aperçu de partage (WhatsApp, Facebook, X) --}}
    <meta property="og:site_name" content="SETUP-Gabon">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $titrePartage }}">
    <meta property="og:description" content="{{ $descriptionPartage }}">
    <meta property="og:url" content="{{ $urlCanonique }}">
    <meta property="og:image" content="@yield('og_image', asset('img/partage-setup.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="@yield('og_image_alt', 'SETUP-Gabon, boutique informatique à Libreville')">
    <meta name="twitter:card" content="summary_large_image">
    @stack('donnees_structurees')
    @include('layouts.partials.theme')
    <!-- Alpine.js Plugins & Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <!-- FontAwesome 6 Free CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 leading-normal min-h-screen flex flex-col">

    @php
        $estGerante = Auth::guard('staff')->check() && Auth::guard('staff')->user()->role === 'gerante';
    @endphp

    @unless($estGerante)
    <!-- BANDEAU INFOS PRATIQUES -->
    <div class="bg-blue-700 text-blue-50 text-[12px]">
        <div class="max-w-7xl mx-auto px-4 h-8 flex items-center justify-between gap-4">
            <p class="hidden sm:flex items-center gap-2 truncate">
                <i class="fa-solid fa-location-dot text-blue-200 text-[11px]"></i>
                <span>Boutique à Petit Paris, galerie Mouenga, Libreville</span>
                <span class="text-blue-300" aria-hidden="true">·</span>
                <span>Mar. – dim. 07h30 – 20h00</span>
            </p>
            <a href="https://wa.me/24166131270" target="_blank" rel="noopener" class="flex items-center gap-1.5 font-semibold hover:text-white transition-colors mx-auto sm:mx-0">
                <i class="fa-brands fa-whatsapp text-[13px] text-amber-300"></i>
                <span class="chiffres">+241 66 13 12 70</span>
            </a>
        </div>
    </div>
    @endunless

    <!-- EN-TÊTE -->
    <header class="bg-white text-slate-900 sticky top-0 z-40 border-b border-slate-200">
        <div class="{{ $estGerante ? 'px-4 lg:px-6' : 'max-w-7xl mx-auto px-4' }} h-16 flex items-center gap-3 sm:gap-6">

            <div class="flex items-center gap-1 sm:gap-3 shrink-0">
                <button id="openSidebar" class="-ml-2 w-10 h-10 rounded-lg flex items-center justify-center text-slate-600 hover:text-blue-700 hover:bg-blue-50 transition-colors {{ $estGerante ? 'lg:hidden' : '' }}" aria-label="Ouvrir le menu">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <a href="{{ route('home') }}" class="flex items-center" aria-label="SETUP-Gabon, accueil">
                    <img src="{{ asset('img/logo.png') }}" alt="SETUP-Gabon" class="h-9 sm:h-10 w-auto object-contain">
                </a>
            </div>

            @if($estGerante)
                <span class="hidden sm:inline-flex items-center h-7 px-2.5 rounded-md bg-blue-50 text-blue-800 text-xs font-semibold">Espace gérante</span>
            @else
            <!-- RECHERCHE (bureau) -->
            <form action="{{ route('home') }}" method="GET" role="search" class="hidden md:flex flex-1 max-w-xl">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <label for="recherche-entete" class="sr-only">Rechercher un produit</label>
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <input id="recherche-entete" type="search" name="search" value="{{ request('search') }}"
                           placeholder="Ordinateur, imprimante, écran…"
                           class="w-full h-11 pl-10 pr-28 bg-slate-100 border border-transparent rounded-xl text-sm text-slate-900 placeholder-slate-500 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none transition">
                    <button type="submit" class="absolute right-1.5 top-1.5 h-8 px-4 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold transition-colors">
                        Rechercher
                    </button>
                </div>
            </form>

            <nav class="hidden lg:flex items-center gap-1 text-sm font-medium" aria-label="Navigation principale">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('home') ? 'text-blue-700' : 'text-slate-600 hover:text-blue-700' }}">Boutique</a>
                <a href="{{ route('categories.index') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'text-blue-700' : 'text-slate-600 hover:text-blue-700' }}">Catégories</a>
                <a href="{{ route('about') }}" class="px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('about') ? 'text-blue-700' : 'text-slate-600 hover:text-blue-700' }}">À propos</a>
            </nav>
            @endif

            <div class="ml-auto flex items-center gap-1 sm:gap-2">
                @if(Auth::guard('staff')->check() || Auth::check())
                    @php
                        $currentUser = Auth::guard('staff')->user() ?? Auth::user();
                        $userDisplayName = $currentUser->prenom ?? $currentUser->email;
                        $userFullName = isset($currentUser->prenom) ? ($currentUser->prenom . ' ' . $currentUser->nom) : $currentUser->email;
                        $initiale = mb_strtoupper(mb_substr($currentUser->prenom ?? $currentUser->email, 0, 1));
                    @endphp
                    <div class="relative" id="userMenuWrapper">
                        <button id="userMenuBtn" onclick="toggleUserMenu()" class="h-10 flex items-center gap-2 pl-1 pr-2 rounded-lg hover:bg-slate-100 transition-colors" aria-haspopup="true" aria-label="Mon compte">
                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-700 border border-blue-100 flex items-center justify-center font-bold text-sm">{{ $initiale }}</span>
                            <span class="hidden md:block text-sm font-semibold text-slate-700 max-w-[9rem] truncate">{{ $userDisplayName }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform" id="userMenuChevron"></i>
                        </button>

                        <div id="userMenu" class="absolute right-0 mt-2 w-60 bg-white text-slate-800 rounded-xl shadow-xl border border-slate-200 py-1.5 hidden z-50">
                            <div class="px-4 py-2.5 border-b border-slate-100 mb-1">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $userFullName }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $currentUser->email }}</p>
                            </div>
                            @if($estGerante)
                                <a href="{{ route('gerante.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-700 transition-colors">
                                    <i class="fa-solid fa-gauge-high w-4 text-slate-400"></i>
                                    <span>Espace gérante</span>
                                </a>
                            @else
                                <a href="{{ route('account.profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-700 transition-colors">
                                    <i class="fa-solid fa-user w-4 text-slate-400"></i>
                                    <span>Mon compte</span>
                                </a>
                                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-700 transition-colors">
                                    <i class="fa-solid fa-box-archive w-4 text-slate-400"></i>
                                    <span>Mes commandes</span>
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 mt-1 pt-1">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-slate-600 hover:bg-rose-50 hover:text-rose-700 transition-colors">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-slate-400"></i>
                                    <span>Se déconnecter</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="h-10 px-3 rounded-lg flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-blue-700 hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-user"></i>
                        <span class="hidden sm:inline">Se connecter</span>
                    </a>
                @endif

                @unless(Auth::guard('staff')->check())
                <!-- PANIER CLIENT -->
                <div class="relative" x-data="clientPanierComponent()" @keydown.escape.window="openCart = false">
                    <button @click="openCart = !openCart" class="relative h-10 px-3 sm:pr-4 rounded-lg bg-blue-700 hover:bg-blue-800 text-white flex items-center gap-2 transition-colors" :aria-expanded="openCart" aria-label="Ouvrir le panier">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="hidden sm:inline text-sm font-semibold">Panier</span>
                        <span x-show="countArticles > 0" x-cloak x-text="countArticles" class="chiffres min-w-[1.25rem] h-5 px-1 rounded-full bg-amber-400 text-blue-950 text-[11px] font-bold flex items-center justify-center"></span>
                    </button>

                    <!-- PANNEAU PANIER -->
                    <div x-show="openCart"
                         x-cloak
                         @click.outside="openCart = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed left-3 right-3 top-24 sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-[24rem] bg-white text-slate-800 rounded-xl shadow-2xl border border-slate-200 z-50 flex flex-col max-h-[calc(100vh-7rem)]">

                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                            <h3 class="font-semibold text-sm text-slate-900">
                                Votre panier
                                <span x-show="countArticles > 0" class="chiffres text-slate-500 font-normal" x-text="'(' + countArticles + ' article' + (countArticles > 1 ? 's' : '') + ')'"></span>
                            </h3>
                            <button @click="openCart = false" class="w-8 h-8 -mr-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center" aria-label="Fermer le panier">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto px-4 divide-y divide-slate-100">
                            <template x-if="items.length === 0">
                                <div class="text-center py-10">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800">Votre panier est vide</p>
                                    <p class="text-xs text-slate-500 mt-1">Ajoutez un produit depuis la boutique.</p>
                                </div>
                            </template>

                            <template x-for="(item, index) in items" :key="item.id + (item.negociation_id ? '-' + item.negociation_id : '')">
                                <div class="py-3 flex items-start gap-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 line-clamp-2" x-text="item.designation"></p>
                                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                                            <template x-if="item.negociation_id">
                                                <span class="text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 bg-amber-100 text-amber-800 rounded">Prix négocié</span>
                                            </template>
                                            <template x-if="item.prixCatalogue && item.prixCatalogue > item.prix">
                                                <span class="chiffres text-xs text-slate-400 line-through" x-text="formatPrix(item.prixCatalogue)"></span>
                                            </template>
                                            <span class="chiffres text-sm font-semibold text-slate-900" x-text="formatPrix(item.prix) + ' FCFA'"></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-2 shrink-0">
                                        <div class="flex items-center rounded-lg border border-slate-200">
                                            <button @click="modifierQte(item.id, -1, item.negociation_id)" class="w-8 h-8 text-slate-600 hover:text-blue-700" aria-label="Diminuer la quantité"><i class="fa-solid fa-minus text-[10px]"></i></button>
                                            <span class="chiffres w-6 text-center text-sm font-semibold" x-text="item.quantite"></span>
                                            <button @click="modifierQte(item.id, 1, item.negociation_id)" class="w-8 h-8 text-slate-600 hover:text-blue-700 disabled:text-slate-300 disabled:cursor-not-allowed" :disabled="item.negociation_id" aria-label="Augmenter la quantité"><i class="fa-solid fa-plus text-[10px]"></i></button>
                                        </div>
                                        <button @click="supprimer(item.id, item.negociation_id)" class="text-xs text-slate-500 hover:text-rose-600 underline">Retirer</button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <template x-if="items.length > 0">
                            <div class="px-4 py-4 border-t border-slate-100 space-y-3 bg-slate-50 rounded-b-xl">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-sm text-slate-600">Total</span>
                                    <span class="chiffres text-lg font-bold text-slate-900" x-text="formatPrix(totalMontant) + ' FCFA'"></span>
                                </div>
                                <a href="{{ route('checkout.index') }}" class="w-full h-11 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg flex items-center justify-center gap-2 transition-colors">
                                    <span>Passer la commande</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                                <p class="text-[11px] text-slate-500 text-center leading-snug">
                                    Votre commande est enregistrée puis envoyée à la boutique sur WhatsApp. Paiement à la livraison ou par mobile money.
                                </p>
                                <button @click="if (confirm('Vider le panier ?')) { vider(); }" class="w-full text-xs text-slate-500 hover:text-rose-600">Vider le panier</button>
                            </div>
                        </template>
                    </div>
                </div>
                @endunless
            </div>
        </div>

        @unless($estGerante)
        <!-- RECHERCHE (mobile) -->
        <form action="{{ route('home') }}" method="GET" role="search" class="md:hidden px-4 pb-3">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <label for="recherche-mobile" class="sr-only">Rechercher un produit</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input id="recherche-mobile" type="search" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher un produit…"
                       class="w-full h-11 pl-10 pr-4 bg-slate-100 border border-transparent rounded-xl text-[15px] text-slate-900 placeholder-slate-500 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none transition">
            </div>
        </form>
        @endunless
    </header>

    <!-- MENU LATÉRAL -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-950/50 z-50 hidden opacity-0 transition-opacity duration-300"></div>

    <aside id="sidebar" class="fixed top-0 left-0 h-full w-[19rem] max-w-[85vw] bg-white shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 ease-out flex flex-col {{ $estGerante ? 'lg:translate-x-0 lg:top-16 lg:h-[calc(100vh-4rem)] lg:w-64 lg:shadow-none lg:border-r lg:border-slate-200 lg:z-30' : '' }}" aria-label="Menu">
        <div class="h-16 px-4 flex items-center justify-between border-b border-slate-200 shrink-0 {{ $estGerante ? 'lg:hidden' : '' }}">
            <img src="{{ asset('img/logo.png') }}" alt="SETUP-Gabon" class="h-9 w-auto object-contain">
            <button id="closeSidebar" class="w-10 h-10 -mr-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center transition-colors" aria-label="Fermer le menu">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5 text-sm" aria-label="{{ $estGerante ? 'Espace gérante' : 'Navigation' }}">
            @if($estGerante)
                @php
                    $nbCmdEnAttente = \App\Models\Commande::where('statut', 'en_attente')->count();
                    $nbNegoEnAttente = \App\Models\Negociation::where('statut', 'en_attente_gerante')->count();
                    $menuGerante = [
                        null => [
                            ['Tableau de bord', 'gerante.dashboard', 'gerante.dashboard', 'fa-solid fa-house', null],
                        ],
                        'Ventes' => [
                            ['Caisse', 'gerante.pos.index', 'gerante.pos.*', 'fa-solid fa-cash-register', null],
                            ['Commandes en ligne', 'gerante.commandes.index', 'gerante.commandes.*', 'fa-solid fa-bag-shopping', $nbCmdEnAttente ?: null],
                            ['Négociations', 'gerante.negociations.index', 'gerante.negociations.*', 'fa-solid fa-handshake-simple', $nbNegoEnAttente ?: null],
                            ['Statistiques', 'gerante.analytics.index', 'gerante.analytics.*', 'fa-solid fa-chart-line', null],
                        ],
                        'Catalogue' => [
                            ['Produits', 'gerante.produits.index', 'gerante.produits.*', 'fa-solid fa-box', null],
                            ['Stock', 'gerante.stock.index', 'gerante.stock.*', 'fa-solid fa-warehouse', null],
                            ['Catégories', 'gerante.categories.index', 'gerante.categories.*', 'fa-solid fa-tags', null],
                            ['Marques', 'gerante.marques.index', 'gerante.marques.*', 'fa-solid fa-copyright', null],
                        ],
                        'Clients & documents' => [
                            ['Clients', 'gerante.clients.index', 'gerante.clients.*', 'fa-solid fa-users', null],
                            ['Devis', 'gerante.devis.index', 'gerante.devis.*', 'fa-solid fa-file-lines', null],
                            ['Factures', 'gerante.factures.index', 'gerante.factures.*', 'fa-solid fa-file-invoice', null],
                            ['Service après-vente', 'gerante.sav.index', 'gerante.sav.*', 'fa-solid fa-screwdriver-wrench', null],
                        ],
                    ];
                @endphp

                @foreach($menuGerante as $groupe => $liens)
                    @if($groupe)
                        <p class="px-3.5 pt-5 pb-1.5 text-xs font-semibold text-slate-500">{{ $groupe }}</p>
                    @endif
                    @foreach($liens as [$libelle, $route, $motif, $icone, $badge])
                        @php $actif = request()->routeIs($motif); @endphp
                        <a href="{{ route($route) }}" @if($actif) aria-current="page" @endif
                           class="flex items-center gap-3 px-3.5 h-10 rounded-lg transition-colors {{ $actif ? 'bg-blue-50 text-blue-800 font-semibold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="{{ $icone }} w-4 text-center text-sm {{ $actif ? 'text-blue-700' : 'text-slate-400' }}"></i>
                            <span class="flex-1">{{ $libelle }}</span>
                            @if($badge)
                                <span class="chiffres min-w-[1.375rem] h-[1.375rem] px-1.5 rounded-full text-[11px] font-bold flex items-center justify-center {{ $route === 'gerante.negociations.index' ? 'bg-rose-600 text-white' : 'bg-amber-400 text-blue-950' }}">{{ $badge }}</span>
                            @endif
                        </a>
                    @endforeach
                @endforeach

                <div class="pt-4 mt-4 border-t border-slate-100 space-y-0.5">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3.5 h-10 rounded-lg text-slate-700 hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-store w-4 text-center text-sm text-slate-400"></i>
                        <span>Voir la boutique</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3.5 h-10 rounded-lg text-slate-700 hover:bg-rose-50 hover:text-rose-700 transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center text-sm text-slate-400"></i>
                            <span>Se déconnecter</span>
                        </button>
                    </form>
                </div>
            @else
                @auth
                    <div class="px-3.5 py-2 mb-2 bg-blue-50 rounded-lg">
                        <p class="text-xs font-medium text-blue-700">Espace client</p>
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</p>
                    </div>

                    <a href="{{ route('account.profile') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg hover:bg-blue-50 font-medium text-sm text-slate-700 hover:text-blue-700 transition-colors {{ request()->routeIs('account.profile') ? 'bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <i class="fa-solid fa-user-gear text-slate-400 text-sm w-4"></i>
                        <span>Mon Compte</span>
                    </a>

                    <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg hover:bg-blue-50 font-medium text-sm text-slate-700 hover:text-blue-700 transition-colors {{ request()->routeIs('orders.*') ? 'bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <i class="fa-solid fa-box-archive text-slate-400 text-sm w-4"></i>
                        <span>Mes Commandes</span>
                    </a>
                @endauth

                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg hover:bg-blue-50 font-medium text-sm text-slate-700 hover:text-blue-700 transition-colors">
                    <i class="fa-solid fa-border-all text-slate-400 text-sm w-4"></i>
                    <span>Tous les produits</span>
                </a>
                <!-- Accordéon Catégories boutique standard -->
                <div>
                    <button onclick="toggleCatMenu()" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg hover:bg-blue-50 font-medium text-sm text-slate-700 hover:text-blue-700 transition-colors">
                        <i class="fa-solid fa-layer-group text-slate-400 text-sm w-4"></i>
                        <span class="flex-1 text-left">Nos catégories</span>
                        <i id="catChevron" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                    </button>
                    <div id="catMenu" class="overflow-hidden transition-all duration-300 ease-in-out" style="max-height: 0px; opacity: 0;">
                        <div class="pl-4 mt-1 space-y-0.5 border-l-2 border-blue-100 ml-5">
                            <a href="{{ route('categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 font-semibold text-sm text-blue-600 hover:text-blue-700 transition-colors">
                                <i class="fa-solid fa-border-all text-xs"></i>
                                <span>Toutes les catégories</span>
                            </a>
                            @if(isset($categories) && $categories->count() > 0)
                                @foreach($categories as $cat)
                                    <a href="{{ route('home', ['category' => $cat->id]) }}" 
                                       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 font-medium text-sm text-slate-600 hover:text-blue-700 transition-colors">
                                        <i class="fa-solid fa-tag text-slate-300 text-xs"></i>
                                        <span>{{ $cat->nom }}</span>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('about') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg hover:bg-blue-50 font-medium text-sm text-slate-700 hover:text-blue-700 transition-colors {{ request()->routeIs('about') ? 'bg-blue-50 text-blue-700 font-semibold' : '' }}">
                    <i class="fa-solid fa-circle-info text-slate-400 text-sm w-4"></i>
                    <span>À propos</span>
                </a>

                @auth
                <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-slate-100 mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg hover:bg-rose-50 font-medium text-sm text-rose-600 hover:text-rose-700 transition-colors">
                        <i class="fa-solid fa-right-from-bracket text-slate-400 text-sm w-4"></i>
                        <span>Se déconnecter</span>
                    </button>
                </form>
                @else
                <div class="pt-3 border-t border-slate-100 mt-2 space-y-1">
                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg hover:bg-blue-50 font-medium text-sm text-slate-700 hover:text-blue-700 transition-colors">
                        <i class="fa-solid fa-right-to-bracket text-slate-400 text-sm w-4"></i>
                        <span>Se connecter</span>
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition-colors shadow">
                        <i class="fa-solid fa-user-plus text-sm"></i>
                        <span>Créer un compte</span>
                    </a>
                </div>
                @endauth
            @endif
        </nav>
    </aside>

    <!-- NOTIFICATIONS -->
    <div id="toastContainer" class="fixed top-4 inset-x-4 sm:inset-x-auto sm:right-5 sm:top-24 z-[60] flex flex-col gap-3 sm:w-96 pointer-events-none" aria-live="polite">
        @if(session('success'))
            <div id="toast-success" class="pointer-events-auto bg-white border border-slate-200 text-slate-800 pl-4 pr-2 py-3 rounded-xl shadow-xl flex items-start gap-3 -translate-y-2 opacity-0 transition-all duration-300 ease-out" role="status">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5 shrink-0"></i>
                <p class="flex-1 text-sm leading-snug pt-0.5">{{ session('success') }}</p>
                <button onclick="closeToast('toast-success')" class="w-8 h-8 shrink-0 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center" aria-label="Fermer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="toast-error" class="pointer-events-auto bg-white border border-rose-200 text-slate-800 pl-4 pr-2 py-3 rounded-xl shadow-xl flex items-start gap-3 -translate-y-2 opacity-0 transition-all duration-300 ease-out" role="alert">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg mt-0.5 shrink-0"></i>
                <p class="flex-1 text-sm leading-snug pt-0.5">{{ session('error') }}</p>
                <button onclick="closeToast('toast-error')" class="w-8 h-8 shrink-0 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center" aria-label="Fermer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div id="toast-info" class="pointer-events-auto bg-white border border-slate-200 text-slate-800 pl-4 pr-2 py-3 rounded-xl shadow-xl flex items-start gap-3 -translate-y-2 opacity-0 transition-all duration-300 ease-out" role="status">
                <i class="fa-solid fa-circle-info text-blue-600 text-lg mt-0.5 shrink-0"></i>
                <p class="flex-1 text-sm leading-snug pt-0.5">{{ session('info') }}</p>
                <button onclick="closeToast('toast-info')" class="w-8 h-8 shrink-0 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center" aria-label="Fermer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- CONTENU PRINCIPAL -->
    <main class="flex-1 w-full px-4 py-6 sm:py-8 {{ $estGerante ? 'lg:pl-72 lg:pr-8 xl:pr-10' : 'max-w-7xl mx-auto' }}">
        @yield('content')
    </main>

    <!-- SCRIPT JS INTERACTION SIDEBAR ET TOASTS -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const openBtn = document.getElementById('openSidebar');
        const closeBtn = document.getElementById('closeSidebar');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            if (overlay.classList.contains('hidden')) {
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        openBtn.addEventListener('click', toggleSidebar);
        closeBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        // MENU UTILISATEUR - TOGGLE PAR CLIC
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            const chevron = document.getElementById('userMenuChevron');
            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden', !isHidden);
            chevron.classList.toggle('rotate-180', isHidden);
        }

        // Fermer le menu si on clique en dehors
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('userMenuWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                const menu = document.getElementById('userMenu');
                const chevron = document.getElementById('userMenuChevron');
                if (menu) { menu.classList.add('hidden'); }
                if (chevron) { chevron.classList.remove('rotate-180'); }
            }
        });

        // GESTION DES TOASTS POPUP
        function closeToast(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('-translate-y-2', 'opacity-0');
                setTimeout(() => el.remove(), 300);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            ['toast-success', 'toast-error', 'toast-info'].forEach(id => {
                const toast = document.getElementById(id);
                if (toast) {
                    setTimeout(() => {
                        toast.classList.remove('-translate-y-2', 'opacity-0');
                    }, 100);

                    setTimeout(() => {
                        closeToast(id);
                    }, id === 'toast-error' ? 9000 : 5000);
                }
            });
        });
    </script>
    @unless($estGerante)
    <!-- PIED DE PAGE -->
    <footer class="mt-16 bg-white border-t border-slate-200 text-slate-600">
        <div class="h-1 flex" aria-hidden="true">
            <span class="flex-1 bg-emerald-600"></span>
            <span class="flex-1 bg-amber-400"></span>
            <span class="flex-1 bg-blue-700"></span>
        </div>
        <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-2 lg:grid-cols-12 gap-x-6 gap-y-10 text-sm">

            <div class="col-span-2 lg:col-span-4 space-y-4">
                <a href="{{ route('home') }}" aria-label="SETUP-Gabon, accueil">
                    <img src="{{ asset('img/logo.png') }}" alt="SETUP-Gabon" class="h-11 w-auto object-contain">
                </a>
                <p class="max-w-xs leading-relaxed">
                    Ordinateurs, imprimantes, écrans et accessoires informatiques, en stock à Libreville.
                </p>
                <div class="flex items-center gap-2">
                    <a href="https://www.facebook.com/setupgabon" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:text-blue-700 hover:border-blue-200 flex items-center justify-center transition-colors" aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f text-sm"></i>
                    </a>
                    <a href="https://www.linkedin.com/company/setup-gabon/" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:text-blue-700 hover:border-blue-200 flex items-center justify-center transition-colors" aria-label="LinkedIn">
                        <i class="fa-brands fa-linkedin-in text-sm"></i>
                    </a>
                    <a href="https://wa.me/24166131270" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:text-emerald-700 hover:border-emerald-200 flex items-center justify-center transition-colors" aria-label="WhatsApp">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-2">
                <h3 class="text-slate-900 font-semibold mb-3">Boutique</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="hover:text-blue-700 transition-colors">Tous les produits</a></li>
                    <li><a href="{{ route('categories.index') }}" class="hover:text-blue-700 transition-colors">Catégories</a></li>
                    <li><a href="https://wa.me/24166131270?text={{ rawurlencode('Bonjour SETUP-Gabon, je souhaite recevoir un devis pour :') }}" target="_blank" rel="noopener" class="hover:text-blue-700 transition-colors">Demander un devis</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-blue-700 transition-colors">À propos</a></li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h3 class="text-slate-900 font-semibold mb-3">Mon compte</h3>
                <ul class="space-y-2">
                    @auth
                        <li><a href="{{ route('orders.index') }}" class="hover:text-blue-700 transition-colors">Mes commandes</a></li>
                        <li><a href="{{ route('account.profile') }}" class="hover:text-blue-700 transition-colors">Mes informations</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-blue-700 transition-colors">Se connecter</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-blue-700 transition-colors">Créer un compte</a></li>
                    @endauth
                    <li><a href="{{ route('checkout.index') }}" class="hover:text-blue-700 transition-colors">Finaliser ma commande</a></li>
                </ul>
            </div>

            <div class="col-span-2 lg:col-span-4">
                <h3 class="text-slate-900 font-semibold mb-3">Nous trouver</h3>
                <ul class="space-y-2.5">
                    <li class="flex gap-3">
                        <i class="fa-solid fa-location-dot w-4 mt-1 text-blue-700"></i>
                        <span>Petit Paris, galerie Mouenga<br>Libreville, Gabon</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-clock w-4 mt-1 text-blue-700"></i>
                        <span>Du mardi au dimanche, 07h30 – 20h00</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-phone w-4 mt-1 text-blue-700"></i>
                        <a href="tel:+24166131270" class="chiffres hover:text-blue-700 transition-colors">+241 66 13 12 70</a>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-envelope w-4 mt-1 text-blue-700"></i>
                        <a href="mailto:protocolees@gmail.com" class="hover:text-blue-700 transition-colors break-all">protocolees@gmail.com</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-slate-500">
                <nav class="flex flex-wrap gap-x-4 gap-y-1" aria-label="Informations légales">
                    <span>© {{ date('Y') }} SETUP-Gabon</span>
                    <a href="{{ route('legal.cgv') }}" class="hover:text-blue-700">CGV</a>
                    <a href="{{ route('legal.retours') }}" class="hover:text-blue-700">Retours et garanties</a>
                    <a href="{{ route('legal.confidentialite') }}" class="hover:text-blue-700">Confidentialité</a>
                    <a href="{{ route('legal.mentions') }}" class="hover:text-blue-700">Mentions légales</a>
                </nav>
                <p class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span>Paiement :</span>
                    <span class="text-slate-700 font-medium">espèces</span><span aria-hidden="true">·</span>
                    <span class="text-slate-700 font-medium">Airtel Money</span><span aria-hidden="true">·</span>
                    <span class="text-slate-700 font-medium">Moov Money</span>
                </p>
            </div>
        </div>
    </footer>
    @endunless
<script>
function previewImage(input, previewId, wrapperId, iconId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            document.getElementById(wrapperId).classList.remove('hidden');
            document.getElementById(iconId).classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

var catMenuOpen = false;
function toggleCatMenu() {
    var menu = document.getElementById('catMenu');
    var chevron = document.getElementById('catChevron');
    catMenuOpen = !catMenuOpen;
    if (catMenuOpen) {
        menu.style.maxHeight = menu.scrollHeight + 'px';
        menu.style.opacity = '1';
        chevron.style.transform = 'rotate(180deg)';
    } else {
        menu.style.maxHeight = '0px';
        menu.style.opacity = '0';
        chevron.style.transform = 'rotate(0deg)';
    }
}
function clientPanierComponent() {
    return {
        openCart: false,
        items: JSON.parse(localStorage.getItem('client_panier') || '[]'),
        
        init() {
            this.$watch('items', (newItems) => {
                localStorage.setItem('client_panier', JSON.stringify(newItems));
            });
            window.addEventListener('ajouter-au-panier-client', (e) => {
                this.ajouterProduit(e.detail);
            });
        },

        get countArticles() {
            return this.items.reduce((total, i) => total + i.quantite, 0);
        },

        get totalMontant() {
            return this.items.reduce((total, i) => total + (i.prix * i.quantite), 0);
        },

        ajouterProduit(prod) {
            const index = this.items.findIndex(i => i.id === prod.id && (i.negociation_id || null) === (prod.negociation_id || null));
            if (index > -1) {
                if (prod.negociation_id) {
                    alert('Une offre négociée est verrouillée pour une quantité spécifique.');
                    return;
                }
                const qteDemandee = this.items[index].quantite + (prod.quantite || 1);
                if (prod.stockMax && qteDemandee > prod.stockMax) {
                    alert('Stock maximum atteint (' + prod.stockMax + ' disponible(s)).');
                    return;
                }
                this.items[index].quantite = qteDemandee;
            } else {
                this.items.push({
                    id: prod.id,
                    designation: prod.designation,
                    prix: prod.prix,
                    prixCatalogue: prod.prixCatalogue || prod.prix,
                    quantite: prod.quantite || 1,
                    stockMax: prod.stockMax,
                    negociation_id: prod.negociation_id || null,
                    negociation_ref: prod.negociation_ref || null
                });
            }
            localStorage.setItem('client_panier', JSON.stringify(this.items));
            this.openCart = true;
        },

        modifierQte(id, delta, negociation_id = null) {
            const index = this.items.findIndex(i => i.id === id && (i.negociation_id || null) === (negociation_id || null));
            if (index > -1) {
                if (negociation_id && delta > 0) {
                    alert('La quantité d\'un article négocié ne peut pas être augmentée au-delà de l\'accord conclu.');
                    return;
                }
                this.items[index].quantite += delta;
                if (this.items[index].quantite <= 0) {
                    this.items.splice(index, 1);
                }
                localStorage.setItem('client_panier', JSON.stringify(this.items));
            }
        },

        supprimer(id, negociation_id = null) {
            this.items = this.items.filter(i => !(i.id === id && (i.negociation_id || null) === (negociation_id || null)));
            localStorage.setItem('client_panier', JSON.stringify(this.items));
        },

        vider() {
            this.items = [];
            localStorage.setItem('client_panier', JSON.stringify([]));
        },

        formatPrix(val) {
            return new Intl.NumberFormat('fr-FR').format(val);
        },

        genererMessageWhatsApp() {
            let msg = "Bonjour SETUP GABON, je souhaite passer la commande suivante :\n\n";
            this.items.forEach(i => {
                msg += "• " + i.designation + " x" + i.quantite + " (" + this.formatPrix(i.prix * i.quantite) + " FCFA)\n";
            });
            msg += "\nTotal : " + this.formatPrix(this.totalMontant) + " FCFA";
            return msg;
        }
    }
}

function ajouterAuPanierBoutique(id, designation, prix, stockMax = 999, quantite = 1) {
    window.dispatchEvent(new CustomEvent('ajouter-au-panier-client', {
        detail: { id, designation, prix, stockMax, quantite }
    }));
}
</script>
@stack('scripts')
</body>
</html>