@extends('layouts.app')

@section('content')

    <!-- EN-TÊTE DE PAGE -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-headset text-blue-600"></i>
                <span>Tickets SAV (Service Après-Vente)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Gérez les retours, réparations et garanties de vos clients</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('gerante.sav.create') }}" class="flex-1 sm:flex-none justify-center px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> <span>Nouveau Ticket SAV</span>
            </a>
            <a href="{{ route('gerante.dashboard') }}" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- RECHERCHE & FILTRES -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <form action="{{ route('gerante.sav.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par référence, client, produit..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow">
                    <i class="fa-solid fa-search absolute left-3.5 top-2.5 sm:top-3 text-slate-400 text-xs"></i>
                </div>
                <select name="statut" onchange="this.form.submit()" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">Tous les statuts</option>
                    <option value="ouvert" {{ request('statut') === 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                    <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="resolu" {{ request('statut') === 'resolu' ? 'selected' : '' }}>Résolu</option>
                    <option value="clos" {{ request('statut') === 'clos' ? 'selected' : '' }}>Clos</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Référence</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Client & Contact</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Produit</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Statut</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Retour Prévu</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-4 text-xs font-bold text-slate-900">
                                {{ $ticket->reference }}
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-bold text-slate-800">{{ $ticket->client_nom }}</p>
                                <p class="text-xs text-slate-500"><i class="fa-solid fa-phone text-slate-400 mr-1"></i>{{ $ticket->client_tel }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-xs font-bold text-slate-800">{{ $ticket->designation_produit }}</p>
                                <p class="text-xs text-slate-500 truncate max-w-xs">{{ $ticket->description_panne }}</p>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @php
                                    $statusClasses = [
                                        'ouvert' => 'bg-amber-100 text-amber-800',
                                        'en_cours' => 'bg-blue-100 text-blue-800',
                                        'resolu' => 'bg-emerald-100 text-emerald-800',
                                        'clos' => 'bg-slate-100 text-slate-600',
                                    ];
                                    $statusLabels = [
                                        'ouvert' => 'Ouvert',
                                        'en_cours' => 'En cours',
                                        'resolu' => 'Résolu',
                                        'clos' => 'Clos',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $statusClasses[$ticket->statut] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $statusLabels[$ticket->statut] ?? $ticket->statut }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center text-xs text-slate-600">
                                {{ $ticket->date_retour_prevue ? $ticket->date_retour_prevue->format('d/m/Y') : 'Non définie' }}
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('gerante.sav.show', $ticket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-eye"></i> Gérer
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                                <i class="fa-solid fa-headset text-2xl text-slate-300 mb-2 block"></i>
                                Aucun ticket SAV enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $tickets->links() }}
        </div>
    </div>

@endsection
