@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Entête -->
    <div class="bg-gradient-to-r from-blue-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-blue-600/30 border border-blue-500/40 text-blue-300 flex items-center justify-center font-bold text-2xl shadow">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold">Mes Commandes</h1>
                <p class="text-xs text-blue-200 mt-1">Consultez l'historique et l'état de traitement de vos commandes.</p>
            </div>
        </div>
        <a href="{{ route('account.profile') }}" class="px-4 py-2 bg-blue-900/60 hover:bg-blue-800 border border-blue-700/50 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2">
            <i class="fa-solid fa-user"></i>
            <span>Mon Profil</span>
        </a>
    </div>

    <!-- Liste des commandes -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        @if ($commandes->isEmpty())
            <div class="text-center py-12 text-slate-400 space-y-3">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-300 text-3xl">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
                <h3 class="font-bold text-slate-700 text-base">Aucune commande pour le moment</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Vous n'avez pas encore passé de commande sur SETUP-GABON.</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl transition-all shadow-md mt-2">
                    <i class="fa-solid fa-shop"></i>
                    <span>Découvrir nos produits</span>
                </a>
            </div>
        @else
            <div class="space-y-4 divide-y divide-slate-100">
                @foreach ($commandes as $cmd)
                    <div class="pt-4 first:pt-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <span class="font-black text-sm text-slate-900">Commande N°#{{ $cmd->id }}</span>
                                @php
                                    $statuts = [
                                        'en_attente' => ['label' => 'En attente', 'bg' => 'bg-amber-100 text-amber-800 border-amber-200'],
                                        'validee' => ['label' => 'Validée', 'bg' => 'bg-blue-100 text-blue-800 border-blue-200'],
                                        'livree' => ['label' => 'Livrée', 'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                                        'annulee' => ['label' => 'Annulée', 'bg' => 'bg-rose-100 text-rose-800 border-rose-200'],
                                    ];
                                    $st = $statuts[$cmd->statut] ?? ['label' => ucfirst($cmd->statut), 'bg' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $st['bg'] }}">
                                    {{ $st['label'] }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400">
                                Passée le {{ \Carbon\Carbon::parse($cmd->date_c)->format('d/m/Y à H:i') }} • {{ $cmd->lignesCommandes->count() }} article(s)
                            </p>
                            <p class="text-xs font-bold text-blue-700">
                                Total : {{ number_format($cmd->montant_c, 0, ',', ' ') }} FCFA
                            </p>
                        </div>

                        <a href="{{ route('orders.show', $cmd->id) }}" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5 shrink-0">
                            <i class="fa-solid fa-eye text-blue-600"></i>
                            <span>Détails</span>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $commandes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
