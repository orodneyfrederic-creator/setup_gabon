@extends('layouts.app')

@section('content')

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Caisse</h1>
        <p class="mt-1 text-slate-500">Touchez un produit pour l'ajouter à la vente, puis encaissez.</p>
    </div>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLONNE GAUCHE : catalogue produits --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Recherche --}}
            <form method="GET" action="{{ route('gerante.pos.index') }}" class="flex gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Rechercher un produit..."
                    class="flex-1 h-11 px-4 border border-slate-300 rounded-lg text-sm focus:outline-hidden focus:ring-4 focus:ring-blue-100 focus:border-blue-600 bg-white"
                    id="pos-search-input"
                >
                <button type="submit" class="h-11 px-4 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-sm font-semibold transition-colors" aria-label="Rechercher">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                @if($search)
                    <a href="{{ route('gerante.pos.index') }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-sm hover:bg-slate-50 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>

            {{-- Grille produits --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                @forelse($produits as $produit)
                    <button
                        type="button"
                        onclick='ajouterAuPanier({{ $produit->id }}, {{ json_encode($produit->designation) }}, {{ $produit->prix_unitaire }}, {{ $produit->quantite }})'
                        class="bg-white border border-slate-200 rounded-xl p-2.5 text-left hover:border-blue-400 hover:shadow-md active:scale-[0.98] transition group"
                        id="produit-btn-{{ $produit->id }}"
                    >
                        <div class="w-full aspect-square bg-slate-50 rounded-xl mb-2 flex items-center justify-center overflow-hidden">
                            @if($produit->image)
                                <img src="{{ asset('storage/' . $produit->image) }}" alt="{{ $produit->designation }}" class="w-full h-full object-cover rounded-xl">
                            @else
                                <i class="fa-solid fa-box text-slate-300 text-2xl"></i>
                            @endif
                        </div>
                        <p class="text-sm font-medium text-slate-900 truncate group-hover:text-blue-700 transition-colors">{{ $produit->designation }}</p>
                        <p class="chiffres text-slate-900 font-bold text-sm mt-0.5">{{ number_format($produit->prix_unitaire, 0, ',', ' ') }} FCFA</p>
                        <p class="chiffres text-xs mt-0.5 {{ $produit->quantite <= $produit->seuil_alerte ? 'text-amber-800' : 'text-slate-500' }}">{{ $produit->quantite }} en stock</p>
                    </button>
                @empty
                    <div class="col-span-full text-center py-12 text-slate-400">
                        <i class="fa-solid fa-box-open text-4xl mb-3 block"></i>
                        <p class="text-sm">Aucun produit en stock.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- COLONNE DROITE : Panier --}}
        <div class="flex flex-col gap-4">
            <div class="bg-white border border-slate-200 rounded-xl flex flex-col sticky top-24">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60 flex items-center gap-2">
                    <h2 class="font-semibold text-slate-900">Vente en cours</h2>
                    <span id="panier-count" class="chiffres ml-auto bg-slate-100 text-slate-700 text-xs font-semibold px-2 py-0.5 rounded-full">Aucun article</span>
                </div>

                {{-- Articles du panier --}}
                <div id="panier-liste" class="flex-1 divide-y divide-slate-100 max-h-80 overflow-y-auto">
                    <p id="panier-vide" class="text-center text-slate-400 text-sm py-8">
                        <i class="fa-solid fa-cart-shopping text-2xl block mb-2 opacity-30"></i>
                        Panier vide
                    </p>
                </div>

                {{-- Total --}}
                <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/60">
                    <div class="flex justify-between items-center mb-4">
                        <span class="font-bold text-slate-700 text-sm">Total</span>
                        <span id="panier-total" class="chiffres text-2xl font-bold text-slate-900">0 FCFA</span>
                    </div>

                    {{-- Formulaire de validation --}}
                    <form action="{{ route('gerante.pos.store') }}" method="POST" id="pos-form">
                        @csrf
                        <input type="hidden" name="panier" id="panier-data">

                        <div class="mb-3" x-data="{ open: false, selected: 'Espèces', value: 'Especes', icon: 'fa-money-bill text-emerald-600' }">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Méthode de paiement</label>
                            <input type="hidden" name="methode_paiement" :value="value">
                            <div class="relative">
                                <button type="button" @click="open = !open"
                                        class="w-full flex items-center justify-between border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white text-sm text-slate-800 font-semibold focus:outline-hidden focus:ring-2 focus:ring-emerald-500 shadow-xs transition-all">
                                    <span class="flex items-center gap-2.5">
                                        <i class="fa-solid" :class="icon"></i>
                                        <span x-text="selected"></span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="open" 
                                     x-cloak
                                     @click.outside="open = false" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute left-0 right-0 bottom-full mb-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 py-1 overflow-hidden divide-y divide-slate-100">
                                    
                                    <button type="button" @click="selected='Espèces'; value='Especes'; icon='fa-money-bill text-emerald-600'; open=false" 
                                            class="w-full flex items-center gap-3 px-3.5 py-2.5 hover:bg-emerald-50/60 text-left transition-colors text-xs font-bold text-slate-700">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-money-bill text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-800 font-bold">Espèces</p>
                                            <p class="text-[11px] text-slate-400 font-normal">Paiement physique en monnaie</p>
                                        </div>
                                    </button>

                                    <button type="button" @click="selected='Carte bancaire'; value='Carte'; icon='fa-credit-card text-blue-600'; open=false" 
                                            class="w-full flex items-center gap-3 px-3.5 py-2.5 hover:bg-blue-50/60 text-left transition-colors text-xs font-bold text-slate-700">
                                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-credit-card text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-800 font-bold">Carte bancaire</p>
                                            <p class="text-[11px] text-slate-400 font-normal">Terminal de paiement (TPE)</p>
                                        </div>
                                    </button>

                                    <button type="button" @click="selected='Mobile Money'; value='Mobile Money'; icon='fa-mobile-screen-button text-amber-600'; open=false" 
                                            class="w-full flex items-center gap-3 px-3.5 py-2.5 hover:bg-amber-50/60 text-left transition-colors text-xs font-bold text-slate-700">
                                        <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-mobile-screen-button text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-800 font-bold">Mobile Money</p>
                                            <p class="text-[11px] text-slate-400 font-normal">Airtel Money / Moov Money</p>
                                        </div>
                                    </button>

                                </div>
                            </div>
                        </div>

                        <button
                            type="submit"
                            id="btn-valider"
                            onclick="return preparerSoumission()"
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-colors text-sm flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            <i class="fa-solid fa-check"></i> Encaisser
                        </button>
                    </form>

                    <button type="button" onclick="viderPanier()" class="mt-2 w-full py-2 border border-slate-200 text-slate-500 hover:bg-slate-50 text-xs font-semibold rounded-xl transition-colors">
                        <i class="fa-solid fa-trash-can mr-1"></i> Vider le panier
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let panier = JSON.parse(sessionStorage.getItem('pos_panier') || '{}');

        function sauvegarderPanier() {
            sessionStorage.setItem('pos_panier', JSON.stringify(panier));
        }

        function ajouterAuPanier(id, designation, prix, stockMax) {
            if (panier[id]) {
                if (panier[id].quantite >= stockMax) {
                    alert('Stock maximum atteint pour ce produit (' + stockMax + ' disponible).');
                    return;
                }
                panier[id].quantite++;
            } else {
                panier[id] = { id, designation, prix_unitaire: prix, quantite: 1, stockMax };
            }
            mettreAJourPanier();
        }

        function modifierQuantite(id, delta) {
            if (!panier[id]) { return; }
            panier[id].quantite += delta;
            if (panier[id].quantite <= 0) {
                delete panier[id];
            } else if (panier[id].quantite > panier[id].stockMax) {
                panier[id].quantite = panier[id].stockMax;
            }
            mettreAJourPanier();
        }

        function viderPanier() {
            panier = {};
            sessionStorage.removeItem('pos_panier');
            mettreAJourPanier();
        }

        function mettreAJourPanier() {
            const liste = document.getElementById('panier-liste');
            const vide = document.getElementById('panier-vide');
            const count = document.getElementById('panier-count');
            const totalEl = document.getElementById('panier-total');

            const articles = Object.values(panier);
            let total = 0;
            let nbArticles = 0;

            if (articles.length === 0) {
                liste.innerHTML = `
                    <p id="panier-vide" class="text-center text-slate-400 text-sm py-8">
                        <i class="fa-solid fa-cart-shopping text-2xl block mb-2 opacity-30"></i>
                        Panier vide
                    </p>
                `;
                count.textContent = 'Aucun article';
                totalEl.textContent = '0 FCFA';
                sauvegarderPanier();
                return;
            }

            if (vide) {
                vide.style.display = 'none';
            }
            liste.innerHTML = '';

            articles.forEach(item => {
                const sousTotal = item.prix_unitaire * item.quantite;
                total += sousTotal;
                nbArticles += item.quantite;

                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 px-4 py-3';
                row.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-800 truncate">${item.designation}</p>
                        <p class="text-[11px] text-slate-400">${Number(item.prix_unitaire).toLocaleString('fr-FR')} FCFA × ${item.quantite}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="modifierQuantite(${item.id}, -1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-red-100 hover:text-red-600 text-slate-600 text-xs font-black flex items-center justify-center transition-colors">−</button>
                        <span class="text-xs font-bold text-slate-700 w-5 text-center">${item.quantite}</span>
                        <button type="button" onclick="modifierQuantite(${item.id}, 1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-700 text-slate-600 text-xs font-black flex items-center justify-center transition-colors">+</button>
                    </div>
                    <p class="text-xs font-black text-emerald-700 w-20 text-right">${Number(sousTotal).toLocaleString('fr-FR')} FCFA</p>
                `;
                liste.appendChild(row);
            });

            count.textContent = nbArticles + (nbArticles > 1 ? ' articles' : ' article');
            totalEl.textContent = Number(total).toLocaleString('fr-FR') + ' FCFA';
            sauvegarderPanier();
        }

        function preparerSoumission() {
            const articles = Object.values(panier);
            if (articles.length === 0) {
                alert('Le panier est vide. Ajoutez au moins un produit avant d\'encaisser.');
                return false;
            }
            document.getElementById('panier-data').value = JSON.stringify(articles.map(item => ({
                id: item.id,
                designation: item.designation,
                prix_unitaire: item.prix_unitaire,
                quantite: item.quantite,
            })));
            return true;
        }

        document.addEventListener('DOMContentLoaded', mettreAJourPanier);
    </script>

@endsection

