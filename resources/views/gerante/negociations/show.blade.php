@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    {{-- FIL D'ARIANE & RETOUR --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('gerante.negociations.index') }}" class="text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Retour aux négociations</span>
        </a>
        <span class="text-xs font-mono font-bold text-slate-400">Réf. #{{ $negociation->reference }}</span>
    </div>

    {{-- EN-TÊTE DÉTAIL --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2 flex-wrap">
                <h1 class="text-xl font-black text-slate-900">Négociation #{{ $negociation->reference }}</h1>
                @php
                    $statutClasses = [
                        'en_cours' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'en_attente_gerante' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'acceptee' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'utilisee' => 'bg-violet-50 text-violet-700 border-violet-200',
                        'refusee' => 'bg-rose-50 text-rose-700 border-rose-200',
                        'expiree' => 'bg-slate-100 text-slate-600 border-slate-200',
                        'annulee' => 'bg-slate-100 text-slate-500 border-slate-200',
                    ];
                    $statutLabels = [
                        'en_cours' => 'En cours',
                        'en_attente_gerante' => 'Décision requise',
                        'acceptee' => 'Acceptée',
                        'utilisee' => 'Utilisée dans une commande',
                        'refusee' => 'Refusée',
                        'expiree' => 'Expirée',
                        'annulee' => 'Abandonnée',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statutClasses[$negociation->statut] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ $statutLabels[$negociation->statut] ?? $negociation->statut }}
                </span>
            </div>
            <p class="text-xs text-slate-500">
                Initié le {{ $negociation->created_at->format('d/m/Y à H:i') }} par <strong>{{ $negociation->user->prenom }} {{ $negociation->user->nom }}</strong>
            </p>
        </div>

        {{-- RÉSUMÉ FINANCIER --}}
        <div class="flex items-center gap-6 bg-slate-50 px-5 py-3 rounded-xl border border-slate-100">
            <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Prix Catalogue</p>
                <p class="text-base font-black text-slate-800">{{ number_format($negociation->prix_catalogue, 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span></p>
            </div>
            <div class="h-8 w-px bg-slate-200"></div>
            <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Prix Convenu</p>
                <p class="text-base font-black text-emerald-600">
                    {{ $negociation->prix_final_convenu ? number_format($negociation->prix_final_convenu, 0, ',', ' ') . ' FCFA' : '—' }}
                </p>
            </div>
            @if($margeEstimee !== null)
                <div class="h-8 w-px bg-slate-200"></div>
                <div>
                    <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Marge brute unit.</p>
                    <p class="text-base font-black {{ $margeEstimee >= 0 ? 'text-blue-600' : 'text-rose-600' }}">
                        {{ number_format($margeEstimee, 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span>
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- DÉTAILS DU PRODUIT ET CLIENT --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Fiche Produit --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-box text-blue-600"></i>
                <span>Équipement concerné</span>
            </h3>
            <div class="flex items-center gap-4">
                @if($negociation->produit->image)
                    <img src="{{ asset('storage/' . $negociation->produit->image) }}" class="w-16 h-16 object-contain rounded-xl bg-slate-50 border p-1" alt="">
                @else
                    <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl">
                        <i class="fa-solid fa-image"></i>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm text-slate-900 truncate">{{ $negociation->produit->designation }}</p>
                    <p class="text-xs text-slate-500">Réf: {{ $negociation->produit->reference }}</p>
                    <p class="text-xs text-slate-500">Stock actuel : <strong>{{ $negociation->produit->quantite }}</strong> unité(s)</p>
                </div>
            </div>
            <div class="text-xs text-slate-600 pt-2 border-t border-slate-100 flex justify-between">
                <span>Quantité négociée :</span>
                <span class="font-bold text-slate-800">{{ $negociation->quantite }} unité(s)</span>
            </div>
        </div>

        {{-- Fiche Client --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-user text-blue-600"></i>
                <span>Informations Client</span>
            </h3>
            <div class="space-y-1.5 text-xs text-slate-700">
                <p class="font-bold text-sm text-slate-900">{{ $negociation->user->prenom }} {{ $negociation->user->nom }}</p>
                <p><i class="fa-solid fa-envelope text-slate-400 mr-2"></i>{{ $negociation->user->email }}</p>
                <p><i class="fa-solid fa-phone text-slate-400 mr-2"></i>{{ $negociation->user->tel ?: 'Non renseigné' }}</p>
                <p><i class="fa-solid fa-location-dot text-slate-400 mr-2"></i>{{ $negociation->user->adresse ?: 'Non renseignée' }}</p>
            </div>
        </div>
    </div>

    {{-- FORMULAIRE D'ARBITRAGE GÉRANTE (si en_attente_gerante) --}}
    @if($negociation->statut === 'en_attente_gerante')
        <div class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-3 text-amber-800">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-sm">
                    <i class="fa-solid fa-gavel"></i>
                </div>
                <div>
                    <h3 class="font-black text-sm uppercase tracking-wide">Décision Manuelle Requise</h3>
                    <p class="text-xs text-amber-700">Le client a formulé une offre nécessitant votre arbitrage direct.</p>
                </div>
            </div>

            <form action="{{ route('gerante.negociations.decider', $negociation) }}" method="POST" class="space-y-4" x-data="{ action: 'accepter' }">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="p-3 bg-white rounded-xl border-2 cursor-pointer flex items-center gap-3 transition-all"
                           :class="action === 'accepter' ? 'border-emerald-500 ring-2 ring-emerald-200' : 'border-slate-200'">
                        <input type="radio" name="action" value="accepter" x-model="action" class="sr-only">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                        <div>
                            <p class="font-bold text-xs text-slate-800">Accepter l'offre</p>
                            <p class="text-[11px] text-slate-400">Au prix proposé par le client</p>
                        </div>
                    </label>

                    <label class="p-3 bg-white rounded-xl border-2 cursor-pointer flex items-center gap-3 transition-all"
                           :class="action === 'contre_offre' ? 'border-amber-500 ring-2 ring-amber-200' : 'border-slate-200'">
                        <input type="radio" name="action" value="contre_offre" x-model="action" class="sr-only">
                        <i class="fa-solid fa-handshake text-amber-600 text-lg"></i>
                        <div>
                            <p class="font-bold text-xs text-slate-800">Faire une contre-offre</p>
                            <p class="text-[11px] text-slate-400">Proposer un nouveau tarif</p>
                        </div>
                    </label>

                    <label class="p-3 bg-white rounded-xl border-2 cursor-pointer flex items-center gap-3 transition-all"
                           :class="action === 'refuser' ? 'border-rose-500 ring-2 ring-rose-200' : 'border-slate-200'">
                        <input type="radio" name="action" value="refuser" x-model="action" class="sr-only">
                        <i class="fa-solid fa-circle-xmark text-rose-600 text-lg"></i>
                        <div>
                            <p class="font-bold text-xs text-slate-800">Refuser net</p>
                            <p class="text-[11px] text-slate-400">Mettre fin à l'échange</p>
                        </div>
                    </label>
                </div>

                {{-- Champ montant pour la contre-offre --}}
                <div x-show="action === 'contre_offre'" x-cloak class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Montant de votre contre-offre (FCFA) *</label>
                    <input type="number" name="montant" min="1" placeholder="Ex: 445000"
                           class="w-full sm:w-64 px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold focus:outline-hidden focus:border-amber-500">
                </div>

                {{-- Message explicatif optionnel --}}
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Message au client (optionnel)</label>
                    <textarea name="message" rows="2" placeholder="Ex: Nous acceptons exceptionnellement votre offre compte tenu de la quantité..."
                              class="w-full px-4 py-2 border border-slate-300 rounded-xl text-xs focus:outline-hidden focus:border-amber-500"></textarea>
                </div>

                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Valider ma décision</span>
                </button>
            </form>
        </div>
    @endif

    {{-- TIMELINE DES OFFRES --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
            <i class="fa-solid fa-clock-rotate-left text-blue-600"></i>
            <span>Historique des échanges ({{ $negociation->offres->count() }})</span>
        </h3>

        <div class="space-y-4 pl-4 border-l-2 border-slate-100 ml-2">
            @forelse($negociation->offres as $offre)
                <div class="relative pl-6">
                    {{-- Puce d'icône --}}
                    @php
                        $iconBg = match($offre->auteur) {
                            'client' => 'bg-blue-600 text-white',
                            'systeme' => 'bg-amber-500 text-white',
                            'gerante' => 'bg-violet-600 text-white',
                            default => 'bg-slate-400 text-white'
                        };
                        $iconClass = match($offre->type_action) {
                            'offre_client' => 'fa-arrow-up',
                            'contre_offre' => 'fa-arrows-rotate',
                            'acceptation' => 'fa-check',
                            'derniere_offre' => 'fa-flag-checkered',
                            'mise_en_attente' => 'fa-hourglass-half',
                            'refus' => 'fa-xmark',
                            'abandon' => 'fa-person-walking-arrow-right',
                            default => 'fa-comment'
                        };
                    @endphp
                    <div class="absolute -left-[35px] top-1 w-6 h-6 rounded-full {{ $iconBg }} flex items-center justify-center text-[11px] shadow-sm">
                        <i class="fa-solid {{ $iconClass }}"></i>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800">
                                @if($offre->auteur === 'client')
                                    {{ $negociation->user->prenom }} {{ $negociation->user->nom }} (Client)
                                @elseif($offre->auteur === 'gerante')
                                    Gérante (Arbitrage manuel)
                                @else
                                    Moteur commercial automatique
                                @endif
                            </span>
                            <span class="text-slate-400 text-[11px]">{{ $offre->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-xs text-slate-600">{!! nl2br(e($offre->message)) !!}</p>
                        @if($offre->montant_propose)
                            <div class="pt-1 flex items-center gap-2">
                                <span class="text-[11px] font-bold text-slate-400 uppercase">Montant :</span>
                                <span class="text-xs font-black text-slate-900 bg-white px-2 py-0.5 rounded-sm border border-slate-200">
                                    {{ number_format($offre->montant_propose, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-4">Aucun échange enregistré.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
