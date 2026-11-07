@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Modifier la marque</h1>
            <p class="mt-1 text-slate-500">Mise à jour de : {{ $marque->nom }}</p>
        </div>
        <div>
            <a href="{{ route('gerante.marques.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
            <p class="font-bold mb-2"><i class="fa-solid fa-triangle-exclamation"></i> Veuillez corriger les erreurs suivantes :</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden max-w-2xl mx-auto">
        <div class="p-6">
            <form action="{{ route('gerante.marques.update', $marque) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nom de la marque *</label>
                        <input type="text" name="nom" value="{{ old('nom', $marque->nom) }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Logo (Optionnel)</label>
                        @if($marque->logo)
                            <div class="mb-3 flex items-center gap-3">
                                <img src="{{ Storage::url($marque->logo) }}" alt="{{ $marque->nom }}" class="w-16 h-16 object-contain rounded-xl border border-slate-200 bg-white p-2">
                                <span class="text-xs text-slate-500">Logo actuel</span>
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
                        <p class="text-xs text-slate-400 mt-1">Laissez vide pour conserver le logo actuel.</p>
                    </div>
                </div>

                <div class="mt-8 pt-5 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('gerante.marques.index') }}" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
