@extends('layouts.app')

@section('content')

    <div class="max-w-lg mx-auto">

        {{-- Bannière succès --}}
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 mb-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center shrink-0">
                <i class="fa-solid fa-check text-white text-lg"></i>
            </div>
            <div>
                <h1 class="font-black text-emerald-900 text-lg">Vente enregistrée !</h1>
                <p class="text-emerald-700 text-sm">Le stock a été mis à jour automatiquement.</p>
            </div>
        </div>
        <script>sessionStorage.removeItem('pos_panier');</script>

        {{-- Reçu --}}
        <div id="recu-zone" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            {{-- En-tête reçu --}}
            <div class="bg-slate-900 text-white px-6 py-5 text-center">
                <p class="text-xs uppercase tracking-widest text-slate-400 mb-1">Setup Gabon</p>
                <p class="text-2xl font-black">🧾 Reçu de caisse</p>
                <p class="text-slate-400 text-xs mt-1">{{ $vente->created_at->format('d/m/Y à H:i') }}</p>
            </div>

            <div class="px-6 py-4 border-b border-slate-100 flex justify-between text-sm">
                <span class="text-slate-500 font-medium">N° Ticket</span>
                <span class="font-black text-slate-900">{{ $vente->numero }}</span>
            </div>
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between text-sm">
                <span class="text-slate-500 font-medium">Caissier</span>
                <span class="font-semibold text-slate-700">{{ $vente->staff?->nom_display ?? 'N/A' }}</span>
            </div>
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between text-sm">
                <span class="text-slate-500 font-medium">Paiement</span>
                <span class="font-semibold text-slate-700">{{ $vente->methode_paiement }}</span>
            </div>

            {{-- Lignes articles --}}
            <div class="divide-y divide-slate-100">
                @foreach($vente->lignes as $ligne)
                    <div class="px-6 py-3 flex justify-between items-start gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $ligne->designation }}</p>
                            <p class="text-xs text-slate-400">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA × {{ $ligne->quantite }}</p>
                        </div>
                        <p class="text-sm font-black text-slate-900 shrink-0">{{ number_format($ligne->total_ligne, 0, ',', ' ') }} FCFA</p>
                    </div>
                @endforeach
            </div>

            {{-- Total --}}
            <div class="px-6 py-5 bg-emerald-50 border-t-2 border-emerald-200 flex justify-between items-center">
                <span class="font-bold text-emerald-900 text-lg">TOTAL</span>
                <span class="text-2xl font-black text-emerald-700">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</span>
            </div>

            <div class="px-6 py-4 text-center text-xs text-slate-400 border-t border-slate-100">
                Merci pour votre achat — Setup Gabon
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <button onclick="window.print()" class="flex-1 py-3 bg-slate-900 hover:bg-slate-700 text-white font-bold rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimer le reçu
            </button>
            <a href="{{ route('gerante.pos.index') }}" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm text-center transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i> Nouvelle vente
            </a>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #recu-zone, #recu-zone * { visibility: visible; }
            #recu-zone { position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>

@endsection
