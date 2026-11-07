@extends('layouts.app')

@section('title', 'Mes commandes')

@section('content')
<div class="max-w-4xl mx-auto">
    @include('account.partials.entete', ['titre' => 'Mes commandes', 'sousTitre' => 'Suivez l\'état de vos commandes passées sur SETUP-Gabon.'])

    @if ($commandes->isEmpty())
        <div class="bg-white border border-slate-200 rounded-xl px-6 py-14 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                <i class="fa-solid fa-box-open text-lg"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900">Aucune commande pour le moment</h2>
            <p class="text-sm text-slate-500 mt-1">Vos commandes apparaîtront ici dès que vous en aurez passé une.</p>
            <a href="{{ route('home') }}" class="mt-6 inline-flex items-center h-11 px-5 bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm rounded-lg">
                Voir les produits
            </a>
        </div>
    @else
        <ul class="bg-white border border-slate-200 rounded-xl divide-y divide-slate-100">
            @foreach ($commandes as $cmd)
                <li>
                    <a href="{{ route('orders.show', $cmd->id) }}" class="flex items-center gap-4 px-4 sm:px-6 py-4 hover:bg-slate-50 transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span class="chiffres font-semibold text-slate-900">Commande n° {{ $cmd->id }}</span>
                                @include('account.partials.statut', ['statut' => $cmd->statut])
                            </div>
                            <p class="chiffres text-sm text-slate-500 mt-1">
                                {{ \Carbon\Carbon::parse($cmd->date_c)->translatedFormat('d M Y') }}
                                · {{ $cmd->lignesCommandes->count() }} {{ $cmd->lignesCommandes->count() > 1 ? 'articles' : 'article' }}
                            </p>
                        </div>
                        <span class="chiffres font-bold text-slate-900 text-right shrink-0">{{ number_format($cmd->montant_c, 0, ',', ' ') }} <span class="text-xs font-semibold text-slate-500">FCFA</span></span>
                        <i class="fa-solid fa-chevron-right text-xs text-slate-400"></i>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="mt-6">
            {{ $commandes->links() }}
        </div>
    @endif
</div>
@endsection
