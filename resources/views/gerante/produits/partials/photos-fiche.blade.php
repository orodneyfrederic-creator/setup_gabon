{{-- Photos supplémentaires et fiche technique : partagé par la création et la modification d'un produit --}}
@php
    $produitEdite = $produit ?? null;
    $lignesInitiales = old('caracteristiques', $produitEdite?->caracteristiques ?? []);
    if (empty($lignesInitiales)) {
        $lignesInitiales = [['libelle' => '', 'valeur' => '']];
    }
@endphp

<div class="mb-8 pt-6 border-t border-slate-100">
    <h2 class="text-base font-bold text-slate-900">Photos supplémentaires</h2>
    <p class="text-sm text-slate-500 mt-1">Montrez le produit sous plusieurs angles : face, dos, connectique, accessoires inclus. Jusqu'à 8 photos, 10 Mo chacune.</p>

    @if($produitEdite && $produitEdite->images->isNotEmpty())
        <ul class="mt-4 grid grid-cols-3 sm:grid-cols-6 gap-3">
            @foreach($produitEdite->images as $photo)
                <li>
                    <label class="block cursor-pointer group">
                        <img src="{{ asset('storage/'.$photo->chemin) }}" alt="Photo {{ $loop->iteration }}" class="w-full aspect-square object-cover rounded-lg border border-slate-200 group-has-[:checked]:opacity-40">
                        <span class="mt-1.5 flex items-center gap-1.5 text-xs text-slate-600">
                            <input type="checkbox" name="supprimer_photos[]" value="{{ $photo->id }}" class="rounded-sm border-slate-300 text-rose-600 focus:ring-rose-500">
                            Supprimer
                        </span>
                    </label>
                </li>
            @endforeach
        </ul>
    @endif

    <input type="file" name="photos[]" accept="image/*" multiple
           class="mt-4 w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
    @error('photos') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('photos.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-8 pt-6 border-t border-slate-100" x-data="{ lignes: @js(array_values($lignesInitiales)) }">
    <h2 class="text-base font-bold text-slate-900">Fiche technique</h2>
    <p class="text-sm text-slate-500 mt-1">Une ligne par caractéristique. Exemples : Processeur / Intel Core i5-1135G7 · Mémoire vive / 8 Go · Stockage / SSD 256 Go · Écran / 14 pouces Full HD · État / Reconditionné.</p>

    <div class="mt-4 space-y-2">
        <template x-for="(ligne, index) in lignes" :key="index">
            <div class="flex gap-2 items-start">
                <input type="text" :name="`caracteristiques[${index}][libelle]`" x-model="ligne.libelle" placeholder="Caractéristique" maxlength="60" aria-label="Caractéristique"
                       class="w-2/5 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-hidden">
                <input type="text" :name="`caracteristiques[${index}][valeur]`" x-model="ligne.valeur" placeholder="Valeur" maxlength="255" aria-label="Valeur"
                       class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-hidden">
                <button type="button" @click="lignes.splice(index, 1); if (lignes.length === 0) lignes.push({ libelle: '', valeur: '' })"
                        class="w-9 h-9 shrink-0 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center" aria-label="Retirer la ligne">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </template>
    </div>
    <button type="button" @click="lignes.push({ libelle: '', valeur: '' })" x-show="lignes.length < 30"
            class="mt-3 h-9 px-3 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 inline-flex items-center gap-2">
        <i class="fa-solid fa-plus text-xs"></i> Ajouter une caractéristique
    </button>
    @error('caracteristiques') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
