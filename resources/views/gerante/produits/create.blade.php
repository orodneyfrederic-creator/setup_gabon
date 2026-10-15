@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-box-open text-violet-700"></i>
                <span>Ajouter un produit</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Créer une nouvelle fiche produit dans le catalogue</p>
        </div>
        <div>
            <a href="{{ route('gerante.produits.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6 max-w-4xl mx-auto">
        <form action="{{ route('gerante.produits.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Référence -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Référence *</label>
                    <input type="text" name="reference" value="{{ old('reference') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                    @error('reference') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Désignation -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Désignation *</label>
                    <input type="text" name="designation" value="{{ old('designation') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                    @error('designation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Catégorie -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Catégorie *</label>
                    <select name="id_cat" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('id_cat') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                        @endforeach
                    </select>
                    @error('id_cat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Marque -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Marque *</label>
                    <select name="id_m" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                        <option value="">Sélectionner une marque</option>
                        @foreach($marques as $marque)
                            <option value="{{ $marque->id }}" {{ old('id_m') == $marque->id ? 'selected' : '' }}>{{ $marque->nom }}</option>
                        @endforeach
                    </select>
                    @error('id_m') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Prix -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Prix Unitaire (FCFA) *</label>
                    <input type="number" step="0.01" name="prix_unitaire" value="{{ old('prix_unitaire') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                    @error('prix_unitaire') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Quantité -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Stock Initial *</label>
                        <input type="number" name="quantite" value="{{ old('quantite', 0) }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                        @error('quantite') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Seuil d'alerte</label>
                        <input type="number" name="seuil_alerte" value="{{ old('seuil_alerte', 5) }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                        @error('seuil_alerte') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Image & Vidéo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Image du produit</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 transition-colors">
                    <p class="text-[11px] text-slate-400 mt-1">Formats : JPG, PNG, WEBP (Max 2 Mo)</p>
                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Vidéo de présentation (optionnelle)</label>
                    <input type="file" name="video" accept="video/mp4,video/quicktime,video/x-msvideo,video/webm" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 transition-colors">
                    <p class="text-[11px] text-slate-400 mt-1">Formats : MP4, MOV, AVI, WEBM (Max 50 Mo)</p>
                    @error('video') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Enregistrer le produit
                </button>
            </div>
        </form>
    </div>

@endsection
