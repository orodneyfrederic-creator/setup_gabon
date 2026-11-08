@extends('layouts.app')

@section('title', 'Avis clients')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Avis clients</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-2xl">
                Seuls les clients qui ont reçu un produit peuvent le noter. Vous pouvez masquer un avis injurieux, hors sujet ou qui contient
                des données personnelles, mais pas le modifier. Un avis négatif honnête doit rester visible.
            </p>
        </div>
        <div class="flex gap-6 text-sm">
            <div>
                <p class="chiffres text-2xl font-bold text-slate-900">{{ $noteMoyenne ? number_format($noteMoyenne, 1, ',', ' ') : '–' }}<span class="text-sm font-medium text-slate-500"> / 5</span></p>
                <p class="text-slate-500">Note moyenne</p>
            </div>
            <div>
                <p class="chiffres text-2xl font-bold text-slate-900">{{ $nbPublies }}</p>
                <p class="text-slate-500">Publiés</p>
            </div>
            <div>
                <p class="chiffres text-2xl font-bold text-slate-900">{{ $nbMasques }}</p>
                <p class="text-slate-500">Masqués</p>
            </div>
        </div>
    </div>

    <nav class="flex items-center gap-2 overflow-x-auto pb-1 text-sm font-semibold" aria-label="Filtrer les avis">
        @foreach (['' => 'Tous', 'publie' => 'Publiés', 'masque' => 'Masqués'] as $valeur => $libelle)
            @php $estActif = ($statut ?? '') === $valeur; @endphp
            <a href="{{ route('gerante.avis.index', array_filter(['statut' => $valeur])) }}" @if($estActif) aria-current="page" @endif
               class="h-9 px-3.5 rounded-lg border whitespace-nowrap flex items-center {{ $estActif ? 'bg-blue-700 text-white border-blue-700' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                {{ $libelle }}
            </a>
        @endforeach
    </nav>

    @if($avis->isEmpty())
        <div class="bg-white border border-slate-200 rounded-xl px-6 py-14 text-center">
            <i class="fa-regular fa-star text-3xl text-slate-300"></i>
            <p class="mt-3 font-semibold text-slate-900">Aucun avis pour le moment</p>
            <p class="text-sm text-slate-500 mt-1">Les clients peuvent noter un produit dès que leur commande passe au statut « Livrée ».</p>
        </div>
    @else
        <ul class="space-y-3">
            @foreach($avis as $unAvis)
                <li class="bg-white border rounded-xl p-4 sm:p-5 {{ $unAvis->estPublie() ? 'border-slate-200' : 'border-slate-200 bg-slate-50' }}" x-data="{ masquer: false }">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                @include('produits.partials.etoiles', ['note' => $unAvis->note])
                                <span class="font-semibold text-slate-900">{{ $unAvis->user?->prenom }} {{ $unAvis->user?->nom }}</span>
                                <span class="chiffres text-xs text-slate-500">{{ $unAvis->created_at->format('d/m/Y') }}</span>
                                @unless($unAvis->estPublie())
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">Masqué</span>
                                @endunless
                            </div>
                            <p class="mt-1 text-sm">
                                @if($unAvis->produit)
                                    <a href="{{ route('produits.show', $unAvis->produit->id) }}#avis" class="text-blue-700 hover:underline">{{ $unAvis->produit->designation }}</a>
                                @endif
                                @if($unAvis->id_c)
                                    <span class="text-slate-500">· commande <a href="{{ route('gerante.commandes.show', $unAvis->id_c) }}" class="underline">#{{ $unAvis->id_c }}</a></span>
                                @endif
                            </p>
                            @if($unAvis->commentaire)
                                <p class="mt-2 text-[15px] text-slate-700 leading-relaxed">{!! nl2br(e($unAvis->commentaire)) !!}</p>
                            @endif
                            @if(! $unAvis->estPublie() && $unAvis->motif_masquage)
                                <p class="mt-2 text-xs text-slate-500">Motif du masquage : {{ $unAvis->motif_masquage }}</p>
                            @endif
                        </div>

                        <div class="shrink-0">
                            @if($unAvis->estPublie())
                                <button type="button" @click="masquer = !masquer" class="h-9 px-3.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold">
                                    Masquer
                                </button>
                            @else
                                <form method="POST" action="{{ route('gerante.avis.moderer', $unAvis) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="action" value="republier">
                                    <button type="submit" class="h-9 px-3.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-white text-sm font-semibold">Republier</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if($unAvis->estPublie())
                        <form method="POST" action="{{ route('gerante.avis.moderer', $unAvis) }}" x-show="masquer" x-cloak class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="masquer">
                            <label class="sr-only" for="motif-{{ $unAvis->id }}">Motif du masquage</label>
                            <select id="motif-{{ $unAvis->id }}" name="motif_masquage" required class="flex-1 h-10 px-3 border border-slate-300 rounded-lg text-sm bg-white focus:outline-hidden focus:ring-4 focus:ring-blue-100 focus:border-blue-600">
                                <option value="">Choisir un motif…</option>
                                <option>Propos injurieux ou discriminatoires</option>
                                <option>Contient des données personnelles</option>
                                <option>Sans rapport avec le produit</option>
                                <option>Publicité ou lien vers un autre site</option>
                            </select>
                            <button type="submit" class="h-10 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold">Confirmer le masquage</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>

        {{ $avis->links() }}
    @endif
</div>
@endsection
