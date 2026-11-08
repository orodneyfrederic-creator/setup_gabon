@extends('layouts.app')

@section('title', $produit->designation)
@section('meta_description', \Illuminate\Support\Str::limit(trim(($produit->description ?: $produit->designation).' – '.number_format($produit->prix_unitaire, 0, ',', ' ').' FCFA chez SETUP-Gabon, Libreville.'), 155))

@section('og_type', 'product')
@section('og_image', route('produits.apercu', $produit->id))
@section('og_image_alt', $produit->designation)
@section('url_canonique', route('produits.show', $produit->id))

@push('donnees_structurees')
    {{-- « @@context » : le double @ empêche Blade de le prendre pour sa directive @@context (Laravel 13) --}}
    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $produit->designation,
            'sku' => $produit->reference,
            'description' => \Illuminate\Support\Str::limit(trim($produit->description ?: $produit->designation), 500),
            'image' => array_map(fn ($chemin) => asset('storage/'.$chemin), $produit->galerie()) ?: null,
            'category' => $produit->category?->nom,
            'brand' => $produit->marque && strcasecmp(trim($produit->marque->nom), 'Sans marque') !== 0
                ? ['@type' => 'Brand', 'name' => $produit->marque->nom]
                : null,
            'offers' => [
                '@type' => 'Offer',
                'url' => route('produits.show', $produit->id),
                'price' => (int) $produit->prix_unitaire,
                'priceCurrency' => 'XAF',
                'availability' => $produit->quantite > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller' => ['@type' => 'Organization', 'name' => 'SETUP-Gabon'],
            ],
            // Note moyenne issue des avis réels et visibles sur la page (règle Google pour les avis produits)
            'aggregateRating' => $avisPublies->isNotEmpty() ? [
                '@type' => 'AggregateRating',
                'ratingValue' => round($avisPublies->avg('note'), 1),
                'reviewCount' => $avisPublies->count(),
                'bestRating' => 5,
                'worstRating' => 1,
            ] : null,
        ]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
@php
    $enStock = $produit->quantite > 0;
    $stockFaible = $enStock && $produit->quantite <= 3;
@endphp

{{-- FENÊTRE DE NÉGOCIATION (Alpine.js + API JSON) --}}
@auth
<div x-data="negociationModal()" x-cloak @keydown.escape.window="fermer()">
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-950/50 z-[9998]"
         @click="fermer()">
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
         x-transition:enter-end="translate-y-0 sm:opacity-100 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 sm:opacity-100"
         x-transition:leave-end="translate-y-full sm:translate-y-0 sm:opacity-0"
         class="fixed inset-x-0 bottom-0 sm:inset-0 sm:m-auto sm:h-fit z-[9999] w-full sm:w-[440px] max-h-[92vh] bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl flex flex-col overflow-hidden"
         role="dialog" aria-modal="true" aria-labelledby="titre-negociation"
         @click.stop>

        <div class="px-5 pt-4 pb-3 flex items-start justify-between gap-4 border-b border-slate-100 shrink-0">
            <div>
                <h3 id="titre-negociation" class="font-bold text-slate-900">Proposer mon prix</h3>
                <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                    {{ $produit->designation }} · <span class="chiffres">{{ number_format($produit->prix_unitaire, 0, ',', ' ') }} FCFA</span>
                </p>
            </div>
            <button @click="fermer()" class="w-9 h-9 -mr-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center shrink-0" aria-label="Fermer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-slate-50 min-h-[200px] max-h-[50vh]" id="negoChatZone" x-ref="chatZone">

            <template x-if="!negociation && !loading && !erreur">
                <div class="text-center py-6 text-sm text-slate-500">
                    <p>Indiquez la quantité puis démarrez pour proposer votre prix.</p>
                </div>
            </template>

            <template x-if="loading">
                <div class="text-center py-8" role="status">
                    <div class="animate-spin w-7 h-7 border-[3px] border-slate-200 border-t-blue-700 rounded-full mx-auto"></div>
                    <p class="text-xs text-slate-500 mt-3">Un instant…</p>
                </div>
            </template>

            <template x-if="erreur && !loading">
                <div class="bg-rose-50 text-rose-800 text-sm px-3.5 py-3 rounded-lg flex items-start gap-2.5" role="alert">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-600"></i>
                    <span x-text="erreur"></span>
                </div>
            </template>

            <template x-for="(msg, idx) in messages" :key="idx">
                <div :class="msg.auteur === 'client' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.auteur === 'client'
                        ? 'bg-blue-700 text-white rounded-2xl rounded-br-md px-4 py-2.5 max-w-[85%]'
                        : 'bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-bl-md px-4 py-2.5 max-w-[85%]'">
                        <p class="text-sm leading-relaxed" x-html="msg.message"></p>
                        <div class="flex items-center gap-2 mt-1.5" :class="msg.auteur === 'client' ? 'justify-end' : 'justify-start'">
                            <template x-if="msg.montant">
                                <span class="chiffres text-xs font-semibold px-2 py-0.5 rounded-full"
                                      :class="msg.auteur === 'client' ? 'bg-white/15' : 'bg-amber-100 text-amber-900'"
                                      x-text="formatPrix(msg.montant) + ' FCFA'"></span>
                            </template>
                            <span class="text-[11px] opacity-60" x-text="msg.heure"></span>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="negociation && negociation.statut === 'acceptee'">
                <div class="bg-white border border-emerald-200 rounded-xl p-4 text-center space-y-3">
                    <div class="w-11 h-11 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-emerald-600 text-lg">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Prix accepté</p>
                        <p class="chiffres text-2xl font-bold text-emerald-700 mt-1" x-text="formatPrix(negociation.prix_final_convenu) + ' FCFA'"></p>
                    </div>
                    <template x-if="negociation.expire_a">
                        <p class="text-xs text-slate-500">
                            <i class="fa-solid fa-clock mr-1"></i>
                            Offre valable encore <strong class="chiffres text-slate-700" x-text="tempsRestant"></strong>
                        </p>
                    </template>
                    <button @click="ajouterAuPanierNegocie()"
                            class="w-full h-11 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span>Ajouter au panier à ce prix</span>
                    </button>
                </div>
            </template>
        </div>

        <div class="border-t border-slate-100 bg-white px-4 py-4 shrink-0 space-y-3">

            <template x-if="!negociation && !loading">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <label for="quantite-negociation" class="text-sm font-medium text-slate-700">Quantité</label>
                        <input id="quantite-negociation" type="number" x-model.number="quantiteNego" min="1" :max="stockMax"
                               class="chiffres w-20 h-10 px-3 border border-slate-300 rounded-lg text-center font-semibold focus:outline-hidden focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                    </div>
                    <button @click="demarrer()" :disabled="loading"
                            class="w-full h-11 bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm rounded-lg transition-colors disabled:opacity-50">
                        Démarrer la négociation
                    </button>
                </div>
            </template>

            <template x-if="negociation && negociation.statut === 'en_cours' && !loading">
                <div class="space-y-2.5">
                    <template x-if="!contreOffrePendante">
                        <form @submit.prevent="proposerOffre()" class="flex gap-2">
                            <label for="offre-client" class="sr-only">Votre prix en FCFA</label>
                            <div class="relative flex-1">
                                <input id="offre-client" type="number" inputmode="numeric" x-model.number="offreClient" min="1" placeholder="Votre prix"
                                       class="chiffres w-full h-11 pl-3.5 pr-14 border border-slate-300 rounded-lg font-semibold focus:outline-hidden focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-500">FCFA</span>
                            </div>
                            <button type="submit" :disabled="!offreClient || offreClient < 1"
                                    class="h-11 px-4 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-40">
                                Proposer
                            </button>
                        </form>
                    </template>

                    <template x-if="contreOffrePendante">
                        <div class="space-y-2.5">
                            <button @click="accepterContre()"
                                    class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                                <i class="fa-solid fa-check"></i>
                                <span>Accepter cette offre</span>
                            </button>
                            <template x-if="negociation.tours_utilises < negociation.tours_max">
                                <form @submit.prevent="proposerOffre()" class="flex gap-2">
                                    <label for="contre-offre-client" class="sr-only">Autre prix en FCFA</label>
                                    <div class="relative flex-1">
                                        <input id="contre-offre-client" type="number" inputmode="numeric" x-model.number="offreClient" min="1" placeholder="Proposer un autre prix"
                                               class="chiffres w-full h-11 pl-3.5 pr-14 border border-slate-300 rounded-lg font-semibold focus:outline-hidden focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-500">FCFA</span>
                                    </div>
                                    <button type="submit" :disabled="!offreClient || offreClient < 1"
                                            class="h-11 px-4 border border-slate-300 text-slate-800 hover:bg-slate-50 text-sm font-semibold rounded-lg transition-colors disabled:opacity-40">
                                        Proposer
                                    </button>
                                </form>
                            </template>
                        </div>
                    </template>

                    <div class="flex items-center justify-between pt-1 text-xs text-slate-500">
                        <span class="chiffres">Proposition <span x-text="negociation.tours_utilises"></span> sur <span x-text="negociation.tours_max"></span></span>
                        <button @click="abandonner()" class="hover:text-rose-600 underline">Abandonner</button>
                    </div>
                </div>
            </template>

            <template x-if="negociation && ['refusee','expiree','annulee'].includes(negociation.statut)">
                <div class="text-center text-sm text-slate-600 space-y-2">
                    <p>Cette négociation est terminée. Vous pourrez en ouvrir une nouvelle dans 24 h.</p>
                    <button @click="fermer()" class="text-blue-700 font-semibold underline">Fermer</button>
                </div>
            </template>

            <template x-if="negociation && negociation.statut === 'en_attente_gerante'">
                <div class="text-center text-sm bg-amber-50 text-amber-900 px-4 py-3 rounded-lg">
                    <p class="font-semibold">Votre offre est entre les mains de la gérante</p>
                    <p class="text-xs text-amber-800 mt-1">Revenez sur cette page pour voir sa réponse.</p>
                </div>
            </template>

            <template x-if="negociation && negociation.statut === 'utilisee'">
                <p class="text-center text-sm text-slate-600">Cette offre a déjà servi dans une commande.</p>
            </template>
        </div>
    </div>
</div>
@endauth

{{-- FIL D'ARIANE --}}
<nav class="mb-5 text-sm text-slate-500" aria-label="Fil d'Ariane">
    <ol class="flex items-center gap-2 min-w-0">
        <li class="shrink-0"><a href="{{ route('home') }}" class="hover:text-blue-700">Boutique</a></li>
        @if($produit->category)
            <li aria-hidden="true" class="text-slate-300">/</li>
            <li class="shrink-0"><a href="{{ route('home', ['category' => $produit->id_cat]) }}#catalogue" class="hover:text-blue-700">{{ $produit->category->nom }}</a></li>
        @endif
        <li aria-hidden="true" class="text-slate-300 hidden sm:block">/</li>
        <li aria-current="page" class="text-slate-800 truncate hidden sm:block">{{ $produit->designation }}</li>
    </ol>
</nav>

<div class="grid lg:grid-cols-12 gap-6 lg:gap-12 items-start">

    {{-- VISUEL : galerie de photos --}}
    @php $photos = array_map(fn ($chemin) => asset('storage/'.$chemin), $produit->galerie()); @endphp
    <div class="lg:col-span-7"
         x-data="{
             photos: @js($photos),
             active: 0,
             debutToucher: null,
             precedente() { this.active = (this.active - 1 + this.photos.length) % this.photos.length },
             suivante() { this.active = (this.active + 1) % this.photos.length },
         }"
         @keydown.left.window="if (photos.length > 1 && !document.activeElement.closest('input, textarea')) precedente()"
         @keydown.right.window="if (photos.length > 1 && !document.activeElement.closest('input, textarea')) suivante()">
        <div class="relative bg-white border border-slate-200 rounded-2xl overflow-hidden aspect-square sm:aspect-[4/3] lg:aspect-square"
             @touchstart.passive="debutToucher = $event.touches[0].clientX"
             @touchend.passive="if (debutToucher !== null && photos.length > 1) { const ecart = $event.changedTouches[0].clientX - debutToucher; if (Math.abs(ecart) > 40) { ecart < 0 ? suivante() : precedente() } } debutToucher = null">
            @if(count($photos) > 0)
                <img :src="photos[active]" src="{{ $photos[0] }}" :alt="@js($produit->designation) + ', photo ' + (active + 1) + ' sur ' + photos.length" alt="{{ $produit->designation }}"
                     class="w-full h-full object-contain">
                @if(count($photos) > 1)
                    <button type="button" @click="precedente()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-md text-slate-700 flex items-center justify-center" aria-label="Photo précédente">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <button type="button" @click="suivante()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-md text-slate-700 flex items-center justify-center" aria-label="Photo suivante">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                    <span class="chiffres absolute right-3 top-3 px-2 py-0.5 rounded-md bg-slate-950/70 text-white text-xs" x-text="(active + 1) + ' / ' + photos.length" aria-live="polite"></span>
                @endif
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 gap-2">
                    <i class="fa-solid fa-laptop text-6xl"></i>
                    <span class="text-sm text-slate-400">Photo bientôt disponible</span>
                </div>
            @endif
            @if($produit->video)
                <button type="button" onclick="openVideoModal()"
                        class="absolute left-4 bottom-4 h-11 pl-2 pr-4 rounded-full bg-slate-950/80 hover:bg-slate-950 text-white text-sm font-semibold flex items-center gap-2.5 transition-colors">
                    <span class="w-7 h-7 rounded-full bg-white text-slate-950 flex items-center justify-center"><i class="fa-solid fa-play text-[10px] ml-0.5"></i></span>
                    Voir la vidéo
                </button>
            @endif
        </div>

        @if(count($photos) > 1)
            <ul class="mt-3 flex gap-2 overflow-x-auto scrollbar-none" aria-label="Photos du produit">
                @foreach($photos as $index => $photo)
                    <li class="shrink-0">
                        <button type="button" @click="active = {{ $index }}"
                                class="block w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden border-2 bg-white transition-colors"
                                :class="active === {{ $index }} ? 'border-blue-700' : 'border-transparent hover:border-slate-300'"
                                :aria-current="active === {{ $index }} ? 'true' : 'false'"
                                aria-label="Afficher la photo {{ $index + 1 }}">
                            <img src="{{ $photo }}" alt="" loading="lazy" class="w-full h-full object-cover">
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- ACHAT --}}
    <div class="lg:col-span-5 lg:sticky lg:top-24">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 leading-tight text-balance">
            {{ $produit->designation }}
        </h1>

        <p class="mt-2 text-sm text-slate-500 flex flex-wrap items-center gap-x-2">
            @if($produit->marque && strcasecmp(trim($produit->marque->nom), 'Sans marque') !== 0)
                <span class="font-semibold text-slate-700">{{ $produit->marque->nom }}</span>
                <span aria-hidden="true">·</span>
            @endif
            <span>Réf. {{ $produit->reference }}</span>
        </p>

        @if($avisPublies->isNotEmpty())
            <a href="#avis" class="mt-2 inline-flex items-center gap-2 text-sm text-slate-600 hover:text-blue-700">
                @include('produits.partials.etoiles', ['note' => round($avisPublies->avg('note'), 1)])
                <span class="chiffres">{{ number_format($avisPublies->avg('note'), 1, ',', ' ') }}</span>
                <span class="underline">{{ $avisPublies->count() }} avis</span>
            </a>
        @endif

        <p class="chiffres mt-5 text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
            {{ number_format($produit->prix_unitaire, 0, ',', ' ') }} <span class="text-lg font-semibold text-slate-500">FCFA</span>
        </p>

        <p class="mt-3 text-sm font-medium flex items-center gap-2 {{ ! $enStock ? 'text-slate-600' : ($stockFaible ? 'text-amber-800' : 'text-emerald-700') }}">
            <span class="w-2 h-2 rounded-full {{ ! $enStock ? 'bg-slate-400' : ($stockFaible ? 'bg-amber-500' : 'bg-emerald-600') }}"></span>
            @if(! $enStock)
                En rupture pour le moment
            @elseif($stockFaible)
                Plus que {{ $produit->quantite }} en stock à la boutique
            @else
                En stock à la boutique · <span class="chiffres">{{ $produit->quantite }}</span> disponibles
            @endif
        </p>

        <div class="mt-6 pt-6 border-t border-slate-200 space-y-3">
            @if($enStock)
                <div class="flex gap-3">
                    <div class="flex items-center h-12 border border-slate-300 rounded-lg bg-white shrink-0">
                        <button type="button" class="w-11 h-full text-slate-500 hover:text-blue-700" onclick="document.getElementById('qte').stepDown()" aria-label="Diminuer la quantité">
                            <i class="fa-solid fa-minus text-xs"></i>
                        </button>
                        <label for="qte" class="sr-only">Quantité</label>
                        <input type="number" id="qte" name="quantite" value="1" min="1" max="{{ $produit->quantite }}"
                               class="chiffres w-10 h-full text-center font-semibold text-slate-900 bg-transparent focus:outline-hidden [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none"
                               oninput="if(this.value > {{ $produit->quantite }}) this.value = {{ $produit->quantite }}; if(this.value !== '' && this.value < 1) this.value = 1;">
                        <button type="button" class="w-11 h-full text-slate-500 hover:text-blue-700" onclick="document.getElementById('qte').stepUp()" aria-label="Augmenter la quantité">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                    </div>
                    <button type="button" onclick="ajouterDepuisFiche()"
                            class="flex-1 h-12 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span>Ajouter au panier</span>
                    </button>
                </div>

                <button type="button" onclick="commanderMaintenant()"
                        class="w-full h-12 border border-blue-700 text-blue-700 hover:bg-blue-50 font-semibold rounded-lg transition-colors">
                    Commander maintenant
                </button>

                @if($estNegociable)
                    <button type="button" onclick="ouvrirNegociationClient({{ $produit->id }})"
                            class="w-full mt-1 px-4 py-3 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 text-left flex items-center gap-3 transition-colors group">
                        <span class="w-9 h-9 rounded-full bg-amber-400 text-blue-950 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-handshake-simple text-sm"></i>
                        </span>
                        <span class="flex-1">
                            <span class="block text-sm font-semibold text-slate-900">Proposer mon prix</span>
                            <span class="block text-xs text-slate-600">Faites une offre à la boutique, dans la limite des remises autorisées.</span>
                        </span>
                        <i class="fa-solid fa-chevron-right text-xs text-amber-700 group-hover:translate-x-0.5 transition-transform"></i>
                    </button>
                @endif
            @else
                <a href="https://wa.me/24166131270?text={{ rawurlencode('Bonjour, le produit « '.$produit->designation.' » (réf. '.$produit->reference.') sera-t-il bientôt disponible ?') }}" target="_blank" rel="noopener"
                   class="w-full h-12 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>Être prévenu du retour en stock</span>
                </a>
            @endif
        </div>

        <ul class="mt-6 space-y-3 text-sm text-slate-600">
            <li class="flex gap-3">
                <i class="fa-solid fa-truck-fast w-5 mt-0.5 text-blue-700"></i>
                <span><strong class="font-semibold text-slate-900">Livraison gratuite</strong> à Libreville, ou retrait à la boutique de Petit Paris.</span>
            </li>
            <li class="flex gap-3">
                <i class="fa-solid fa-shield-halved w-5 mt-0.5 text-blue-700"></i>
                <span><strong class="font-semibold text-slate-900">Garantie boutique {{ $produit->libelleGarantie() }}</strong> et {{ config('boutique.retractation_jours') }} jours pour changer d'avis. <a href="{{ route('legal.retours') }}" class="text-blue-700 underline">En savoir plus</a></span>
            </li>
            <li class="flex gap-3">
                <i class="fa-solid fa-wallet w-5 mt-0.5 text-blue-700"></i>
                <span>Paiement en espèces, Airtel Money ou Moov Money. Rien n'est débité en ligne.</span>
            </li>
            <li class="flex gap-3">
                <i class="fa-brands fa-whatsapp w-5 mt-0.5 text-blue-700"></i>
                <span>Une question ?
                    <a href="https://wa.me/24166131270?text={{ rawurlencode('Bonjour, j\'ai une question sur « '.$produit->designation.' » (réf. '.$produit->reference.').') }}" target="_blank" rel="noopener" class="font-semibold text-blue-700 underline">Écrivez-nous sur WhatsApp</a>
                </span>
            </li>
        </ul>

        <a href="https://wa.me/?text={{ rawurlencode($produit->designation.' – '.number_format($produit->prix_unitaire, 0, ',', ' ').' FCFA chez SETUP-Gabon : '.route('produits.show', $produit->id)) }}"
           target="_blank" rel="noopener"
           class="mt-5 inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-white hover:border-slate-400 transition-colors">
            <i class="fa-brands fa-whatsapp"></i>
            Partager ce produit
        </a>
    </div>
</div>

@if(count($produit->ficheTechnique()) > 0)
    <section class="mt-8 pt-8 lg:mt-12 lg:pt-10 border-t border-slate-200 grid lg:grid-cols-12 gap-4 lg:gap-6" aria-labelledby="titre-fiche">
        <h2 id="titre-fiche" class="lg:col-span-4 text-xl font-bold tracking-tight text-slate-900">Fiche technique</h2>
        <dl class="lg:col-span-8 max-w-[68ch] divide-y divide-slate-200 border-y border-slate-200 text-[15px]">
            @foreach($produit->ficheTechnique() as $ligne)
                <div class="py-3 grid grid-cols-5 gap-4">
                    <dt class="col-span-2 text-slate-500">{{ $ligne['libelle'] }}</dt>
                    <dd class="col-span-3 text-slate-900 font-medium">{{ $ligne['valeur'] }}</dd>
                </div>
            @endforeach
        </dl>
    </section>
@endif

@if($produit->description)
    <section class="mt-8 pt-8 lg:mt-12 lg:pt-10 border-t border-slate-200 grid lg:grid-cols-12 gap-4 lg:gap-6">
        <h2 class="lg:col-span-4 text-xl font-bold tracking-tight text-slate-900">Description</h2>
        <div class="lg:col-span-8 max-w-[68ch] text-[15px] leading-relaxed text-slate-700">
            {!! nl2br(e($produit->description)) !!}
        </div>
    </section>
@endif

@include('produits.partials.avis')

@if($produitsSimilaires->isNotEmpty())
    <section class="mt-8 pt-8 lg:mt-12 lg:pt-10 border-t border-slate-200" aria-labelledby="titre-similaires">
        <h2 id="titre-similaires" class="text-xl font-bold tracking-tight text-slate-900">Dans la même catégorie</h2>
        <div class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($produitsSimilaires as $similaire)
                @include('produits.partials.carte', ['produit' => $similaire])
            @endforeach
        </div>
    </section>
@endif

{{-- BARRE D'ACHAT MOBILE --}}
@if($enStock)
    <div class="h-20 lg:hidden" aria-hidden="true"></div>
    <div class="lg:hidden fixed inset-x-0 bottom-0 z-30 bg-white border-t border-slate-200 px-4 py-3 flex items-center gap-3" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
        <p class="chiffres flex-1 font-bold text-slate-900 leading-tight">
            {{ number_format($produit->prix_unitaire, 0, ',', ' ') }} <span class="text-xs font-semibold text-slate-500">FCFA</span>
        </p>
        <button type="button" onclick="ajouterDepuisFiche()" class="h-11 px-5 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg flex items-center gap-2">
            <i class="fa-solid fa-bag-shopping"></i>
            Ajouter au panier
        </button>
    </div>
@endif

<script>
function quantiteChoisie() {
    return Math.max(1, parseInt(document.getElementById('qte')?.value || 1, 10));
}

function ajouterDepuisFiche() {
    ajouterAuPanierBoutique({{ $produit->id }}, @json($produit->designation), {{ $produit->prix_unitaire }}, {{ $produit->quantite }}, quantiteChoisie());
}

function commanderMaintenant() {
    ajouterDepuisFiche();
    window.location.href = @json(route('checkout.index'));
}
</script>

@if($produit->video)
<!-- Modal Vidéo -->
<div id="videoModal" class="fixed inset-0 z-[99999] bg-slate-950/95 flex items-center justify-center" style="display:none;" onclick="closeVideoModal()" role="dialog" aria-modal="true" aria-label="Vidéo du produit">
    <div class="relative w-[95%] max-w-4xl mx-auto" onclick="event.stopPropagation()">
        <button onclick="closeVideoModal()" class="absolute -top-12 right-0 w-10 h-10 rounded-full text-white/80 hover:text-white hover:bg-white/10 flex items-center justify-center" aria-label="Fermer la vidéo">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>

        <div class="bg-black rounded-xl overflow-hidden">
            <video id="productVideo"
                   controls
                   playsinline
                   preload="none"
                   disablepictureinpicture
                   class="w-full bg-black outline-hidden"
                   style="display:block; width:100%; max-height:85vh;">
                <source src="{{ route('produits.video', $produit->id) }}" type="video/mp4">
                Votre navigateur ne supporte pas la balise vidéo.
            </video>
        </div>
    </div>
</div>

<script>
function openVideoModal() {
    var modal = document.getElementById('videoModal');
    var v = document.getElementById('productVideo');
    modal.style.display = 'flex';
    v.load();
    v.play().catch(function(e) { console.warn('Autoplay bloqué:', e); });
}

function closeVideoModal() {
    var modal = document.getElementById('videoModal');
    var v = document.getElementById('productVideo');
    v.pause();
    v.currentTime = 0;
    modal.style.display = 'none';
}
</script>
@endif

<script>
function ouvrirNegociationClient(produitId) {
    @guest
        window.location.href = "{{ route('login') }}";
        return;
    @endguest

    window.dispatchEvent(new CustomEvent('ouvrir-modal-negociation', {
        detail: { produitId: produitId }
    }));
}

function negociationModal() {
    return {
        open: false,
        produitId: {{ $produit->id }},
        stockMax: {{ $produit->quantite }},
        quantiteNego: 1,
        offreClient: '',
        negociation: null,
        messages: [],
        loading: false,
        erreur: null,
        contreOffrePendante: false,
        tempsRestant: '',
        timerInterval: null,

        init() {
            window.addEventListener('ouvrir-modal-negociation', (e) => {
                if (e.detail.produitId === this.produitId) {
                    this.open = true;
                    this.demarrer();
                }
            });
        },

        fermer() {
            this.open = false;
            if (this.timerInterval) clearInterval(this.timerInterval);
        },

        formatPrix(val) {
            return new Intl.NumberFormat('fr-FR').format(val);
        },

        majCompteARebours() {
            if (!this.negociation || !this.negociation.expire_a) return;
            const expire = new Date(this.negociation.expire_a).getTime();
            
            const update = () => {
                const now = new Date().getTime();
                const diff = expire - now;
                if (diff <= 0) {
                    this.tempsRestant = 'Expiré';
                    if (this.timerInterval) clearInterval(this.timerInterval);
                    return;
                }
                const h = Math.floor(diff / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);
                this.tempsRestant = `${h}h ${m}m ${s}s`;
            };
            update();
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.timerInterval = setInterval(update, 1000);
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const zone = this.$refs.chatZone;
                if (zone) zone.scrollTop = zone.scrollHeight;
            });
        },

        async demarrer() {
            this.loading = true;
            this.erreur = null;
            try {
                const res = await fetch("{{ url('/produits/' . $produit->id . '/negocier') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantite: this.quantiteNego })
                });

                if (res.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                let data;
                try {
                    data = await res.json();
                } catch (jsonErr) {
                    this.erreur = 'Réponse non exploitable du serveur (code ' + res.status + ').';
                    return;
                }

                if (!res.ok) {
                    this.erreur = data.message || 'Une erreur est survenue (' + res.status + ').';
                    return;
                }
                this.negociation = data.negociation;
                this.messages = (data.negociation.offres || []).map(o => ({
                    auteur: o.auteur,
                    message: o.message,
                    montant: o.montant_propose,
                    heure: o.heure
                }));
                this.verifierContreOffre();
                if (this.negociation.statut === 'acceptee') {
                    this.majCompteARebours();
                }
                this.scrollToBottom();
            } catch (err) {
                console.error('Erreur négociation:', err);
                this.erreur = 'Erreur lors de la communication : ' + (err.message || 'Problème réseau.');
            } finally {
                this.loading = false;
            }
        },

        verifierContreOffre() {
            if (!this.negociation || !this.negociation.offres) {
                this.contreOffrePendante = false;
                return;
            }
            const offres = this.negociation.offres;
            if (offres.length > 0) {
                const last = offres[offres.length - 1];
                this.contreOffrePendante = (last.auteur === 'systeme' || last.auteur === 'gerante') 
                    && ['contre_offre', 'derniere_offre'].includes(last.type_action)
                    && this.negociation.statut === 'en_cours';
            } else {
                this.contreOffrePendante = false;
            }
        },

        async proposerOffre() {
            if (!this.offreClient || this.offreClient < 1) return;
            this.loading = true;
            this.erreur = null;
            const montant = parseInt(this.offreClient);
            this.offreClient = '';

            try {
                const res = await fetch("{{ url('/negociations') }}/" + this.negociation.id + "/offre", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ montant: montant })
                });
                const data = await res.json();
                if (!res.ok) {
                    this.erreur = data.message || 'Erreur lors de la proposition.';
                    return;
                }
                this.negociation = data.negociation;
                this.messages = (data.negociation.offres || []).map(o => ({
                    auteur: o.auteur,
                    message: o.message,
                    montant: o.montant_propose,
                    heure: o.heure
                }));
                this.verifierContreOffre();
                if (this.negociation.statut === 'acceptee') {
                    this.majCompteARebours();
                }
                this.scrollToBottom();
            } catch (err) {
                this.erreur = 'Erreur réseau lors de la soumission de l\'offre.';
            } finally {
                this.loading = false;
            }
        },

        async accepterContre() {
            this.loading = true;
            this.erreur = null;
            try {
                const res = await fetch("{{ url('/negociations') }}/" + this.negociation.id + "/accepter", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (!res.ok) {
                    this.erreur = data.message || 'Impossible d\'accepter l\'offre.';
                    return;
                }
                this.negociation = data.negociation;
                this.messages = (data.negociation.offres || []).map(o => ({
                    auteur: o.auteur,
                    message: o.message,
                    montant: o.montant_propose,
                    heure: o.heure
                }));
                this.contreOffrePendante = false;
                this.majCompteARebours();
                this.scrollToBottom();
            } catch (err) {
                this.erreur = 'Erreur réseau lors de l\'acceptation.';
            } finally {
                this.loading = false;
            }
        },

        async abandonner() {
            if (!confirm('Êtes-vous sûr de vouloir abandonner ? Vous devrez attendre 24h avant de pouvoir négocier à nouveau sur ce produit.')) {
                return;
            }
            this.loading = true;
            try {
                const res = await fetch("{{ url('/negociations') }}/" + this.negociation.id + "/abandonner", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.negociation.statut = 'annulee';
                    this.messages.push({
                        auteur: 'client',
                        message: 'Vous avez abandonné la négociation.',
                        heure: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                    });
                }
            } catch (err) {
                this.erreur = 'Erreur réseau lors de l\'abandon.';
            } finally {
                this.loading = false;
            }
        },

        ajouterAuPanierNegocie() {
            if (!this.negociation || this.negociation.statut !== 'acceptee') return;
            
            window.dispatchEvent(new CustomEvent('ajouter-au-panier-client', {
                detail: {
                    id: this.produitId,
                    designation: @json($produit->designation),
                    prix: this.negociation.prix_final_convenu,
                    prixCatalogue: {{ $produit->prix_unitaire }},
                    stockMax: this.stockMax,
                    quantite: this.quantiteNego,
                    negociation_id: this.negociation.id,
                    negociation_ref: this.negociation.reference
                }
            }));
            this.fermer();
        }
    };
}
</script>

@endsection

