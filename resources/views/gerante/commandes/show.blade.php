@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    {{-- FIL D'ARIANE & RETOUR --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('gerante.commandes.index') }}" class="text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Retour aux commandes</span>
        </a>
        <span class="text-xs font-mono font-bold text-slate-400">Commande #{{ $commande->id }}</span>
    </div>

    {{-- EN-TÊTE DE COMMANDE --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2 flex-wrap">
                <h1 class="text-xl font-black text-slate-900">Commande en ligne #{{ $commande->id }}</h1>
                @php
                    $statutClasses = [
                        'en_attente' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'confirmee' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'livree' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'annulee' => 'bg-rose-50 text-rose-700 border-rose-200',
                    ];
                    $statutLabels = [
                        'en_attente' => 'En attente',
                        'confirmee' => 'Confirmée',
                        'livree' => 'Livrée',
                        'annulee' => 'Annulée',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statutClasses[$commande->statut] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ $statutLabels[$commande->statut] ?? $commande->statut }}
                </span>
            </div>
            <p class="text-xs text-slate-500">
                Passée le {{ $commande->date_c ? \Carbon\Carbon::parse($commande->date_c)->format('d/m/Y à H:i') : $commande->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>

        <div class="text-right">
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Montant total</p>
            <p class="text-2xl font-black text-blue-700">{{ number_format($commande->montant_c, 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
        </div>
    </div>

    {{-- DÉTAILS CLIENT ET ACTIONS GÉRANTE --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Colonne Gauche : Client (1 col) --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-user text-blue-600"></i>
                <span>Client</span>
            </h3>
            <div class="space-y-2 text-xs text-slate-700">
                <p class="font-bold text-sm text-slate-900">{{ $commande->user->prenom }} {{ $commande->user->nom }}</p>
                <p><i class="fa-solid fa-phone text-slate-400 mr-2"></i>{{ $commande->user->tel ?: 'Non renseigné' }}</p>
                <p><i class="fa-solid fa-envelope text-slate-400 mr-2"></i>{{ $commande->user->email }}</p>
                <p><i class="fa-solid fa-location-dot text-slate-400 mr-2"></i>{{ $commande->user->adresse ?: 'Non renseignée' }}</p>

                @if($commande->user->tel)
                    <div class="pt-3">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $commande->user->tel) }}" target="_blank" 
                           class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-sm">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Écrire sur WhatsApp</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Colonne Droite : Gestion du statut (2 cols) --}}
        <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-gears text-blue-600"></i>
                <span>Changer le statut de la commande</span>
            </h3>

            <form action="{{ route('gerante.commandes.statut', $commande) }}" method="POST" class="space-y-4" x-data="{ statutChoisi: @js($commande->statut) }">
                @csrf
                @method('PATCH')

                @php
                    $optionsStatut = [
                        'en_attente' => ['label' => 'En attente', 'icone' => 'fa-clock text-amber-600', 'actif' => 'border-amber-500 bg-amber-50 ring-2 ring-amber-300'],
                        'confirmee' => ['label' => 'Confirmée', 'icone' => 'fa-circle-check text-emerald-600', 'actif' => 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-300'],
                        'livree' => ['label' => 'Livrée', 'icone' => 'fa-truck text-blue-600', 'actif' => 'border-blue-500 bg-blue-50 ring-2 ring-blue-300'],
                        'annulee' => ['label' => 'Annulée', 'icone' => 'fa-ban text-rose-600', 'actif' => 'border-rose-500 bg-rose-50 ring-2 ring-rose-300'],
                    ];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach ($optionsStatut as $valeur => $option)
                        @if (in_array($valeur, $statutsPossibles, true))
                            <label class="p-3 border rounded-xl cursor-pointer flex flex-col items-center gap-1.5 text-center transition-all"
                                   :class="statutChoisi === @js($valeur) ? @js($option['actif']) : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="statut" value="{{ $valeur }}" x-model="statutChoisi" {{ $commande->statut === $valeur ? 'checked' : '' }} class="sr-only">
                                <i class="fa-solid {{ $option['icone'] }}"></i>
                                <span class="text-xs font-bold text-slate-800">{{ $option['label'] }}</span>
                                @if ($commande->statut === $valeur)
                                    <span class="text-[11px] text-slate-400">Statut actuel</span>
                                @endif
                            </label>
                        @endif
                    @endforeach
                </div>

                <div class="text-[11px] text-slate-500 bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-1">
                    <p><i class="fa-solid fa-shield-halved text-blue-500 mr-1"></i> <strong>Règles de gestion automatique :</strong></p>
                    <p>• Le passage à <em>Confirmée</em> ou <em>Livrée</em> décrémente automatiquement le stock et enregistre un <code>MouvementStock</code> de sortie.</p>
                    <p>• Le passage à <em>Annulée</em> restaure le stock (si la commande était déjà confirmée) et libère les offres négociées.</p>
                    <p>• Une commande confirmée ne peut plus revenir <em>En attente</em>, et une commande annulée est définitive.</p>
                </div>

                @if (count($statutsPossibles) > 1)
                    <div class="flex justify-end">
                        <button type="submit" :disabled="statutChoisi === @js($commande->statut)" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Enregistrer le changement</span>
                        </button>
                    </div>
                @else
                    <p class="text-xs text-slate-500 text-right">Cette commande est dans un statut final : aucun changement possible.</p>
                @endif
            </form>
        </div>

    </div>

    {{-- ARTICLES COMMANDÉS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-bag-shopping text-blue-600"></i>
                <span>Articles commandés ({{ $commande->lignesCommandes->count() }})</span>
            </h3>
        </div>

        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold text-[11px]">
                    <th class="py-3 px-4">Article</th>
                    <th class="py-3 px-4 text-center">Quantité</th>
                    <th class="py-3 px-4 text-right">Prix cat.</th>
                    <th class="py-3 px-4 text-right">Prix facturé</th>
                    <th class="py-3 px-4 text-center">Négociation</th>
                    <th class="py-3 px-4 text-right">Sous-total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($commande->lignesCommandes as $ligne)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-900">{{ $ligne->produit?->designation ?? 'Produit supprimé' }}</p>
                            @if($ligne->produit)
                                <p class="text-[11px] text-slate-400">Réf: {{ $ligne->produit->reference }} | Stock: {{ $ligne->produit->quantite }}</p>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                            {{ $ligne->quantite_com }}
                        </td>
                        <td class="py-3.5 px-4 text-right text-slate-500">
                            {{ number_format($ligne->prix_catalogue_unitaire ?? $ligne->prix_unitaire_com, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-slate-900">
                            {{ number_format($ligne->prix_final_unitaire ?? $ligne->prix_unitaire_com, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($ligne->negociation)
                                <a href="{{ route('gerante.negociations.show', $ligne->negociation) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200 hover:bg-amber-200 transition-colors">
                                    <i class="fa-solid fa-handshake-simple"></i>
                                    <span>#{{ $ligne->negociation->reference }}</span>
                                </a>
                            @else
                                <span class="text-slate-400 text-[11px]">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-blue-700">
                            {{ number_format(($ligne->prix_final_unitaire ?? $ligne->prix_unitaire_com) * $ligne->quantite_com, 0, ',', ' ') }} FCFA
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-50 border-t-2 border-slate-200 font-bold">
                    <td colspan="5" class="py-3 px-4 text-right text-slate-600 uppercase tracking-wider text-xs">Total Général :</td>
                    <td class="py-3 px-4 text-right font-black text-base text-blue-700">
                        {{ number_format($commande->montant_c, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
@endsection
