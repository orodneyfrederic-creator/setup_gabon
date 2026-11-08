@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Facture {{ $facture->numero }}</h1>
            <p class="mt-1 text-slate-500">Émise le {{ $facture->created_at->format('d/m/Y à H:i') }} @if($facture->devis) (Depuis devis {{ $facture->devis->numero }}) @endif</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button onclick="window.print()" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimer
            </button>
            <a href="{{ route('gerante.factures.pdf', $facture) }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl transition-colors shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-download"></i> PDF
            </a>
            <a href="{{ route('gerante.factures.index') }}" class="px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('info'))
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-info text-blue-600"></i>
            <p class="text-sm font-semibold">{{ session('info') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Colonne Gauche : Actions & Client -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Actions Statut -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs p-6">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">
                    <i class="fa-solid fa-bolt text-emerald-500 mr-2"></i> Statut & Paiement
                </h2>
                
                <div class="mb-6 text-center">
                    @if($facture->statut === 'emise')
                        <div class="inline-flex items-center px-4 py-2 rounded-xl bg-rose-50 text-rose-800 border border-rose-200">
                            <i class="fa-solid fa-circle-exclamation mr-2"></i> <span class="font-bold">Facture Impayée</span>
                        </div>
                    @elseif($facture->statut === 'payee')
                        <div class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                            <i class="fa-solid fa-check-double mr-2"></i> <span class="font-bold">Facture Payée</span>
                        </div>
                    @elseif($facture->statut === 'annulee')
                        <div class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-50 text-slate-800 border border-slate-200">
                            <i class="fa-solid fa-ban mr-2"></i> <span class="font-bold">Facture Annulée</span>
                        </div>
                    @endif
                </div>

                @if($facture->statut === 'emise')
                    <form action="{{ route('gerante.factures.status', $facture) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="statut" value="payee">
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl transition-colors shadow-xs flex items-center justify-center gap-2">
                            <i class="fa-solid fa-hand-holding-dollar"></i> Enregistrer le paiement
                        </button>
                    </form>
                @endif

                @if(in_array($facture->statut, ['emise', 'payee'], true))
                    <form action="{{ route('gerante.factures.status', $facture) }}" method="POST" class="mt-3" onsubmit="return confirm('Annuler cette facture ? Les produits facturés seront remis en stock.');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="statut" value="annulee">
                        <button type="submit" class="w-full py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-ban"></i> Annuler la facture
                        </button>
                    </form>
                    <p class="text-[11px] text-slate-400 mt-3 text-center">Une annulation remet les produits en stock et est définitive.</p>
                @endif
            </div>

            <!-- Client Info -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs p-6">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">
                    <i class="fa-solid fa-user text-emerald-600 mr-2"></i> Facturé à
                </h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-slate-500 font-semibold uppercase">Nom</p>
                        <p class="font-bold text-slate-800">{{ $facture->client_nom }}</p>
                    </div>
                    @if($facture->client_entreprise)
                    <div>
                        <p class="text-xs text-slate-500 font-semibold uppercase">Entreprise</p>
                        <p class="font-bold text-slate-800">{{ $facture->client_entreprise }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-slate-500 font-semibold uppercase">Téléphone</p>
                        <p class="font-bold text-slate-800">{{ $facture->client_telephone }}</p>
                    </div>
                    @if($facture->client_email)
                    <div>
                        <p class="text-xs text-slate-500 font-semibold uppercase">Email</p>
                        <p class="font-bold text-slate-800 break-all">{{ $facture->client_email }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
        </div>

        <!-- Colonne Droite : Lignes & Total -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden h-full flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">
                        <i class="fa-solid fa-list text-emerald-600 mr-2"></i> Détail de la facture
                    </h2>
                </div>
                
                <div class="p-0 overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-100">
                                <th class="px-6 py-3 text-xs font-bold text-slate-500">Désignation</th>
                                <th class="px-6 py-3 text-xs font-bold text-slate-500 text-right">P.U.</th>
                                <th class="px-6 py-3 text-xs font-bold text-slate-500 text-center">Qté</th>
                                <th class="px-6 py-3 text-xs font-bold text-slate-500 text-right">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($facture->lignes as $ligne)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-800">{{ $ligne->produit->designation ?? 'Produit inconnu' }}</p>
                                        <p class="text-xs text-slate-500">Réf : {{ $ligne->produit->reference ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-sm font-semibold text-slate-700">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-800 font-bold text-sm">
                                            {{ $ligne->quantite }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-sm font-black text-slate-900">{{ number_format($ligne->sous_total, 0, ',', ' ') }} FCFA</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-slate-600 uppercase">Montant Net à Payer :</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xl font-black text-emerald-700">{{ number_format($facture->montant_total, 0, ',', ' ') }}</span>
                                    <span class="text-sm font-bold text-emerald-700 ml-1">FCFA</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($facture->notes)
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Notes</h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $facture->notes }}</p>
                </div>
                @endif
                
            </div>
        </div>

    </div>

@endsection
