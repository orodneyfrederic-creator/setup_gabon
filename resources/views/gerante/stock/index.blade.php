@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-boxes-packing text-violet-700"></i>
                <span>Gestion du Stock</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Suivi des quantités et réapprovisionnement</p>
        </div>
        <div>
            <a href="{{ route('gerante.dashboard') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if($alertesStock > 0)
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl"></i>
        <div>
            <p class="text-sm font-bold">Attention, {{ $alertesStock }} produit(s) en stock critique !</p>
            <p class="text-xs">Veuillez vérifier les produits signalés en rouge ci-dessous.</p>
        </div>
    </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <form action="{{ route('gerante.stock.index') }}" method="GET" class="w-full sm:w-96 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un produit..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition-shadow">
                <i class="fa-solid fa-search absolute left-3.5 top-2.5 text-slate-400"></i>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Réf / Désignation</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">En Stock</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Seuil Alerte</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">État</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
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
                            <td class="px-4 py-3 text-center">
                                @if($produit->quantite <= $produit->seuil_alerte)
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700">
                                        Critique
                                    </span>
                                @elseif($produit->quantite <= ($produit->seuil_alerte * 2))
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700">
                                        Attention
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">
                                        Normal
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('gerante.stock.create_mouvement', $produit) }}" class="px-3 py-1.5 rounded-lg bg-violet-50 text-violet-700 hover:bg-violet-600 hover:text-white flex items-center gap-1.5 text-xs font-bold transition-colors">
                                        <i class="fa-solid fa-plus-minus"></i> Mouvement
                                    </a>
                                    <a href="{{ route('gerante.stock.historique', $produit) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center gap-1.5 text-xs font-bold transition-colors">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Histo
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
