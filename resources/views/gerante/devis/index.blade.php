@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Devis</h1>
            <p class="mt-1 text-slate-500">Créez et suivez les propositions commerciales</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('gerante.devis.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Créer un devis
            </a>
            <a href="{{ route('gerante.factures.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar"></i> Voir les factures
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
            <form action="{{ route('gerante.devis.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher (Réf, Client, Entreprise)..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow">
                    <i class="fa-solid fa-search absolute left-3.5 top-2.5 text-slate-400"></i>
                </div>
                <select name="statut" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Tous les statuts</option>
                    <option value="brouillon" {{ request('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="accepte" {{ request('statut') === 'accepte' ? 'selected' : '' }}>Accepté</option>
                    <option value="refuse" {{ request('statut') === 'refuse' ? 'selected' : '' }}>Refusé</option>
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
                    @forelse($devis as $d)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-slate-800">{{ $d->numero }}</p>
                                <p class="text-xs text-slate-500">{{ $d->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-slate-800">{{ $d->client_nom }}</p>
                                @if($d->client_entreprise)
                                    <p class="text-xs text-slate-500"><i class="fa-solid fa-building mr-1"></i> {{ $d->client_entreprise }}</p>
                                @endif
                                <p class="text-xs text-slate-400">{{ $d->client_telephone }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm font-black text-slate-900">{{ number_format($d->montant_total, 0, ',', ' ') }} FCFA</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($d->statut === 'brouillon')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-clock mr-1"></i> Brouillon
                                    </span>
                                @elseif($d->statut === 'accepte')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check mr-1"></i> Accepté
                                    </span>
                                @elseif($d->statut === 'refuse')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-rose-100 text-rose-800">
                                        <i class="fa-solid fa-xmark mr-1"></i> Refusé
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('gerante.devis.show', $d) }}" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-100 hover:text-blue-700 flex items-center justify-center transition-colors" title="Voir les détails" aria-label="Voir les détails">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('gerante.devis.pdf', $d) }}?print=1" target="_blank" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-100 hover:text-blue-700 flex items-center justify-center transition-colors" title="Imprimer le PDF" aria-label="Imprimer le PDF">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>
                                    <a href="{{ route('gerante.devis.pdf', $d) }}" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-colors" title="Télécharger PDF" aria-label="Télécharger PDF">
                                        <i class="fa-solid fa-download text-xs"></i>
                                    </a>
                                    @if($d->statut === 'brouillon')
                                    <form action="{{ route('gerante.devis.destroy', $d) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer ce devis ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-rose-100 hover:text-rose-700 flex items-center justify-center transition-colors" title="Supprimer">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm">
                                Aucun devis trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $devis->links() }}
        </div>
    </div>

@endsection
