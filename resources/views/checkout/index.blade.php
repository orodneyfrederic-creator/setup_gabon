@extends('layouts.app')

@section('title', 'Finaliser ma commande')

@section('content')
@if (session('error') || $errors->any())
    {{-- Commande refusée : restaurer le panier (avant l'initialisation d'Alpine) sans les offres négociées invalides --}}
    <script>
        (function () {
            const sauvegarde = localStorage.getItem('client_panier_sauvegarde');
            if (!sauvegarde) return;
            const invalides = @json(session('negociations_invalides', []));
            const items = JSON.parse(sauvegarde).filter(i => !invalides.includes(i.negociation_id));
            localStorage.setItem('client_panier', JSON.stringify(items));
            localStorage.removeItem('client_panier_sauvegarde');
        })();
    </script>
@endif
@php
    $champ = 'w-full h-12 px-3.5 bg-white border border-slate-300 rounded-lg text-[15px] text-slate-900 placeholder-slate-400 focus:outline-hidden focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition';
@endphp
<div class="max-w-5xl mx-auto" x-data="checkoutComponent()">

    <div class="mb-6 sm:mb-8">
        <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-blue-700 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i> Continuer mes achats
        </a>
        <h1 class="mt-3 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Finaliser ma commande</h1>
        <p class="mt-1 text-slate-500">Vérifiez vos articles et indiquez où vous livrer. La boutique reçoit la commande sur WhatsApp.</p>
    </div>

    @if (session('error'))
        <div class="mb-6 bg-rose-50 text-rose-800 px-4 py-3.5 rounded-lg text-sm flex items-start gap-3" role="alert">
            <i class="fa-solid fa-circle-exclamation text-rose-600 mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <template x-if="items.length === 0">
        <div class="bg-white border border-slate-200 rounded-xl px-6 py-14 text-center max-w-lg mx-auto">
            <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                <i class="fa-solid fa-bag-shopping text-lg"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900">Votre panier est vide</h2>
            <p class="text-sm text-slate-500 mt-1">Ajoutez au moins un produit pour passer commande.</p>
            <a href="{{ route('home') }}" class="mt-6 inline-flex items-center h-11 px-5 bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm rounded-lg">
                Voir les produits
            </a>
        </div>
    </template>

    <template x-if="items.length > 0">
        <div class="grid lg:grid-cols-12 gap-6 lg:gap-8 items-start">

            <form id="checkoutForm" method="POST" action="{{ route('checkout.store') }}" @submit="submitCheckout($event)" class="lg:col-span-7 space-y-6">
                @csrf
                <input type="hidden" name="items" :value="JSON.stringify(items)">

                <section class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6">
                    <h2 class="font-bold text-slate-900">Livraison</h2>

                    @if($user)
                        <dl class="mt-4 grid sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-slate-500">Client</dt>
                                <dd class="mt-0.5 font-semibold text-slate-900">{{ $user->prenom }} {{ $user->nom }}</dd>
                            </div>
                            @if($user->tel)
                                <div>
                                    <dt class="text-slate-500">Téléphone</dt>
                                    <dd class="chiffres mt-0.5 font-semibold text-slate-900">{{ $user->tel }}</dd>
                                </div>
                            @endif
                        </dl>
                    @else
                        <p class="mt-1 text-sm text-slate-500">
                            Pas besoin de compte.
                            <a href="{{ route('login') }}" class="font-semibold text-blue-700 underline">Déjà client ? Connectez-vous</a> pour retrouver vos commandes et négocier les prix.
                        </p>
                        <div class="mt-5">
                            <label for="client_nom" class="block text-sm font-semibold text-slate-800 mb-1.5">Nom et prénom</label>
                            <input type="text" id="client_nom" name="client_nom" required maxlength="120" autocomplete="name"
                                   value="{{ old('client_nom') }}"
                                   class="{{ $champ }} @error('client_nom') !border-rose-500 @enderror">
                            @error('client_nom')
                                <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @if(! $user?->tel)
                        <div class="mt-5">
                            <label for="client_telephone" class="block text-sm font-semibold text-slate-800 mb-1.5">Téléphone</label>
                            <input type="tel" id="client_telephone" name="client_telephone" required inputmode="tel" autocomplete="tel"
                                   value="{{ old('client_telephone') }}" placeholder="Ex. : 066 12 34 56"
                                   aria-describedby="aide-telephone"
                                   class="chiffres {{ $champ }} @error('client_telephone') !border-rose-500 @enderror">
                            <p id="aide-telephone" class="text-xs text-slate-500 mt-1.5">La boutique vous appelle à ce numéro pour confirmer la livraison.</p>
                            @error('client_telephone')
                                <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    @unless($user)
                        <div class="mt-5">
                            <label for="client_email" class="block text-sm font-semibold text-slate-800 mb-1.5">
                                E-mail <span class="font-normal text-slate-500">(facultatif)</span>
                            </label>
                            <input type="email" id="client_email" name="client_email" maxlength="255" autocomplete="email" inputmode="email"
                                   value="{{ old('client_email') }}"
                                   aria-describedby="aide-email"
                                   class="{{ $champ }} @error('client_email') !border-rose-500 @enderror">
                            <p id="aide-email" class="text-xs text-slate-500 mt-1.5">Pour recevoir le récapitulatif et le suivi de votre commande.</p>
                            @error('client_email')
                                <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    @endunless

                    <div class="mt-5">
                        <label for="adresse_livraison" class="block text-sm font-semibold text-slate-800 mb-1.5">Adresse ou lieu de livraison à Libreville</label>
                        <input type="text" id="adresse_livraison" name="adresse_livraison" required autocomplete="street-address"
                               value="{{ old('adresse_livraison', $user?->adresse) }}"
                               placeholder="Ex. : Akanda, carrefour GP, quartier Louis…"
                               class="{{ $champ }} @error('adresse_livraison') !border-rose-500 @enderror">
                        @error('adresse_livraison')
                            <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                <fieldset class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6">
                    <legend class="sr-only">Mode de paiement</legend>
                    <h2 class="font-bold text-slate-900" aria-hidden="true">Mode de paiement</h2>
                    <p class="text-sm text-slate-500 mt-1">Rien n'est débité en ligne. Choisissez comment vous réglerez : la boutique vous indique la marche à suivre sur WhatsApp.</p>

                    <div class="mt-4 space-y-2">
                        @foreach ([
                            'especes' => ['Espèces', 'Au livreur ou en boutique', 'fa-solid fa-money-bill-wave'],
                            'airtel_money' => ['Airtel Money', 'Paiement mobile', 'fa-solid fa-mobile-screen-button'],
                            'moov_money' => ['Moov Money', 'Paiement mobile', 'fa-solid fa-mobile-screen-button'],
                        ] as $valeur => [$libelle, $detail, $icone])
                            <label class="flex items-center gap-3 px-4 h-14 border rounded-lg cursor-pointer transition-colors"
                                   :class="payment === @js($valeur) ? 'border-blue-700 bg-blue-50 ring-1 ring-blue-700' : 'border-slate-200 hover:border-slate-300'">
                                <input type="radio" name="methode_paiement" value="{{ $valeur }}" x-model="payment" class="w-4 h-4 text-blue-700 focus:ring-blue-600">
                                <i class="{{ $icone }} w-5 text-center text-slate-500"></i>
                                <span class="flex-1 text-sm font-semibold text-slate-900">{{ $libelle }}</span>
                                <span class="text-xs text-slate-500">{{ $detail }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <section class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6" aria-labelledby="titre-conditions">
                    <h2 id="titre-conditions" class="font-bold text-slate-900">Avant de confirmer</h2>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li class="flex gap-3"><i class="fa-solid fa-truck-fast w-4 mt-1 text-blue-700"></i><span>Livraison gratuite à Libreville sous {{ config('boutique.livraison.delai_heures') }} h après confirmation par la boutique, ou retrait en boutique.</span></li>
                        <li class="flex gap-3"><i class="fa-solid fa-rotate-left w-4 mt-1 text-blue-700"></i><span>{{ config('boutique.retractation_jours') }} jours après réception pour changer d'avis, remboursement intégral.</span></li>
                        <li class="flex gap-3"><i class="fa-solid fa-shield-halved w-4 mt-1 text-blue-700"></i><span>Garantie boutique : 1 mois sur les ordinateurs, 2 semaines sur les autres produits.</span></li>
                    </ul>
                    <label class="mt-5 flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="accepte_conditions" value="1" required @checked(old('accepte_conditions'))
                               class="mt-0.5 w-5 h-5 rounded-sm border-slate-300 text-blue-700 focus:ring-blue-600 shrink-0">
                        <span class="text-sm text-slate-800">
                            J'ai lu et j'accepte les <a href="{{ route('legal.cgv') }}" target="_blank" class="font-semibold text-blue-700 underline">conditions générales de vente</a>
                            et la <a href="{{ route('legal.confidentialite') }}" target="_blank" class="font-semibold text-blue-700 underline">politique de confidentialité</a>.
                        </span>
                    </label>
                    @error('accepte_conditions')
                        <p class="text-rose-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </section>

                <div class="lg:hidden">
                    <button type="submit" class="w-full h-12 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>Confirmer · <span class="chiffres" x-text="formatPrix(totalMontant) + ' FCFA'"></span></span>
                    </button>
                </div>
            </form>

            <aside class="order-first lg:order-none lg:col-span-5 lg:sticky lg:top-24 bg-white border border-slate-200 rounded-xl p-5 sm:p-6">
                <h2 class="font-bold text-slate-900">
                    Récapitulatif
                    <span class="chiffres font-normal text-slate-500" x-text="'· ' + countArticles + ' article' + (countArticles > 1 ? 's' : '')"></span>
                </h2>

                <ul class="mt-4 divide-y divide-slate-100 max-h-80 overflow-y-auto">
                    <template x-for="item in items" :key="item.id + (item.negociation_id ? '-' + item.negociation_id : '')">
                        <li class="py-3 first:pt-0 flex items-start justify-between gap-4 text-sm">
                            <div class="min-w-0">
                                <p class="font-medium text-slate-900 line-clamp-2" x-text="item.designation"></p>
                                <p class="chiffres text-slate-500 mt-0.5">
                                    <span x-text="item.quantite"></span> × <span x-text="formatPrix(item.prix)"></span> FCFA
                                    <template x-if="item.negociation_id">
                                        <span class="ml-1 text-[11px] font-semibold px-1.5 py-0.5 bg-amber-100 text-amber-900 rounded-sm">Prix négocié</span>
                                    </template>
                                </p>
                            </div>
                            <span class="chiffres font-semibold text-slate-900 shrink-0" x-text="formatPrix(item.prix * item.quantite) + ' FCFA'"></span>
                        </li>
                    </template>
                </ul>

                <dl class="mt-4 pt-4 border-t border-slate-200 space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <dt>Livraison à Libreville</dt>
                        <dd class="font-semibold text-emerald-700">Gratuite</dd>
                    </div>
                    <div class="flex justify-between items-baseline pt-2">
                        <dt class="font-semibold text-slate-900">Total</dt>
                        <dd class="chiffres text-2xl font-bold text-slate-900" x-text="formatPrix(totalMontant) + ' FCFA'"></dd>
                    </div>
                </dl>

                <button type="submit" form="checkoutForm" class="hidden lg:flex mt-5 w-full h-12 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg items-center justify-center gap-2 transition-colors">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>Confirmer la commande</span>
                </button>
                <p class="mt-3 text-xs text-slate-500 leading-relaxed">
                    En confirmant, votre commande est enregistrée et WhatsApp s'ouvre avec le détail prêt à envoyer à la boutique.
                </p>
            </aside>
        </div>
    </template>
</div>

<script>
function checkoutComponent() {
    return {
        items: JSON.parse(localStorage.getItem('client_panier') || '[]'),
        payment: @js(old('methode_paiement', 'especes')),

        get countArticles() {
            return this.items.reduce((total, i) => total + i.quantite, 0);
        },

        get totalMontant() {
            return this.items.reduce((total, i) => total + (i.prix * i.quantite), 0);
        },

        formatPrix(val) {
            return new Intl.NumberFormat('fr-FR').format(val);
        },

        submitCheckout(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Votre panier est vide. Vous ne pouvez pas passer de commande.');
                return false;
            }
            // Vider le panier dans le navigateur une fois validé (une copie permet de le restaurer si le serveur refuse la commande)
            localStorage.setItem('client_panier_sauvegarde', JSON.stringify(this.items));
            localStorage.setItem('client_panier', JSON.stringify([]));
        }
    }
}
</script>
@endsection
