@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Entête -->
    <div class="bg-gradient-to-r from-blue-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-blue-600/30 border border-blue-500/40 text-blue-300 flex items-center justify-center font-bold text-2xl shadow">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold">Mon Compte Personnel</h1>
                <p class="text-xs text-blue-200 mt-1">Gérez vos informations personnelles et vos préférences.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition-all shadow flex items-center gap-2">
                <i class="fa-solid fa-box-archive"></i>
                <span>Mes Commandes</span>
            </a>
        </div>
    </div>

    <!-- Formulaire de modification du profil -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <h2 class="text-base font-bold text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-3">
            <i class="fa-solid fa-id-card text-blue-600"></i>
            <span>Informations Personnelles</span>
        </h2>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl mb-6 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nom *</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>

                <!-- Prénom -->
                <div>
                    <label for="prenom" class="block text-xs font-bold text-slate-700 uppercase mb-1">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase mb-1">Adresse E-mail *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>

                <!-- Téléphone -->
                <div>
                    <label for="tel" class="block text-xs font-bold text-slate-700 uppercase mb-1">Numéro de téléphone</label>
                    <input type="text" id="tel" name="tel" value="{{ old('tel', $user->tel) }}" placeholder="+241 ..."
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Modification du mot de passe -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-slate-400"></i>
                    <span>Changer le mot de passe (optionnel)</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase mb-1">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-bold text-slate-700 uppercase mb-1">Confirmer le nouveau</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>
                </div>
            </div>

            <!-- Bouton -->
            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-lg transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Enregistrer les modifications</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
