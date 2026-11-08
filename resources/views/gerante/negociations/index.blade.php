@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- EN-TÊTE --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Négociations de prix</h1>
            <p class="text-xs text-slate-500 mt-1">
                Suivi des propositions clients, contre-offres automatiques et arbitrages manuels.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('gerante.negociations.regles') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-sliders"></i>
                <span>Règles par catégorie</span>
            </a>
        </div>
    </div>

    {{-- STATISTIQUES --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- En attente --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $nbEnAttente }}</p>
                <p class="text-xs text-slate-500 font-medium">À arbitrer manuellement</p>
            </div>
        </div>

        {{-- Acceptées --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $nbAcceptees }}</p>
                <p class="text-xs text-slate-500 font-medium">Acceptées / Utilisées</p>
            </div>
        </div>

        {{-- Taux d'acceptation --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $tauxAcceptation }} %</p>
                <p class="text-xs text-slate-500 font-medium">Taux d'accord</p>
            </div>
        </div>

        {{-- Remise moyenne --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-tag"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ number_format($remiseMoyenne, 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span></p>
                <p class="text-xs text-slate-500 font-medium">Remise moyenne accordée</p>
            </div>
        </div>
    </div>

    {{-- FILTRES PAR STATUT --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-semibold">
        <a href="{{ route('gerante.negociations.index') }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap {{ !$statut ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Toutes
        </a>
        <a href="{{ route('gerante.negociations.index', ['statut' => 'en_attente_gerante']) }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap flex items-center gap-1.5 {{ $statut === 'en_attente_gerante' ? 'bg-amber-600 text-white border-amber-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            <span>En attente gérante</span>
            @if($nbEnAttente > 0)
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            @endif
        </a>
        <a href="{{ route('gerante.negociations.index', ['statut' => 'en_cours']) }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap {{ $statut === 'en_cours' ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            En cours
        </a>
        <a href="{{ route('gerante.negociations.index', ['statut' => 'acceptee']) }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap {{ $statut === 'acceptee' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Acceptées
        </a>
        <a href="{{ route('gerante.negociations.index', ['statut' => 'utilisee']) }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap {{ $statut === 'utilisee' ? 'bg-violet-600 text-white border-violet-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Utilisées
        </a>
        <a href="{{ route('gerante.negociations.index', ['statut' => 'refusee']) }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap {{ $statut === 'refusee' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Refusées
        </a>
        <a href="{{ route('gerante.negociations.index', ['statut' => 'expiree']) }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap {{ $statut === 'expiree' ? 'bg-slate-600 text-white border-slate-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Expirées
        </a>
    </div>

    {{-- TABLEAU DES NÉGOCIATIONS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold text-[11px]">
                        <th class="py-3 px-4">Réf.</th>
                        <th class="py-3 px-4">Client</th>
                        <th class="py-3 px-4">Produit</th>
                        <th class="py-3 px-4 text-center">Quantité</th>
                        <th class="py-3 px-4 text-right">Prix cat.</th>
                        <th class="py-3 px-4 text-right">Prix convenu</th>
                        <th class="py-3 px-4 text-center">Tours</th>
                        <th class="py-3 px-4 text-center">Statut</th>
                        <th class="py-3 px-4 text-center">Date</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($negociations as $nego)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                                #{{ $nego->reference }}
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-900">{{ $nego->user->prenom }} {{ $nego->user->nom }}</p>
                                <p class="text-[11px] text-slate-400">{{ $nego->user->email }}</p>
                            </td>
                            <td class="py-3.5 px-4 max-w-[200px]">
                                <p class="font-bold text-slate-800 truncate">{{ $nego->produit->designation }}</p>
                                <p class="text-[11px] text-slate-400">{{ $nego->produit->category?->nom ?? 'Sans catégorie' }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                {{ $nego->quantite }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-slate-500">
                                {{ number_format($nego->prix_catalogue, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3.5 px-4 text-right font-black">
                                @if($nego->prix_final_convenu)
                                    <span class="text-emerald-700">{{ number_format($nego->prix_final_convenu, 0, ',', ' ') }} FCFA</span>
                                    <span class="text-[11px] block text-emerald-600 font-normal">
                                        -{{ number_format($nego->prix_catalogue - $nego->prix_final_convenu, 0, ',', ' ') }} FCFA
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                    {{ $nego->tours_utilises }}/{{ $nego->tours_max }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $statutClasses = [
                                        'en_cours' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'en_attente_gerante' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'acceptee' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'utilisee' => 'bg-violet-50 text-violet-700 border-violet-200',
                                        'refusee' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'expiree' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        'annulee' => 'bg-slate-100 text-slate-500 border-slate-200',
                                    ];
                                    $statutLabels = [
                                        'en_cours' => 'En cours',
                                        'en_attente_gerante' => 'À décider',
                                        'acceptee' => 'Acceptée',
                                        'utilisee' => 'Commandée',
                                        'refusee' => 'Refusée',
                                        'expiree' => 'Expirée',
                                        'annulee' => 'Abandonnée',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statutClasses[$nego->statut] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $statutLabels[$nego->statut] ?? $nego->statut }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-slate-400 text-[11px]">
                                {{ $nego->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('gerante.negociations.show', $nego) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Détail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-handshake-simple text-4xl mb-3 opacity-20 block"></i>
                                <p class="font-medium">Aucune négociation trouvée.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($negociations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $negociations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
