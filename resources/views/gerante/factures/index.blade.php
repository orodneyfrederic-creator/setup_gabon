@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Factures</h1>
            <p class="mt-1 text-slate-500">Suivi des factures générées à partir des devis</p>
        </div>
        <div>
            <a href="{{ route('gerante.devis.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour aux devis
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <form action="{{ route('gerante.factures.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher (Réf, Client, Entreprise)..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow">
                    <i class="fa-solid fa-search absolute left-3.5 top-2.5 text-slate-400"></i>
                </div>
                <select name="statut" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="">Tous les statuts</option>
                    <option value="emise" {{ request('statut') === 'emise' ? 'selected' : '' }}>Impayée</option>
                    <option value="payee" {{ request('statut') === 'payee' ? 'selected' : '' }}>Payée</option>
                    <option value="annulee" {{ request('statut') === 'annulee' ? 'selected' : '' }}>Annulée</option>
                </select>
                <button type="submit" class="sr-only">Filtrer</button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500">Référence / date</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500">Client</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-right">Montant total</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-center">Statut</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($factures as $f)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-slate-800">{{ $f->numero }}</p>
                                <p class="text-xs text-slate-500">{{ $f->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-slate-800">{{ $f->client_nom }}</p>
                                @if($f->client_entreprise)
                                    <p class="text-xs text-slate-500"><i class="fa-solid fa-building mr-1"></i> {{ $f->client_entreprise }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm font-black text-slate-900">{{ number_format($f->montant_total, 0, ',', ' ') }} FCFA</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($f->statut === 'emise')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-rose-100 text-rose-800">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i> Impayée
                                    </span>
                                @elseif($f->statut === 'payee')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check-double mr-1"></i> Payée
                                    </span>
                                @elseif($f->statut === 'annulee')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-600">
                                        <i class="fa-solid fa-ban mr-1"></i> Annulée
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('gerante.factures.show', $f) }}" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-emerald-100 hover:text-emerald-700 flex items-center justify-center transition-colors" title="Voir les détails" aria-label="Voir les détails">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('gerante.factures.pdf', $f) }}?print=1" target="_blank" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-emerald-100 hover:text-emerald-700 flex items-center justify-center transition-colors" title="Imprimer le PDF" aria-label="Imprimer le PDF">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>
                                    <a href="{{ route('gerante.factures.pdf', $f) }}" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-colors" title="Télécharger PDF" aria-label="Télécharger PDF">
                                        <i class="fa-solid fa-download text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm">
                                Aucune facture trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $factures->links() }}
        </div>
    </div>

@endsection
