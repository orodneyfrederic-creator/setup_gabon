@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Modifier le produit</h1>
            <p class="mt-1 text-slate-500">{{ $produit->reference }} - {{ $produit->designation }}</p>
        </div>
        <div>
            <a href="{{ route('gerante.produits.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden mb-6 max-w-4xl mx-auto">
        <form action="{{ route('gerante.produits.update', $produit) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Référence -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Référence *</label>
                    <input type="text" name="reference" value="{{ old('reference', $produit->reference) }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-hidden transition-all">
                    @error('reference') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Désignation -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Désignation *</label>
                    <input type="text" name="designation" value="{{ old('designation', $produit->designation) }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-hidden transition-all">
                    @error('designation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Catégorie -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Catégorie *</label>
                    <select name="id_cat" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-hidden transition-all">
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
                    <select name="id_m" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-hidden transition-all">
                        <option value="">Sélectionner une marque</option>
                        @foreach($marques as $marque)
                            <option value="{{ $marque->id }}" {{ old('id_m', $produit->id_m) == $marque->id ? 'selected' : '' }}>{{ $marque->nom }}</option>
                        @endforeach
                    </select>
                    @error('id_m') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Prix de Vente -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Prix de Vente (FCFA) *</label>
                    <input type="number" step="1" name="prix_unitaire" value="{{ old('prix_unitaire', (int) $produit->prix_unitaire) }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-hidden transition-all">
                    @error('prix_unitaire') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Prix d'Achat (Garde-fou Marge) -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Prix d'Achat (FCFA) <span class="text-xs text-slate-400 font-normal">(garde-fou)</span></label>
                    <input type="number" step="1" name="prix_achat" value="{{ old('prix_achat', $produit->prix_achat) }}" placeholder="Ex: 380000" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-hidden transition-all">
                    @error('prix_achat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Seuil d'alerte -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Seuil d'alerte</label>
                    <input type="number" name="seuil_alerte" value="{{ old('seuil_alerte', $produit->seuil_alerte) }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-hidden transition-all">
                    @error('seuil_alerte') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Configuration Spécifique Négociation -->
            <div class="mb-6 p-4 bg-amber-50/60 border border-amber-200/80 rounded-2xl space-y-4" x-data="{ nego: '{{ old('negociable', is_null($produit->negociable) ? 'inherit' : ($produit->negociable ? '1' : '0')) }}' }">
                <div class="flex items-center justify-between border-b border-amber-200/60 pb-2">
                    <span class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-handshake-simple text-amber-600"></i>
                        <span>Paramètres de Négociation sur ce produit</span>
                    </span>
                    <span class="text-[11px] text-amber-700">Par défaut hérité de la catégorie</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Autoriser la négociation</label>
                        <select name="negociable" x-model="nego" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 outline-hidden">
                            <option value="inherit">Hériter de la catégorie</option>
                            <option value="1">Oui (Forcer activé)</option>
                            <option value="0">Non (Forcer désactivé)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Remise Max Spécifique (%)</label>
                        <input type="number" min="0" max="50" name="remise_max_pct" value="{{ old('remise_max_pct', $produit->remise_max_pct) }}" placeholder="Vide = selon catégorie" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nombre de tours max</label>
                        <input type="number" min="1" max="10" name="nb_tours_max" value="{{ old('nb_tours_max', $produit->nb_tours_max ?? 3) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 outline-hidden">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="decision_manuelle" name="decision_manuelle" value="1" {{ old('decision_manuelle', $produit->decision_manuelle) ? 'checked' : '' }} class="rounded-sm border-slate-300 text-amber-600 focus:ring-amber-500">
                    <label for="decision_manuelle" class="text-xs text-slate-700 font-semibold cursor-pointer">
                        Mode décision manuelle obligatoire (mettre toute offre en attente d'arbitrage gérante)
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 focus:bg-white outline-hidden transition-all">{{ old('description', $produit->description) }}</textarea>
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
                    <p class="text-xs text-slate-500 mt-1">Laissez vide pour conserver l'image actuelle.</p>
                    <p class="text-xs text-slate-600 mt-2 bg-blue-50 rounded-lg px-3 py-2 leading-relaxed"><strong class="font-semibold">Pour une belle fiche :</strong> produit posé sur un fond blanc ou uni, bien centré, à la lumière du jour, photo prise de face. Évitez le comptoir et les reflets.</p>
                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Vidéo de présentation</label>
                    @if($produit->video)
                        <div class="mb-3">
                            <video src="{{ Storage::url($produit->video) }}" controls class="w-full max-h-32 rounded-xl border border-slate-200 bg-black"></video>
                            <p class="text-[11px] text-emerald-600 font-semibold mt-1"><i class="fa-solid fa-circle-check"></i> Vidéo actuelle enregistrée</p>
                        </div>
                    @endif
                    <input type="file" name="video" accept="video/mp4,video/quicktime,video/x-msvideo,video/webm" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 transition-colors">
                    <p class="text-xs text-slate-400 mt-1">Laissez vide pour conserver la vidéo actuelle (max. 200 Mo).</p>
                    @error('video') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            @include('gerante.produits.partials.photos-fiche')

            <!-- Submit -->
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition-colors shadow-xs flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Mettre à jour le produit
                </button>
            </div>
        </form>
    </div>

@endsection
