@extends('layouts.app')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-tags text-amber-600"></i>
                <span>Nouvelle Catégorie</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Ajouter une nouvelle catégorie de produits</p>
        </div>
        <div>
            <a href="{{ route('gerante.categories.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
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
            <form action="{{ route('gerante.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nom de la catégorie *</label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Description (Optionnel)</label>
                        <textarea name="description" rows="4" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:bg-white outline-none transition-all">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Image de la catégorie (Optionnel)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-xl bg-slate-50 hover:border-amber-400 transition-colors cursor-pointer" onclick="document.getElementById('image_input').click()">
                            <div class="space-y-2 text-center">
                                <div id="image_preview_wrapper" class="hidden">
                                    <img id="image_preview" src="" alt="" class="mx-auto h-32 w-auto object-contain rounded-lg mb-2">
                                </div>
                                <i id="upload_icon" class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400"></i>
                                <div class="text-sm text-slate-600">
                                    <span class="font-semibold text-amber-600">Cliquer pour uploader</span> ou glisser-déposer
                                </div>
                                <p class="text-xs text-slate-500">PNG, JPG, GIF, WEBP jusqu'à 2MB</p>
                            </div>
                        </div>
                        <input id="image_input" type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this, 'image_preview', 'image_preview_wrapper', 'upload_icon')">
                    </div>
                </div>

                <div class="mt-8 pt-5 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('gerante.categories.index') }}" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
