@extends('layouts.app')

@section('content')

    <!-- EN-TÊTE DE PAGE -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Fiche client : {{ $client->prenom }} {{ $client->nom }}</h1>
            <p class="mt-1 text-slate-500">Détails du compte et historique des activités</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('gerante.clients.index') }}" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition-colors shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> <span>Retour aux clients</span>
            </a>
        </div>
    </div>

    <!-- CARTE D'INFORMATIONS DU CLIENT -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-lg">
                    {{ strtoupper(substr($client->prenom ?? $client->nom ?? 'C', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ $client->prenom }} {{ $client->nom }}</h2>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[11px] font-bold rounded-full">Client Inscrit</span>
                </div>
            </div>
            <div class="space-y-2 text-xs border-t border-slate-100 pt-3">
                <p class="flex items-center justify-between text-slate-600">
                    <span><i class="fa-solid fa-envelope text-slate-400 mr-2"></i>Email :</span>
                    <span class="font-semibold text-slate-800">{{ $client->email }}</span>
                </p>
                <p class="flex items-center justify-between text-slate-600">
                    <span><i class="fa-solid fa-phone text-slate-400 mr-2"></i>Téléphone :</span>
                    <span class="font-semibold text-slate-800">{{ $client->tel ?? 'Non renseigné' }}</span>
                </p>
                <p class="flex items-center justify-between text-slate-600">
                    <span><i class="fa-solid fa-calendar text-slate-400 mr-2"></i>Inscrit le :</span>
                    <span class="font-semibold text-slate-800">{{ $client->created_at ? $client->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                </p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total des commandes</p>
                <p class="text-3xl font-black text-slate-900 mt-2">{{ $client->commandes_count }}</p>
            </div>
            <p class="text-xs text-slate-500 mt-4"><i class="fa-solid fa-box text-blue-500 mr-1"></i> Commandes passées sur le site</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Dépensé</p>
                <p class="text-3xl font-black text-emerald-600 mt-2">{{ number_format($client->commandes_sum_montant_c ?? 0, 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
            </div>
            <p class="text-xs text-slate-500 mt-4"><i class="fa-solid fa-sack-dollar text-emerald-500 mr-1"></i> Volume d'achat généré</p>
        </div>
    </div>

    <!-- HISTORIQUE DES COMMANDES -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden mb-8">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-blue-600"></i> Historique des commandes du client
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500">Commande #</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-center">Date</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-center">Statut</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-right">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($client->commandes as $commande)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3.5 text-xs font-bold text-slate-800">
                                #{{ $commande->id }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-center text-slate-600">
                                {{ $commande->created_at ? $commande->created_at->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold 
                                    {{ $commande->statut === 'Payée' ? 'bg-emerald-100 text-emerald-800' : ($commande->statut === 'En attente' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $commande->statut ?? 'Nouveau' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-xs font-black text-right text-slate-900">
                                {{ number_format($commande->montant_c, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-500 text-xs">
                                Ce client n'a pas encore passé de commande.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
