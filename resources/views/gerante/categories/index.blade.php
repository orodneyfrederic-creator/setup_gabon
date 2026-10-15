@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-tags text-amber-600"></i>
                <span>Gestion des Catégories</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Organisez vos produits par catégories</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('gerante.categories.create') }}" class="flex-1 sm:flex-none justify-center px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs sm:text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> <span>Nouvelle Catégorie</span>
            </a>
            <a href="{{ route('gerante.dashboard') }}" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> <span>Dashboard</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-xmark text-red-600"></i>
            <p class="text-sm font-semibold">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <form action="{{ route('gerante.categories.index') }}" method="GET" class="w-full sm:w-96 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une catégorie..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-shadow">
                <i class="fa-solid fa-search absolute left-3.5 top-2.5 text-slate-400"></i>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Nom de la catégorie</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Produits associés</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-4">
                                <p class="text-sm font-bold text-slate-800">{{ $category->nom }}</p>
                                @if($category->description)
                                    <p class="text-xs text-slate-500 mt-1 truncate max-w-xs">{{ $category->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $category->produits_count > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $category->produits_count }} produit(s)
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('gerante.categories.edit', $category) }}" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-amber-100 hover:text-amber-700 flex items-center justify-center transition-colors" title="Modifier">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('gerante.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? {{ $category->produits_count > 0 ? "Attention : " . $category->produits_count . " produit(s) y sont associés." : "" }}');">
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
                            <td colspan="3" class="px-4 py-8 text-center text-slate-500 text-sm">
                                Aucune catégorie trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            @if(method_exists($categories, 'links'))
                {{ $categories->links() }}
            @endif
        </div>
    </div>

@endsection
