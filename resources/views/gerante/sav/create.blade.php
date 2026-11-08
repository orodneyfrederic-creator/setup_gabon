@extends('layouts.app')

@section('content')

    <!-- EN-TÊTE DE PAGE -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Nouveau ticket SAV</h1>
            <p class="mt-1 text-slate-500">Enregistrer une demande de retour, garantie ou réparation</p>
        </div>
        <div>
            <a href="{{ route('gerante.sav.index') }}" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-bold rounded-xl transition-colors shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> <span>Retour aux tickets</span>
            </a>
        </div>
    </div>

    <!-- FORMULAIRE CRÉATION TICKET SAV -->
    <div class="max-w-2xl mx-auto bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
        <form action="{{ route('gerante.sav.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nom complet du client <span class="text-rose-500">*</span></label>
                    <input type="text" name="client_nom" value="{{ old('client_nom') }}" required placeholder="ex: Marc Obame" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 outline-hidden">
                    @error('client_nom') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Numéro de téléphone <span class="text-rose-500">*</span></label>
                    <input type="text" name="client_tel" value="{{ old('client_tel') }}" required placeholder="ex: +241 66 00 00 00" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 outline-hidden">
                    @error('client_tel') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Désignation du produit <span class="text-rose-500">*</span></label>
                <input type="text" name="designation_produit" value="{{ old('designation_produit') }}" required placeholder="ex: PC Portable HP Victus 16" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 outline-hidden">
                @error('designation_produit') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Description de la panne / problème <span class="text-rose-500">*</span></label>
                <textarea name="description_panne" rows="3" required placeholder="Décrivez le problème constaté par le client..." class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 outline-hidden">{{ old('description_panne') }}</textarea>
                @error('description_panne') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Date de retour prévue</label>
                    <input type="date" name="date_retour_prevue" value="{{ old('date_retour_prevue') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Notes complémentaires</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Accessoires laissés, état physique..." class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 outline-hidden">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('gerante.sav.index') }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs sm:text-sm font-bold hover:bg-slate-50 transition-colors">Annuler</a>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs sm:text-sm font-bold transition-colors shadow-xs">
                    Enregistrer le ticket
                </button>
            </div>
        </form>
    </div>

@endsection
