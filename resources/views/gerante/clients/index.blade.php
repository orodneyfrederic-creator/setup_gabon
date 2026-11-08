@extends('layouts.app')

@section('content')

    <!-- EN-TÊTE DE PAGE -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Clients</h1>
            <p class="mt-1 text-slate-500">Consultez tous les comptes clients inscrits sur la plateforme</p>
        </div>
        <div>
            <a href="{{ route('gerante.dashboard') }}" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition-colors shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- RECHERCHE & CONTENEUR TABLEAU -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden mb-6">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <form action="{{ route('gerante.clients.index') }}" method="GET" class="w-full sm:w-96 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom, prénom, email, téléphone..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-hidden transition-shadow">
                <i class="fa-solid fa-search absolute left-3.5 top-2.5 sm:top-3 text-slate-400 text-xs"></i>
            </form>
            <span class="text-xs font-semibold text-slate-500">
                Total : <span class="font-bold text-slate-800">{{ $clients->total() }} client(s)</span>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500">Client</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500">Contact</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-center">Commandes</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-right">Total dépensé</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-center">Inscrit le</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clients as $client)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-black text-sm shrink-0">
                                        {{ strtoupper(substr($client->prenom ?? $client->nom ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">{{ $client->prenom }} {{ $client->nom }}</p>
                                        <p class="text-xs text-slate-400">ID: #{{ $client->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="fa-solid fa-envelope text-slate-400"></i> {{ $client->email }}
                                </p>
                                @if($client->tel)
                                    <p class="text-xs text-slate-500 flex items-center gap-1.5 mt-1">
                                        <i class="fa-solid fa-phone text-slate-400"></i> {{ $client->tel }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $client->commandes_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $client->commandes_count }} commande(s)
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <span class="text-sm font-black text-slate-900">
                                    {{ number_format($client->commandes_sum_montant_c ?? 0, 0, ',', ' ') }} FCFA
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center text-xs text-slate-500">
                                {{ $client->created_at ? $client->created_at->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('gerante.clients.show', $client->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-eye"></i> Détails
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                                <i class="fa-solid fa-users-slash text-2xl text-slate-300 mb-2 block"></i>
                                Aucun client inscrit trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $clients->links() }}
        </div>
    </div>

@endsection
