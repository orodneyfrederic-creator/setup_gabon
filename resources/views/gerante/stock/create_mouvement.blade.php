@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Nouveau mouvement de stock</h1>
            <p class="mt-1 text-slate-500">Produit : {{ $produit->reference }} - {{ $produit->designation }}</p>
        </div>
        <div>
            <a href="{{ route('gerante.stock.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour au stock
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
            <p class="text-sm font-semibold">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Informations actuelles -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sticky top-6">
                <div class="text-center mb-4">
                    @if($produit->image)
                        <img src="{{ Storage::url($produit->image) }}" class="w-32 h-32 mx-auto object-cover rounded-xl border border-slate-200 mb-3">
                    @else
                        <div class="w-32 h-32 mx-auto rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-3 text-3xl">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    @endif
                    <h2 class="font-bold text-slate-800">{{ $produit->designation }}</h2>
                    <p class="text-xs text-slate-500">Réf : {{ $produit->reference }}</p>
                </div>
                
                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Stock Actuel</span>
                        <span class="text-lg font-black {{ $produit->quantite <= $produit->seuil_alerte ? 'text-red-600' : 'text-slate-900' }}">{{ $produit->quantite }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Seuil d'alerte</span>
                        <span class="text-sm font-bold text-slate-700">{{ $produit->seuil_alerte }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Prix unitaire</span>
                        <span class="text-sm font-bold text-slate-700">{{ number_format($produit->prix_unitaire, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de mouvement -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <form action="{{ route('gerante.stock.store_mouvement', $produit) }}" method="POST">
                    @csrf
                    
                    <!-- Type de mouvement -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-3">Type de mouvement *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="entree" class="peer sr-only" {{ old('type') == 'entree' ? 'checked' : '' }} required>
                                <div class="px-4 py-3 rounded-xl border-2 border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 text-slate-600 peer-checked:text-emerald-700 flex flex-col items-center gap-2 transition-all">
                                    <i class="fa-solid fa-arrow-right-to-bracket text-2xl"></i>
                                    <span class="font-bold text-sm">Entrée en stock</span>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="sortie" class="peer sr-only" {{ old('type') == 'sortie' ? 'checked' : '' }}>
                                <div class="px-4 py-3 rounded-xl border-2 border-slate-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 text-slate-600 peer-checked:text-rose-700 flex flex-col items-center gap-2 transition-all">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-2xl"></i>
                                    <span class="font-bold text-sm">Sortie de stock</span>
                                </div>
                            </label>
                        </div>
                        @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Quantité -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Quantité *</label>
                        <input type="number" name="quantite" value="{{ old('quantite', 1) }}" min="1" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-lg font-bold text-center focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                        @error('quantite') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Motif -->
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Motif / Justification *</label>
                        <input type="text" name="motif" value="{{ old('motif') }}" placeholder="Ex: Réapprovisionnement, Retour SAV, Casse..." required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                        <p class="text-xs text-slate-400 mt-1">Sera enregistré dans l'historique des mouvements.</p>
                        @error('motif') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-check"></i> Valider le mouvement
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
