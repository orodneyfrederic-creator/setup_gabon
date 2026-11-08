@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- EN-TÊTE --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Commandes en ligne</h1>
            <p class="text-xs text-slate-500 mt-1">
                Suivi des commandes passées via le panier et WhatsApp, gestion des statuts et du stock.
            </p>
        </div>
    </div>

    {{-- STATISTIQUES --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- En attente --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $nbEnAttente }}</p>
                <p class="text-xs text-slate-500 font-medium">En attente de confirmation</p>
            </div>
        </div>

        {{-- Confirmées --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $nbConfirmees }}</p>
                <p class="text-xs text-slate-500 font-medium">Confirmées (stock décrémenté)</p>
            </div>
        </div>

        {{-- Livrées --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-truck"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ $nbLivrees }}</p>
                <p class="text-xs text-slate-500 font-medium">Livrées au client</p>
            </div>
        </div>
    </div>

    {{-- FILTRES PAR STATUT --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-semibold">
        <a href="{{ route('gerante.commandes.index') }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap {{ !$statut ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Toutes
        </a>
        <a href="{{ route('gerante.commandes.index', ['statut' => 'en_attente']) }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap flex items-center gap-1.5 {{ $statut === 'en_attente' ? 'bg-amber-600 text-white border-amber-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            <span>En attente</span>
            @if($nbEnAttente > 0)
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            @endif
        </a>
        <a href="{{ route('gerante.commandes.index', ['statut' => 'confirmee']) }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap {{ $statut === 'confirmee' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Confirmées
        </a>
        <a href="{{ route('gerante.commandes.index', ['statut' => 'livree']) }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap {{ $statut === 'livree' ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Livrées
        </a>
        <a href="{{ route('gerante.commandes.index', ['statut' => 'annulee']) }}" 
           class="px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap {{ $statut === 'annulee' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Annulées
        </a>
    </div>

    {{-- TABLEAU DES COMMANDES --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold text-[11px]">
                        <th class="py-3 px-4">Commande</th>
                        <th class="py-3 px-4">Client</th>
                        <th class="py-3 px-4">Articles</th>
                        <th class="py-3 px-4 text-center">Négociation</th>
                        <th class="py-3 px-4 text-right">Montant</th>
                        <th class="py-3 px-4 text-center">Statut</th>
                        <th class="py-3 px-4 text-center">Date</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($commandes as $cmd)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                #{{ $cmd->id }}
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-900">{{ $cmd->nomClient() }}</p>
                                <p class="text-[11px] text-slate-400">
                                    {{ $cmd->telephoneClient() ?: $cmd->emailClient() }}
                                    @if($cmd->estInvitee())
                                        · <span class="text-slate-500">sans compte</span>
                                    @endif
                                </p>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-medium text-slate-700">{{ $cmd->lignesCommandes->count() }} article(s)</p>
                                <p class="text-[11px] text-slate-400 truncate max-w-[200px]">
                                    {{ $cmd->lignesCommandes->pluck('produit.designation')->filter()->join(', ') }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $hasNego = $cmd->lignesCommandes->contains(fn($l) => !is_null($l->negociation_id));
                                @endphp
                                @if($hasNego)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-handshake-simple text-[11px]"></i>
                                        <span>Oui</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">Non</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                {{ number_format($cmd->montant_c, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $statutClasses = [
                                        'en_attente' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'confirmee' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'livree' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'annulee' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ];
                                    $statutLabels = [
                                        'en_attente' => 'En attente',
                                        'confirmee' => 'Confirmée',
                                        'livree' => 'Livrée',
                                        'annulee' => 'Annulée',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statutClasses[$cmd->statut] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $statutLabels[$cmd->statut] ?? $cmd->statut }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-slate-400 text-[11px]">
                                {{ $cmd->date_c ? \Carbon\Carbon::parse($cmd->date_c)->format('d/m/Y H:i') : $cmd->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('gerante.commandes.show', $cmd) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Gérer</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-4xl mb-3 opacity-20 block"></i>
                                <p class="font-medium">Aucune commande trouvée.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($commandes->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $commandes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
