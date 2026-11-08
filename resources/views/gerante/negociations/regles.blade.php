@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- EN-TÊTE --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Règles de négociation</h1>
            <p class="text-xs text-slate-500 mt-1">
                Configurez l'éligibilité et le pourcentage de remise maximale autorisé par catégorie.
            </p>
        </div>
        <a href="{{ route('gerante.negociations.index') }}" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Retour</span>
        </a>
    </div>

    {{-- EXPLICATION DES RÈGLES --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-xs text-blue-900 space-y-2">
        <p class="font-bold flex items-center gap-2 text-blue-950">
            <i class="fa-solid fa-circle-info text-blue-600"></i>
            <span>Fonctionnement du moteur déterministe</span>
        </p>
        <ul class="list-disc list-inside space-y-1 text-blue-800">
            <li>Le <strong>prix plancher</strong> est calculé strictement en arithmétique entière avec arrondi au bénéfice du magasin : <code>ceil(P × (100 - remise) / 100)</code>.</li>
            <li>Le prix plancher et le prix d'achat ne sont <strong>JAMAIS</strong> transmis au navigateur du client.</li>
            <li>Si un produit possède un réglage spécifique dans sa fiche, il prévaut sur la règle de sa catégorie.</li>
            <li>Garde-fou automatique : un plancher ne peut jamais descendre en dessous du prix d'achat renseigné.</li>
        </ul>
    </div>

    {{-- FORMULAIRE DES CATÉGORIES --}}
    <form action="{{ route('gerante.negociations.regles.update') }}" method="POST" class="space-y-4">
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold text-[11px]">
                        <th class="py-3 px-4">Catégorie</th>
                        <th class="py-3 px-4 text-center">Négociation activée</th>
                        <th class="py-3 px-4 text-center">Remise max. (%)</th>
                        <th class="py-3 px-4 text-center">Exemple sur 100 000 FCFA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" x-data>
                    @forelse($categories as $index => $cat)
                        <tr class="hover:bg-slate-50/50 transition-colors" x-data="{ 
                            active: {{ $cat->negociable ? 'true' : 'false' }},
                            remise: {{ $cat->remise_max_pct ?? 2 }}
                        }">
                            <td class="py-3.5 px-4">
                                <input type="hidden" name="categories[{{ $index }}][id]" value="{{ $cat->id }}">
                                <p class="font-bold text-slate-900">{{ $cat->nom }}</p>
                                <p class="text-[11px] text-slate-400">{{ $cat->produits()->count() }} produit(s) associé(s)</p>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           name="categories[{{ $index }}][negociable]" 
                                           value="1" 
                                           x-model="active"
                                           class="sr-only peer">
                                    <div class="relative w-9 h-5 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                                </label>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <input type="number" 
                                           name="categories[{{ $index }}][remise_max_pct]" 
                                           min="0" max="50" 
                                           x-model.number="remise"
                                           :disabled="!active"
                                           class="w-16 px-2 py-1.5 border border-slate-300 rounded-lg text-xs font-bold text-center focus:outline-hidden focus:border-blue-500 disabled:bg-slate-100 disabled:text-slate-400">
                                    <span class="font-bold text-slate-500">%</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <template x-if="active">
                                    <span class="font-bold text-slate-700" x-text="new Intl.NumberFormat('fr-FR').format(Math.ceil(100000 * (100 - remise) / 100)) + ' FCFA'"></span>
                                </template>
                                <template x-if="!active">
                                    <span class="text-slate-400 italic">Prix fixe</span>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400">Aucune catégorie trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Enregistrer les règles</span>
            </button>
        </div>
    </form>

</div>
@endsection
