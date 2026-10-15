@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation retour -->
    <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Retour à mes commandes</span>
    </a>

    <!-- Entête de commande -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Commande N°#{{ $commande->id }}</h1>
                <p class="text-xs text-slate-400 mt-1">Passée le {{ \Carbon\Carbon::parse($commande->date_c)->format('d/m/Y à H:i') }}</p>
            </div>
            @php
                $statuts = [
                    'en_attente' => ['label' => 'En attente de traitement', 'bg' => 'bg-amber-100 text-amber-800 border-amber-200'],
                    'validee' => ['label' => 'Commande Validée', 'bg' => 'bg-blue-100 text-blue-800 border-blue-200'],
                    'livree' => ['label' => 'Commande Livrée', 'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                    'annulee' => ['label' => 'Commande Annulée', 'bg' => 'bg-rose-100 text-rose-800 border-rose-200'],
                ];
                $st = $statuts[$commande->statut] ?? ['label' => ucfirst($commande->statut), 'bg' => 'bg-slate-100 text-slate-700 border-slate-200'];
            @endphp
            <span class="px-4 py-1.5 rounded-full text-xs font-bold border {{ $st['bg'] }}">
                {{ $st['label'] }}
            </span>
        </div>

        <!-- Articles commandés -->
        <div>
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-box text-blue-600"></i>
                <span>Articles commandés</span>
            </h3>

            <div class="divide-y divide-slate-100">
                @foreach ($commande->lignesCommandes as $ligne)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-sm shrink-0">
                                <i class="fa-solid fa-laptop"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">{{ $ligne->produit?->designation ?? 'Produit #'.$ligne->id_p }}</h4>
                                <p class="text-[11px] text-slate-400">
                                    {{ number_format($ligne->prix_unitaire_com, 0, ',', ' ') }} FCFA × {{ $ligne->quantite_com }}
                                </p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-900">
                            {{ number_format($ligne->prix_unitaire_com * $ligne->quantite_com, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Récapitulatif financier -->
        <div class="pt-6 border-t border-slate-100 flex justify-end">
            <div class="w-full sm:w-72 space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <div class="flex justify-between text-xs text-slate-500">
                    <span>Sous-total :</span>
                    <span class="font-bold text-slate-700">{{ number_format($commande->montant_c, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between text-xs text-slate-500">
                    <span>Frais de livraison :</span>
                    <span class="font-bold text-emerald-600">Gratuit / Inclus</span>
                </div>
                <div class="pt-2 border-t border-slate-200 flex justify-between text-sm font-black text-blue-700">
                    <span>Total payé :</span>
                    <span>{{ number_format($commande->montant_c, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
