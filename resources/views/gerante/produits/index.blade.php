@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Produits</h1>
            <p class="mt-1 text-slate-500">Catalogue des produits de la boutique</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('gerante.produits.create') }}" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold rounded-xl transition-colors shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nouveau produit
            </a>
            <a href="{{ route('gerante.dashboard') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden mb-6">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <form action="{{ route('gerante.produits.index') }}" method="GET" class="w-full sm:w-96 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par désignation ou référence..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-hidden transition-shadow">
                <i class="fa-solid fa-search absolute left-3.5 top-2.5 text-slate-400"></i>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500">Image</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500">Réf. / désignation</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500">Catégorie / marque</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-right">Prix</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-center">Stock</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($produits as $produit)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                @if($produit->image)
                                    <img src="{{ Storage::url($produit->image) }}" alt="{{ $produit->designation }}" class="w-12 h-12 rounded-lg object-cover border border-slate-200">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs font-semibold text-slate-500 mb-0.5">{{ $produit->reference }}</p>
                                <p class="text-sm font-bold text-slate-800">{{ $produit->designation }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 mb-1">
                                    {{ $produit->category->nom ?? 'N/A' }}
                                </span>
                                <br>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $produit->marque->nom ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="chiffres text-sm font-black text-slate-900 whitespace-nowrap">{{ number_format($produit->prix_unitaire, 0, ',', ' ') }} FCFA</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($produit->quantite <= $produit->seuil_alerte)
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $produit->quantite }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        {{ $produit->quantite }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('gerante.produits.edit', $produit) }}" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-violet-100 hover:text-violet-700 flex items-center justify-center transition-colors" title="Modifier">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('gerante.produits.destroy', $produit) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-rose-100 hover:text-rose-700 flex items-center justify-center transition-colors" title="Supprimer">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
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
