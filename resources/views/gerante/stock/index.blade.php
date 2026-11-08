@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Stock</h1>
            <p class="mt-1 text-slate-500">Quantités en boutique et réapprovisionnement.</p>
        </div>
    </div>

    @if($alertesStock > 0)
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
        <div>
            <p class="text-sm font-semibold">{{ $alertesStock }} {{ $alertesStock > 1 ? 'produits sont' : 'produit est' }} à réapprovisionner.</p>
            <p class="text-xs">Ils sont signalés dans la colonne « État ».</p>
        </div>
    </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden mb-6">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <form action="{{ route('gerante.stock.index') }}" method="GET" class="w-full sm:w-96 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un produit..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-hidden transition-shadow">
                <i class="fa-solid fa-search absolute left-3.5 top-2.5 text-slate-400"></i>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500">Réf. / désignation</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-center whitespace-nowrap">En stock</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-center whitespace-nowrap">Seuil d\'alerte</th>
                        @if($alertesStock > 0)
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-center">État</th>
                        @endif
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($produits as $produit)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-xs font-semibold text-slate-500 mb-0.5">{{ $produit->reference }}</p>
                                <p class="text-sm font-bold text-slate-800">{{ $produit->designation }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-black {{ $produit->quantite <= $produit->seuil_alerte ? 'text-red-600' : 'text-slate-900' }}">
                                    {{ $produit->quantite }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs font-semibold text-slate-500">{{ $produit->seuil_alerte }}</span>
                            </td>
                            @if($alertesStock > 0)
                            <td class="px-4 py-3 text-center">
                                @if($produit->quantite === 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-rose-100 text-rose-700">Épuisé</span>
                                @elseif($produit->quantite <= $produit->seuil_alerte)
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-amber-100 text-amber-900">À réapprovisionner</span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            @endif
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('gerante.stock.create_mouvement', $produit) }}" class="px-3 py-1.5 rounded-lg bg-violet-50 text-violet-700 hover:bg-violet-600 hover:text-white flex items-center gap-1.5 text-xs font-bold transition-colors">
                                        <i class="fa-solid fa-plus-minus"></i> Mouvement
                                    </a>
                                    <a href="{{ route('gerante.stock.historique', $produit) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center gap-1.5 text-xs font-bold transition-colors">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Historique
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm">
                                Aucun produit trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $produits->links() }}
        </div>
    </div>

@endsection
