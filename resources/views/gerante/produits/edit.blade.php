@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-pen text-violet-700"></i>
                <span>Modifier le produit</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">{{ $produit->reference }} - {{ $produit->designation }}</p>
        </div>
        <div>
            <a href="{{ route('gerante.produits.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6 max-w-4xl mx-auto">
        <form action="{{ route('gerante.produits.update', $produit) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Référence -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Référence *</label>
                    <input type="text" name="reference" value="{{ old('reference', $produit->reference) }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                    @error('reference') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Désignation -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Désignation *</label>
                    <input type="text" name="designation" value="{{ old('designation', $produit->designation) }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                    @error('designation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Catégorie -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Catégorie *</label>
                    <select name="id_cat" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('id_cat', $produit->id_cat) == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
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
                            <option value="{{ $marque->id }}" {{ old('id_m', $produit->id_m) == $marque->id ? 'selected' : '' }}>{{ $marque->nom }}</option>
                        @endforeach
                    </select>
                    @error('id_m') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Prix -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Prix Unitaire (FCFA) *</label>
                    <input type="number" step="0.01" name="prix_unitaire" value="{{ old('prix_unitaire', $produit->prix_unitaire) }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                    @error('prix_unitaire') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Seuil d'alerte -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Seuil d'alerte</label>
                    <input type="number" name="seuil_alerte" value="{{ old('seuil_alerte', $produit->seuil_alerte) }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">
                    @error('seuil_alerte') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-none transition-all">{{ old('description', $produit->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Image & Vidéo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Image du produit</label>
                    @if($produit->image)
                        <div class="mb-3">
                            <img src="{{ Storage::url($produit->image) }}" class="w-24 h-24 object-cover rounded-xl border border-slate-200">
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 transition-colors">
                    <p class="text-xs text-slate-400 mt-1">Laissez vide pour conserver l'image actuelle.</p>
                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Vidéo de présentation</label>
                    @if($produit->video)
                        <div class="mb-3">
                            <video src="{{ Storage::url($produit->video) }}" controls class="w-full max-h-32 rounded-xl border border-slate-200 bg-black"></video>
                            <p class="text-[10px] text-emerald-600 font-semibold mt-1"><i class="fa-solid fa-circle-check"></i> Vidéo actuelle enregistrée</p>
                        </div>
                    @endif
                    <input type="file" name="video" accept="video/mp4,video/quicktime,video/x-msvideo,video/webm" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 transition-colors">
                    <p class="text-xs text-slate-400 mt-1">Laissez vide pour conserver la vidéo actuelle (Max 50 Mo).</p>
                    @error('video') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Mettre à jour le produit
                </button>
            </div>
        </form>
    </div>

@endsection
