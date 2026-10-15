<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SETUP-GABON | Accueil</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js Plugins & Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- FontAwesome 6 Free CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body class="bg-slate-100 font-sans leading-normal tracking-normal selection:bg-blue-600 selection:text-white">

    <!-- BARRE SUPÉRIEURE (HEADER / NAVBAR BLEU FONCÉ) -->
    <header class="bg-gradient-to-r from-blue-950 via-slate-900 to-blue-950 text-white sticky top-0 z-40 shadow-xl border-b border-blue-900/60">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-2.5 sm:py-3 flex items-center justify-between gap-2 sm:gap-4">
            
            <!-- Bouton Sidebar & Logo Officiel Mis en Avant -->
            <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                <button id="openSidebar" class="p-1.5 sm:p-2 hover:bg-blue-900/80 text-blue-200 hover:text-white focus:outline-none transition-colors" title="Ouvrir le menu">
                    <i class="fa-solid fa-bars text-lg sm:text-xl"></i>
                </button>

                <!-- LOGO OFFICIEL -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-white font-bold text-lg hover:text-blue-300 transition-colors">
                    <img src="{{ asset('img/logo.png') }}" alt="SETUP-GABON" class="h-7 sm:h-9 w-auto object-contain">
                </a>
            </div>

            <!-- BARRE DE RECHERCHE -->
            <form action="{{ route('home') }}" method="GET" class="flex-1 max-w-2xl flex">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400">
                        <i class="fa-solid fa-magnifying-glass text-xs sm:text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Rechercher..." 
                           class="w-full pl-8 sm:pl-10 pr-2 sm:pr-4 py-1.5 sm:py-2 bg-blue-950/80 text-white placeholder-blue-300/60 rounded-l-xl sm:rounded-l-2xl border border-blue-900/80 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-xs sm:text-sm">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-3 sm:px-6 rounded-r-xl sm:rounded-r-2xl flex items-center justify-center transition-colors shadow-md">
                    <i class="fa-solid fa-magnifying-glass text-xs sm:text-sm"></i>
                </button>
            </form>

            <!-- NAVIGATION HEADER (ACCUEIL & À PROPOS) -->
            <nav class="hidden lg:flex items-center gap-2">
                <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-blue-200 hover:text-white hover:bg-blue-900/50 transition-colors {{ request()->routeIs('home') ? 'bg-blue-900/80 text-white border border-blue-700/50' : '' }}">
                    <i class="fa-solid fa-house mr-1 text-blue-400"></i>
                    Accueil
                </a>
                <a href="{{ route('about') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-blue-200 hover:text-white hover:bg-blue-900/50 transition-colors {{ request()->routeIs('about') ? 'bg-blue-900/80 text-white border border-blue-700/50' : '' }}">
                    <i class="fa-solid fa-circle-info mr-1 text-blue-400"></i>
                    À propos
                </a>
            </nav>

            <!-- BOUTONS ACTION (COMPTE & PANIER) -->
            <div class="flex items-center gap-2 sm:gap-3">
                @if(Auth::guard('staff')->check() || Auth::check())
                    @php
                        $currentUser = Auth::guard('staff')->user() ?? Auth::user();
                        $userDisplayName = $currentUser->prenom ?? $currentUser->email;
                        $userFullName = isset($currentUser->prenom) ? ($currentUser->prenom . ' ' . $currentUser->nom) : $currentUser->email;
                    @endphp
                    <div class="relative" id="userMenuWrapper">
                        <button id="userMenuBtn" onclick="toggleUserMenu()" class="text-sm flex items-center gap-2 py-1.5 px-3 bg-blue-900/40 hover:bg-blue-900/80 border border-blue-800/50 rounded-2xl transition-all focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="text-left hidden sm:block">
                                <span class="text-[10px] block text-blue-300 uppercase leading-none">Bonjour</span>
                                <span class="font-bold text-xs text-white flex items-center gap-1">
                                    {{ $userDisplayName }}
                                    <i class="fa-solid fa-chevron-down text-[10px] text-blue-300" id="userMenuChevron"></i>
                                </span>
                            </div>
                        </button>

                        <!-- Menu Déroulant (toggle par clic) -->
                        <div id="userMenu" class="absolute right-0 mt-1 w-56 bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-100 py-1 hidden z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-100 bg-slate-50">
                                <p class="text-xs font-bold text-slate-900">{{ $userFullName }}</p>
                                <p class="text-[10px] text-gray-500 truncate">{{ $currentUser->email }}</p>
                            </div>
                            @if(Auth::guard('staff')->check() && Auth::guard('staff')->user()->role === 'gerante')
                                <a href="{{ route('gerante.dashboard') }}" class="block px-4 py-2.5 text-xs font-semibold text-blue-600 hover:bg-blue-50 flex items-center gap-2 transition-colors border-b border-gray-100">
                                    <i class="fa-solid fa-gauge-high text-blue-500"></i>
                                    <span>Espace Gérante</span>
                                </a>
                            @else
                                <a href="{{ route('account.profile') }}" class="block px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2 transition-colors">
                                    <i class="fa-solid fa-user-gear text-slate-400"></i>
                                    <span>Mon Compte</span>
                                </a>
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2 transition-colors border-b border-gray-100">
                                    <i class="fa-solid fa-box-archive text-slate-400"></i>
                                    <span>Mes Commandes</span>
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2 transition-colors">
                                    <i class="fa-solid fa-right-from-bracket text-rose-500"></i>
                                    <span>Se déconnecter</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="py-1.5 px-3 bg-blue-900/40 hover:bg-blue-900/80 border border-blue-800/50 rounded-2xl flex items-center gap-1.5 text-white text-xs font-semibold transition-all">
                        <i class="fa-solid fa-right-to-bracket text-blue-400"></i>
                        <span>Connexion</span>
                    </a>
                    <a href="{{ route('register') }}" class="hidden sm:flex py-1.5 px-3 bg-blue-600 hover:bg-blue-500 rounded-2xl items-center gap-1.5 text-white text-xs font-semibold transition-all shadow-md">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>S'inscrire</span>
                    </a>
                @endif


                <!-- BOUTON PANIER CLIENT (INTERACTIF) -->
                <div class="relative" x-data="clientPanierComponent()">
                    <button @click="openCart = !openCart" class="relative p-2.5 bg-blue-900/40 hover:bg-blue-900/80 border border-blue-800/50 rounded-2xl flex items-center gap-2 text-white transition-all focus:outline-none">
                        <div class="relative">
                            <i class="fa-solid fa-cart-shopping text-blue-300 text-lg"></i>
                            <span x-text="countArticles" class="absolute -top-2 -right-2 bg-amber-500 text-slate-950 text-[10px] font-black rounded-full w-4 h-4 flex items-center justify-center">0</span>
                        </div>
                        <span class="font-bold text-xs hidden md:inline">Panier</span>
                    </button>

                    <!-- DRAWER / POPUP PANIER CLIENT -->
                    <div x-show="openCart" 
                         x-cloak 
                         @click.outside="openCart = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-100 p-4 z-50">
                        
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-cart-shopping text-blue-600"></i>
                                Votre Panier
                            </h3>
                            <button @click="openCart = false" class="text-slate-400 hover:text-slate-600 text-xs">
                                <i class="fa-solid fa-xmark text-base"></i>
                            </button>
                        </div>

                        <!-- LISTE DES ARTICLES DANS LE PANIER CLIENT -->
                        <div class="py-2 max-h-64 overflow-y-auto divide-y divide-slate-100">
                            <template x-if="items.length === 0">
                                <div class="text-center py-6 text-slate-400 text-xs">
                                    <i class="fa-solid fa-cart-shopping text-3xl mb-2 opacity-30 block"></i>
                                    Votre panier est vide.
                                </div>
                            </template>

                            <template x-for="(item, index) in items" :key="item.id">
                                <div class="py-2.5 flex items-center justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate" x-text="item.designation"></p>
                                        <p class="text-[11px] text-slate-400" x-text="formatPrix(item.prix) + ' FCFA x ' + item.quantite"></p>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button @click="modifierQte(item.id, -1)" class="w-6 h-6 rounded bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200">-</button>
                                        <span class="text-xs font-bold text-slate-700 w-4 text-center" x-text="item.quantite"></span>
                                        <button @click="modifierQte(item.id, 1)" class="w-6 h-6 rounded bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200">+</button>
                                        <button @click="supprimer(item.id)" class="text-rose-500 hover:text-rose-700 text-xs ml-1">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- TOTAL ET ACTIONS -->
                        <template x-if="items.length > 0">
                            <div class="pt-3 border-t border-slate-100 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-600">Total :</span>
                                    <span class="font-black text-sm text-blue-700" x-text="formatPrix(totalMontant) + ' FCFA'"></span>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('checkout.index') }}" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 shadow-md">
                                        <i class="fa-solid fa-credit-card"></i>
                                        <span>Valider ma commande</span>
                                    </a>
                                    <div class="flex gap-2">
                                        <button @click="vider()" class="w-1/3 py-2 border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50">
                                            Vider
                                        </button>
                                        @if(Auth::check() || Auth::guard('staff')->check())
                                            <a :href="'https://wa.me/24166131270?text=' + encodeURIComponent(genererMessageWhatsApp())" target="_blank" class="w-2/3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 shadow-sm">
                                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                                <span>WhatsApp</span>
                                            </a>
                                        @else
                                            <a href="{{ route('checkout.index') }}" class="w-2/3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 shadow-sm">
                                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                                <span>WhatsApp</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR RÉTRACTABLE (MENU LATÉRAL BLEU FONCÉ) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300"></div>
    
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <div class="bg-blue-950 text-white p-4 flex items-center justify-between border-b border-blue-900">
            <h2 class="text-base font-bold flex items-center gap-2 text-blue-200">
                <i class="fa-solid fa-layer-group text-blue-400"></i>
                <span>Toutes les Catégories</span>
            </h2>
            <button id="closeSidebar" class="p-1 text-blue-300 hover:text-white transition-colors text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            @if(Auth::guard('staff')->check() && Auth::guard('staff')->user()->role === 'gerante')
                <!-- DASHBOARD GÉRANTE -->
                <a href="{{ route('gerante.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-blue-50 font-bold text-xs text-blue-950 hover:text-blue-700 transition-colors {{ request()->routeIs('gerante.dashboard') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <i class="fa-solid fa-gauge-high text-blue-600 text-sm"></i>
                    <span>Dashboard</span>
                </a>

                <!-- SECTION : GESTION DES PRODUITS -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-slate-100 font-semibold text-xs text-slate-700 transition-colors">
                        <i class="fa-solid fa-boxes-stacked text-violet-600 text-sm"></i>
                        <span class="flex-1 text-left">Gestion des produits</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-slate-100 ml-5">
                        <a href="{{ route('gerante.produits.create') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-plus text-[10px] text-slate-400"></i>
                            <span>Ajouter un produit</span>
                        </a>
                        <a href="{{ route('gerante.produits.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-list text-[10px] text-slate-400"></i>
                            <span>Voir tous les produits</span>
                        </a>
                        <a href="{{ route('gerante.stock.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-boxes-packing text-[10px] text-slate-400"></i>
                            <span>Gestion du stock</span>
                        </a>
                        <a href="{{ route('gerante.categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-tags text-[10px] text-slate-400"></i>
                            <span>Catégories</span>
                        </a>
                        <a href="{{ route('categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-500 hover:text-blue-600 transition-colors pl-6">
                            <i class="fa-solid fa-layer-group text-[10px] text-slate-400"></i>
                            <span>Voir toutes les catégories</span>
                        </a>
                        <a href="{{ route('gerante.marques.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-copyright text-[10px] text-slate-400"></i>
                            <span>Marques</span>
                        </a>
                    </div>
                </div>

                <!-- SECTION : CLIENTS & DOCUMENTS -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-slate-100 font-semibold text-xs text-slate-700 transition-colors">
                        <i class="fa-solid fa-users text-blue-600 text-sm"></i>
                        <span class="flex-1 text-left">Clients & Documents</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-slate-100 ml-5">
                        <a href="{{ route('gerante.clients.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-users-line text-[10px] text-slate-400"></i>
                            <span>Liste des clients</span>
                        </a>
                        <a href="{{ route('gerante.devis.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-file-invoice text-[10px] text-slate-400"></i>
                            <span>Devis</span>
                        </a>
                        <a href="{{ route('gerante.factures.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-file-invoice-dollar text-[10px] text-slate-400"></i>
                            <span>Factures</span>
                        </a>
                    </div>
                </div>

                <!-- SECTION : VENTES & ANALYTICS -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-slate-100 font-semibold text-xs text-slate-700 transition-colors">
                        <i class="fa-solid fa-cash-register text-emerald-600 text-sm"></i>
                        <span class="flex-1 text-left">Ventes & Analytics</span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-slate-100 ml-5">
                        <a href="{{ route('gerante.pos.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-cash-register text-[10px] text-slate-400"></i>
                            <span>Caisse (POS)</span>
                        </a>
                        <a href="{{ route('gerante.analytics.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-chart-line text-[10px] text-slate-400"></i>
                            <span>Analytics</span>
                        </a>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-4 space-y-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-slate-100 font-medium text-xs text-slate-600 hover:text-blue-600 transition-colors">
                        <i class="fa-solid fa-shop text-rose-500 text-sm"></i>
                        <span>Voir la boutique</span>
                    </a>
                    <a href="{{ route('about') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-blue-50 font-medium text-xs text-slate-700 hover:text-blue-700 transition-colors {{ request()->routeIs('about') ? 'bg-blue-50 text-blue-700 font-bold' : '' }}">
                        <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
                        <span>À propos</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-rose-50 font-medium text-xs text-rose-600 hover:text-rose-700 transition-colors">
                            <i class="fa-solid fa-right-from-bracket text-rose-500 text-sm"></i>
                            <span>Se déconnecter</span>
                        </button>
                    </form>
                </div>
            @else
                @auth
                    <div class="px-3.5 py-2 mb-2 bg-blue-50/80 rounded-xl border border-blue-100">
                        <p class="text-[10px] uppercase font-bold text-blue-500">Mon Espace Client</p>
                        <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</p>
                    </div>

                    <a href="{{ route('account.profile') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-blue-50 font-medium text-xs text-slate-700 hover:text-blue-700 transition-colors {{ request()->routeIs('account.profile') ? 'bg-blue-50 text-blue-700 font-bold' : '' }}">
                        <i class="fa-solid fa-user-gear text-blue-600 text-sm"></i>
                        <span>Mon Compte</span>
                    </a>

                    <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-blue-50 font-medium text-xs text-slate-700 hover:text-blue-700 transition-colors {{ request()->routeIs('orders.*') ? 'bg-blue-50 text-blue-700 font-bold' : '' }}">
                        <i class="fa-solid fa-box-archive text-blue-600 text-sm"></i>
                        <span>Mes Commandes</span>
                    </a>
                @endauth

                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-blue-50 font-medium text-xs text-slate-700 hover:text-blue-700 transition-colors">
                    <i class="fa-solid fa-border-all text-blue-600 text-sm"></i>
                    <span>Tous les produits</span>
                </a>
                <!-- Accordéon Catégories boutique standard -->
                <div>
                    <button onclick="toggleCatMenu()" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-blue-50 font-medium text-xs text-slate-700 hover:text-blue-700 transition-colors">
                        <i class="fa-solid fa-layer-group text-blue-600 text-sm"></i>
                        <span class="flex-1 text-left">Nos catégories</span>
                        <i id="catChevron" class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-300"></i>
                    </button>
                    <div id="catMenu" class="overflow-hidden transition-all duration-300 ease-in-out" style="max-height: 0px; opacity: 0;">
                        <div class="pl-4 mt-1 space-y-0.5 border-l-2 border-blue-100 ml-5">
                            <a href="{{ route('categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 font-semibold text-xs text-blue-600 hover:text-blue-700 transition-colors">
                                <i class="fa-solid fa-border-all text-[10px]"></i>
                                <span>Toutes les catégories</span>
                            </a>
                            @if(isset($categories) && $categories->count() > 0)
                                @foreach($categories as $cat)
                                    <a href="{{ route('home', ['category' => $cat->id]) }}" 
                                       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 font-medium text-xs text-slate-600 hover:text-blue-700 transition-colors">
                                        <i class="fa-solid fa-tag text-slate-300 text-[10px]"></i>
                                        <span>{{ $cat->nom }}</span>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('about') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-blue-50 font-medium text-xs text-slate-700 hover:text-blue-700 transition-colors {{ request()->routeIs('about') ? 'bg-blue-50 text-blue-700 font-bold' : '' }}">
                    <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
                    <span>À propos</span>
                </a>

                @auth
                <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-slate-100 mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-rose-50 font-medium text-xs text-rose-600 hover:text-rose-700 transition-colors">
                        <i class="fa-solid fa-right-from-bracket text-rose-500 text-sm"></i>
                        <span>Se déconnecter</span>
                    </button>
                </form>
                @else
                <div class="pt-3 border-t border-slate-100 mt-2 space-y-1">
                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-blue-50 font-medium text-xs text-slate-700 hover:text-blue-700 transition-colors">
                        <i class="fa-solid fa-right-to-bracket text-blue-600 text-sm"></i>
                        <span>Se connecter</span>
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-xs transition-colors shadow">
                        <i class="fa-solid fa-user-plus text-sm"></i>
                        <span>Créer un compte</span>
                    </a>
                </div>
                @endauth
            @endif
        </nav>
    </aside>

    <!-- POPUP NOTIFICATIONS (TOASTS EN HAUT À DROITE) -->
    <div id="toastContainer" class="fixed top-20 right-5 z-50 flex flex-col gap-3 max-w-sm w-full px-4 pointer-events-none">
        @if(session('success'))
            <div id="toast-success" class="pointer-events-auto bg-slate-900/95 backdrop-blur-md border border-emerald-500/50 text-slate-100 p-4 rounded-2xl shadow-2xl flex items-center justify-between gap-3 transform translate-x-full opacity-0 transition-all duration-500 ease-out" role="alert">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 font-bold text-sm">
                        <i class="fa-solid fa-circle-check text-base"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-emerald-400">Succès</h4>
                        <p class="text-xs text-gray-300 mt-0.5 leading-snug">{{ session('success') }}</p>
                    </div>
                </div>
                <button onclick="closeToast('toast-success')" class="text-gray-400 hover:text-white p-1 text-base font-semibold leading-none">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="toast-error" class="pointer-events-auto bg-slate-900/95 backdrop-blur-md border border-rose-500/50 text-slate-100 p-4 rounded-2xl shadow-2xl flex items-center justify-between gap-3 transform translate-x-full opacity-0 transition-all duration-500 ease-out" role="alert">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 font-bold text-sm">
                        <i class="fa-solid fa-circle-xmark text-base"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-rose-400">Erreur</h4>
                        <p class="text-xs text-gray-300 mt-0.5 leading-snug">{{ session('error') }}</p>
                    </div>
                </div>
                <button onclick="closeToast('toast-error')" class="text-gray-400 hover:text-white p-1 text-base font-semibold leading-none">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- CONTENU PRINCIPAL -->
    <main class="max-w-7xl mx-auto px-4 py-6">
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
                el.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => el.remove(), 500);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            ['toast-success', 'toast-error'].forEach(id => {
                const toast = document.getElementById(id);
                if (toast) {
                    setTimeout(() => {
                        toast.classList.remove('translate-x-full', 'opacity-0');
                    }, 100);

                    setTimeout(() => {
                        closeToast(id);
                    }, 4500);
                }
            });
        });
    </script>
    <!-- FOOTER SETUP GABON -->
<footer class="bg-slate-900 text-slate-300 border-t border-slate-800 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- COLONNE 1 : À PROPOS & LOGO -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg ">
                         <a href="{{ route('home') }}" class="flex items-center gap-2 text-white font-bold text-lg hover:text-blue-300 transition-colors">
                    <img src="{{ asset('img/logo.png') }}" alt="SETUP-GABON" class="h-9 w-auto object-contain">
                        </a>
                    </div>
                    <span class="text-xl font-black text-white tracking-wider">SETUP <span class="text-blue-500">GABON</span></span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Votre partenaire de confiance en vente d'équipements informatiques, composants PC et matériel réseau au Gabon. Qualité et garantie assurées.
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <a href="https://www.facebook.com/setupgabon" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-blue-600 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                        <i class="fa-brands fa-facebook-f text-xs"></i>
                    </a>
                    <a href="https://wa.me/24166131270" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-emerald-600 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                        <i class="fa-brands fa-whatsapp text-xs"></i>
                    </a>
                    <a href="https://www.linkedin.com/company/setup-gabon/" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-blue-400 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                        <i class="fa-brands fa-linkedin-in text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- COLONNE 2 : NAVIGATIONS RAPIDES -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 border-l-2 border-blue-600 pl-2">Navigation</h3>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="{{ route('home') }}" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-[8px] text-blue-500"></i> Accueil</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-[8px] text-blue-500"></i> Catalogue Produits</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-[8px] text-blue-500"></i> Promotions</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-[8px] text-blue-500"></i> Demande de Devis</a></li>
                </ul>
            </div>

            <!-- COLONNE 3 : SERVICES CLIENTS -->
            <!-- <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 border-l-2 border-blue-600 pl-2">Services & SAV</h3>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-[8px] text-blue-500"></i> Suivi de commande</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-[8px] text-blue-500"></i> Service Après-Vente (SAV)</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-[8px] text-blue-500"></i> Conditions de garantie</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-[8px] text-blue-500"></i> Politique de retour</a></li>
                </ul>
            </div> -->

            <!-- COLONNE 4 : CONTACT & BOUTIQUE -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 border-l-2 border-blue-600 pl-2">Boutique & Contact</h3>
                <ul class="space-y-3 text-xs">
                    <li class="flex items-start gap-2.5">
                        <i class="fa-solid font-bold fa-location-dot text-blue-500 mt-0.5"></i>
                        <span> Libreville, Gabon (A Petit Paris, galerie MOUENGA)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-phone text-blue-500"></i>
                        <span>+241 66 13 12 70</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-envelope text-blue-500"></i>
                        <a href="mailto:protocolees@gmail.com" class="text-blue-400 hover:text-blue-300">
                            <span>protocolees@gmail.com</span>
                        </a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-clock text-blue-500"></i>
                        <span>Mardi - Dimanche : 07h30 - 20h00</span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- MODES DE PAIEMENT & COPYRIGHT -->
        <div class="mt-12 pt-6 border-t border-slate-800/80 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>© {{ date('Y') }} <span class="text-slate-300 font-semibold">SETUP GABON</span>. Tous droits réservés.</p>
            
            <!-- Badges Paiements Locaux -->
           <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-2">Paiements acceptés :</span>
                <span class="px-2 py-1 bg-slate-800 text-slate-300 rounded font-bold text-[10px] border border-slate-700">Espèces</span>
                <span class="px-2 py-1 bg-red-900/40 text-red-400 rounded font-bold text-[10px] border border-red-800/50">Airtel Money</span>
                <span class="px-2 py-1 bg-blue-900/40 text-blue-400 rounded font-bold text-[10px] border border-blue-800/50">Moov Money</span>
            </div>
        </div>
    </div>
</footer>
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
            const index = this.items.findIndex(i => i.id === prod.id);
            if (index > -1) {
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
                    quantite: prod.quantite || 1,
                    stockMax: prod.stockMax
                });
            }
            localStorage.setItem('client_panier', JSON.stringify(this.items));
            this.openCart = true;
        },

        modifierQte(id, delta) {
            const index = this.items.findIndex(i => i.id === id);
            if (index > -1) {
                this.items[index].quantite += delta;
                if (this.items[index].quantite <= 0) {
                    this.items.splice(index, 1);
                }
                localStorage.setItem('client_panier', JSON.stringify(this.items));
            }
        },

        supprimer(id) {
            this.items = this.items.filter(i => i.id !== id);
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