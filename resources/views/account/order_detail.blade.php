@extends('layouts.app')

@section('title', 'Commande n° '.$commande->id)

@section('content')
@php
    $etapes = ['en_attente' => 'Reçue', 'confirmee' => 'Confirmée', 'livree' => 'Livrée'];
    $rangActuel = array_search($commande->statut, array_keys($etapes), true);
    $estAnnulee = $commande->statut === 'annulee';
    $vueInvitee = $vueInvitee ?? false;
    $lienWhatsAppBoutique = 'https://wa.me/'.config('boutique.whatsapp').'?text='.rawurlencode('Bonjour, je vous contacte au sujet de ma commande n° '.$commande->id.'.');
@endphp
@if ($vueInvitee)
    {{-- Lien secret : ni indexé par les moteurs, ni transmis aux sites ouverts depuis cette page --}}
    @push('styles')
        <meta name="robots" content="noindex, nofollow">
        <meta name="referrer" content="no-referrer">
    @endpush
@endif
<div class="max-w-4xl mx-auto">
    @if ($vueInvitee)
        <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-blue-700 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i> Retour à la boutique
        </a>
    @else
        <a href="{{ route('orders.index') }}" class="text-sm text-slate-500 hover:text-blue-700 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i> Mes commandes
        </a>
    @endif

    <div class="mt-3 mb-6 flex flex-wrap items-center gap-x-4 gap-y-2">
        <h1 class="chiffres text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Commande n° {{ $commande->id }}</h1>
        @include('account.partials.statut', ['statut' => $commande->statut])
    </div>
    <p class="chiffres -mt-4 mb-6 text-slate-500">Passée le {{ \Carbon\Carbon::parse($commande->date_c)->translatedFormat('d F Y à H\hi') }}</p>

    {{-- ACCUSÉ DE RÉCEPTION (loi n°025/2021, art. 52) --}}
    @if (session('commande_confirmee'))
        <section class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-5 sm:p-6" role="status">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <h2 class="font-bold text-slate-900">Commande n° {{ $commande->id }} enregistrée</h2>
                    <p class="mt-1 text-sm text-slate-700">
                        Dernière étape : envoyez-la à la boutique sur WhatsApp. La boutique vous recontacte pour confirmer la livraison, sous {{ config('boutique.livraison.delai_heures') }} h après confirmation.
                    </p>
                    @if (session('whatsapp_url'))
                        <a href="{{ session('whatsapp_url') }}" target="_blank" rel="noopener"
                           class="mt-4 inline-flex h-12 px-5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold items-center gap-2 transition-colors">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            Envoyer ma commande sur WhatsApp
                        </a>
                    @endif
                    @if ($vueInvitee)
                        <p class="mt-3 text-xs text-slate-600">
                            Gardez le lien de cette page (ajoutez-la à vos favoris) : il vous permet de suivre votre commande.
                            @if ($commande->client_email)
                                Nous vous l'avons aussi envoyé par e-mail.
                            @endif
                        </p>
                    @else
                        <p class="mt-3 text-xs text-slate-600">Ce récapitulatif reste disponible à tout moment dans « Mes commandes ».</p>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- SUIVI --}}
    <section class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 mb-6" aria-label="Suivi de la commande">
        @if ($estAnnulee)
            <p class="text-sm text-slate-700 flex items-start gap-3">
                <i class="fa-solid fa-ban text-slate-400 mt-0.5"></i>
                <span>Cette commande a été annulée. Pour toute question, contactez la boutique sur
                    <a href="{{ $lienWhatsAppBoutique }}" target="_blank" rel="noopener" class="font-semibold text-blue-700 underline">WhatsApp</a>.
                </span>
            </p>
        @else
            <ol class="grid grid-cols-3">
                @foreach ($etapes as $cle => $libelle)
                    @php
                        $rang = $loop->index;
                        $atteinte = $rangActuel !== false && $rang <= $rangActuel;
                    @endphp
                    <li class="relative flex flex-col items-center text-center">
                        @unless ($loop->first)
                            <span class="absolute top-4 right-1/2 w-full h-0.5 -z-0 {{ $atteinte ? 'bg-blue-700' : 'bg-slate-200' }}" aria-hidden="true"></span>
                        @endunless
                        <span class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $atteinte ? 'bg-blue-700 text-white' : 'bg-white border-2 border-slate-200 text-slate-400' }}">
                            @if ($atteinte)
                                <i class="fa-solid fa-check"></i>
                            @else
                                {{ $rang + 1 }}
                            @endif
                        </span>
                        <span class="mt-2 text-sm {{ $atteinte ? 'font-semibold text-slate-900' : 'text-slate-500' }}">{{ $libelle }}</span>
                    </li>
                @endforeach
            </ol>
            @if ($commande->statut === 'en_attente')
                <p class="mt-5 pt-4 border-t border-slate-100 text-sm text-slate-600">
                    La boutique va vous recontacter pour confirmer la disponibilité et la livraison.
                </p>
            @endif
        @endif
    </section>

    {{-- ARTICLES --}}
    <section class="bg-white border border-slate-200 rounded-xl">
        <h2 class="px-5 sm:px-6 pt-5 font-bold text-slate-900">Articles</h2>
        <ul class="px-5 sm:px-6 divide-y divide-slate-100">
            @foreach ($commande->lignesCommandes as $ligne)
                @php $prixPaye = $ligne->prix_final_unitaire ?? $ligne->prix_unitaire_com; @endphp
                <li class="py-4 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        @if ($ligne->produit)
                            <a href="{{ route('produits.show', $ligne->produit->id) }}" class="font-medium text-slate-900 hover:text-blue-700">{{ $ligne->produit->designation }}</a>
                            @if (! $vueInvitee && $commande->statut === 'livree')
                                <a href="{{ route('produits.show', $ligne->produit->id) }}#avis" class="ml-2 text-xs font-semibold text-blue-700 underline whitespace-nowrap">
                                    <i class="fa-regular fa-star"></i> Donner mon avis
                                </a>
                            @endif
                        @else
                            <p class="font-medium text-slate-900">Produit retiré du catalogue</p>
                        @endif
                        <p class="chiffres text-sm text-slate-500 mt-0.5">
                            {{ $ligne->quantite_com }} × {{ number_format($prixPaye, 0, ',', ' ') }} FCFA
                            @if ($ligne->negociation_id)
                                <span class="ml-1 text-[11px] font-semibold px-1.5 py-0.5 bg-amber-100 text-amber-900 rounded-sm">Prix négocié</span>
                            @endif
                        </p>
                    </div>
                    <span class="chiffres font-semibold text-slate-900 shrink-0">{{ number_format($prixPaye * $ligne->quantite_com, 0, ',', ' ') }} FCFA</span>
                </li>
            @endforeach
        </ul>
        @if ($commande->adresse_livraison || $commande->methode_paiement || $commande->conditions_acceptees_at)
            <dl class="px-5 sm:px-6 py-4 border-t border-slate-200 grid sm:grid-cols-3 gap-4 text-sm">
                @if ($commande->adresse_livraison)
                    <div>
                        <dt class="text-slate-500">Livraison</dt>
                        <dd class="mt-0.5 text-slate-900">{{ $commande->adresse_livraison }}</dd>
                    </div>
                @endif
                @if ($commande->methode_paiement)
                    <div>
                        <dt class="text-slate-500">Mode de paiement</dt>
                        <dd class="mt-0.5 text-slate-900">{{ $commande->libelleMethodePaiement() }}</dd>
                    </div>
                @endif
                @if ($commande->conditions_acceptees_at)
                    <div>
                        <dt class="text-slate-500">Conditions de vente</dt>
                        <dd class="mt-0.5 text-slate-900">
                            Acceptées le {{ $commande->conditions_acceptees_at->translatedFormat('d/m/Y à H\hi') }}
                            · <a href="{{ route('legal.cgv') }}" class="text-blue-700 underline">consulter</a>
                        </dd>
                    </div>
                @endif
            </dl>
        @endif
        <dl class="px-5 sm:px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-xl space-y-1.5 text-sm">
            <div class="flex justify-between text-slate-600">
                <dt>Livraison</dt>
                <dd class="font-semibold text-emerald-700">Gratuite</dd>
            </div>
            <div class="flex justify-between items-baseline">
                <dt class="font-semibold text-slate-900">Total de la commande</dt>
                <dd class="chiffres text-xl font-bold text-slate-900">{{ number_format($commande->montant_c, 0, ',', ' ') }} FCFA</dd>
            </div>
        </dl>
    </section>
</div>
@endsection
