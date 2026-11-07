@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Historique du stock</h1>
            <p class="mt-1 text-slate-500">Produit : {{ $produit->reference }} - {{ $produit->designation }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('gerante.stock.create_mouvement', $produit) }}" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus-minus"></i> Nouveau mouvement
            </a>
            <a href="{{ route('gerante.stock.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Informations produit -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 sticky top-6">
                <div class="text-center mb-4">
                    @if($produit->image)
                        <img src="{{ Storage::url($produit->image) }}" class="w-24 h-24 mx-auto object-cover rounded-xl border border-slate-200 mb-3">
                    @else
                        <div class="w-24 h-24 mx-auto rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-3 text-2xl">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    @endif
                    <h2 class="font-bold text-slate-800 text-sm">{{ $produit->designation }}</h2>
                </div>
                
                <div class="space-y-2 pt-4 border-t border-slate-100">
                    <div class="flex justify-between items-center bg-slate-50 p-2 rounded-lg">
                        <span class="text-xs text-slate-500 font-semibold">Stock Actuel</span>
                        <span class="text-sm font-black {{ $produit->quantite <= $produit->seuil_alerte ? 'text-red-600' : 'text-emerald-600' }}">{{ $produit->quantite }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2">
                        <span class="text-xs text-slate-500">Seuil d'alerte</span>
                        <span class="text-xs font-bold text-slate-700">{{ $produit->seuil_alerte }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau historique -->
        <div class="lg:col-span-3">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h2 class="font-bold text-slate-800 text-sm">Liste des mouvements</h2>
                    <span class="text-xs font-semibold text-slate-500 bg-white px-2 py-1 rounded-md border border-slate-200">{{ $mouvements->total() }} enregistrements</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-100">
                                <th class="px-4 py-3 text-xs font-bold text-slate-500">Date</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500">Type</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 text-center">Quantité</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500">Motif</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500">Effectué par</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($mouvements as $mvt)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm font-bold text-slate-800">{{ $mvt->created_at->format('d/m/Y') }}</p>
                                        <p class="text-xs text-slate-500">{{ $mvt->created_at->format('H:i') }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($mvt->type_mouvement === 'entree')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-700">
                                                <i class="fa-solid fa-arrow-right-to-bracket"></i> Entrée
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-rose-100 text-rose-700">
                                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Sortie
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm font-black {{ $mvt->type_mouvement === 'entree' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $mvt->type_mouvement === 'entree' ? '+' : '-' }}{{ $mvt->quantite }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm text-slate-700">{{ $mvt->motif }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-medium text-slate-800">{{ $mvt->user->prenom }} {{ $mvt->user->nom }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm">
                                        Aucun mouvement enregistré pour ce produit.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100">
                    {{ $mouvements->links() }}
                </div>
            </div>
        </div>

    </div>

@endsection
