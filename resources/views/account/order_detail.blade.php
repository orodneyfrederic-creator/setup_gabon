@extends('layouts.app')

@section('title', 'Commande n° '.$commande->id)

@section('content')
@php
    $etapes = ['en_attente' => 'Reçue', 'confirmee' => 'Confirmée', 'livree' => 'Livrée'];
    $rangActuel = array_search($commande->statut, array_keys($etapes), true);
    $estAnnulee = $commande->statut === 'annulee';
@endphp
<div class="max-w-4xl mx-auto">
    <a href="{{ route('orders.index') }}" class="text-sm text-slate-500 hover:text-blue-700 inline-flex items-center gap-1.5">
        <i class="fa-solid fa-arrow-left text-xs"></i> Mes commandes
    </a>

    <div class="mt-3 mb-6 flex flex-wrap items-center gap-x-4 gap-y-2">
        <h1 class="chiffres text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Commande n° {{ $commande->id }}</h1>
        @include('account.partials.statut', ['statut' => $commande->statut])
    </div>
    <p class="chiffres -mt-4 mb-6 text-slate-500">Passée le {{ \Carbon\Carbon::parse($commande->date_c)->translatedFormat('d F Y à H\hi') }}</p>

    {{-- SUIVI --}}
    <section class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 mb-6" aria-label="Suivi de la commande">
        @if ($estAnnulee)
            <p class="text-sm text-slate-700 flex items-start gap-3">
                <i class="fa-solid fa-ban text-slate-400 mt-0.5"></i>
                <span>Cette commande a été annulée. Pour toute question, contactez la boutique sur
                    <a href="https://wa.me/24166131270?text={{ rawurlencode('Bonjour, je vous contacte au sujet de ma commande n° '.$commande->id.'.') }}" target="_blank" rel="noopener" class="font-semibold text-blue-700 underline">WhatsApp</a>.
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
                        @else
                            <p class="font-medium text-slate-900">Produit retiré du catalogue</p>
                        @endif
                        <p class="chiffres text-sm text-slate-500 mt-0.5">
                            {{ $ligne->quantite_com }} × {{ number_format($prixPaye, 0, ',', ' ') }} FCFA
                            @if ($ligne->negociation_id)
                                <span class="ml-1 text-[11px] font-semibold px-1.5 py-0.5 bg-amber-100 text-amber-900 rounded">Prix négocié</span>
                            @endif
                        </p>
                    </div>
                    <span class="chiffres font-semibold text-slate-900 shrink-0">{{ number_format($prixPaye * $ligne->quantite_com, 0, ',', ' ') }} FCFA</span>
                </li>
            @endforeach
        </ul>
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
