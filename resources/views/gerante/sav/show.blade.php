@extends('layouts.app')

@section('content')

    <!-- EN-TÊTE DE PAGE -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Ticket SAV : {{ $sav->reference }}</h1>
            <p class="mt-1 text-slate-500">Détails du dossier et suivi de la réparation / garantie</p>
        </div>
        <div>
            <a href="{{ route('gerante.sav.index') }}" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> <span>Retour aux tickets</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- FICHE DU TICKET -->
        <div class="md:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Produit concerné</span>
                    <h2 class="text-lg font-black text-slate-900 mt-0.5">{{ $sav->designation_produit }}</h2>
                </div>
                <div>
                    @php
                        $statusClasses = [
                            'ouvert' => 'bg-amber-100 text-amber-800',
                            'en_cours' => 'bg-blue-100 text-blue-800',
                            'resolu' => 'bg-emerald-100 text-emerald-800',
                            'clos' => 'bg-slate-100 text-slate-600',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses[$sav->statut] ?? 'bg-slate-100 text-slate-600' }}">
                        Statut : {{ strtoupper($sav->statut) }}
                    </span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description de la panne</h3>
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl text-xs sm:text-sm text-slate-800 whitespace-pre-line leading-relaxed">
                        {{ $sav->description_panne }}
                    </div>
                </div>

                @if($sav->notes)
                <div>
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Notes complémentaires</h3>
                    <p class="text-xs text-slate-600 italic bg-amber-50/60 p-3 rounded-xl border border-amber-100">{{ $sav->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- MISE À JOUR STATUT & INFOS CLIENT -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-user text-blue-600"></i> Informations Client
                </h3>
                <div class="space-y-2 text-xs">
                    <p class="text-slate-600"><span class="font-semibold text-slate-800">Nom :</span> {{ $sav->client_nom }}</p>
                    <p class="text-slate-600"><span class="font-semibold text-slate-800">Téléphone :</span> {{ $sav->client_tel }}</p>
                    <p class="text-slate-600"><span class="font-semibold text-slate-800">Dossier créé le :</span> {{ $sav->created_at->format('d/m/Y à H:i') }}</p>
                    <p class="text-slate-600"><span class="font-semibold text-slate-800">Retour prévu :</span> {{ $sav->date_retour_prevue ? $sav->date_retour_prevue->format('d/m/Y') : 'Non renseignée' }}</p>
                </div>
            </div>

            <!-- MISE À JOUR STATUT -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-blue-600"></i> Mettre à jour le statut
                </h3>
                <form action="{{ route('gerante.sav.update', $sav) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nouveau statut</label>
                        <select name="statut" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="ouvert" {{ $sav->statut === 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                            <option value="en_cours" {{ $sav->statut === 'en_cours' ? 'selected' : '' }}>En cours de réparation</option>
                            <option value="resolu" {{ $sav->statut === 'resolu' ? 'selected' : '' }}>Résolu / Prêt</option>
                            <option value="clos" {{ $sav->statut === 'clos' ? 'selected' : '' }}>Clos / Restitué</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mettre à jour les notes</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 outline-none">{{ old('notes', $sav->notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-colors shadow-sm">
                        Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>

    </div>

@endsection
