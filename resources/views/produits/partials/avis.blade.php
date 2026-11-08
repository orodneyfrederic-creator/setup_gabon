{{-- Avis clients : achat vérifié uniquement, un avis par client et par produit --}}
@php
    $nbAvis = $avisPublies->count();
    $noteMoyenne = $nbAvis > 0 ? round($avisPublies->avg('note'), 1) : null;
    $repartition = collect(range(5, 1))->mapWithKeys(fn ($note) => [$note => $avisPublies->where('note', $note)->count()]);
@endphp
<section id="avis" class="mt-8 pt-8 lg:mt-12 lg:pt-10 border-t border-slate-200 grid lg:grid-cols-12 gap-6" aria-labelledby="titre-avis">
    <div class="lg:col-span-4">
        <h2 id="titre-avis" class="text-xl font-bold tracking-tight text-slate-900">Avis clients</h2>

        @if ($nbAvis > 0)
            <div class="mt-3 flex items-baseline gap-2">
                <span class="chiffres text-4xl font-extrabold text-slate-900">{{ number_format($noteMoyenne, 1, ',', ' ') }}</span>
                <span class="text-slate-500">sur 5</span>
            </div>
            @include('produits.partials.etoiles', ['note' => $noteMoyenne, 'taille' => 'text-base'])
            <p class="mt-1 text-sm text-slate-500">{{ $nbAvis }} avis</p>

            <dl class="mt-4 space-y-1.5 max-w-xs">
                @foreach ($repartition as $note => $nombre)
                    <div class="flex items-center gap-2 text-sm">
                        <dt class="chiffres w-14 shrink-0 text-slate-600">{{ $note }} étoile{{ $note > 1 ? 's' : '' }}</dt>
                        <dd class="flex-1 flex items-center gap-2">
                            <span class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden" aria-hidden="true">
                                <span class="block h-full bg-amber-400 rounded-full" style="width: {{ $nbAvis ? round($nombre / $nbAvis * 100) : 0 }}%"></span>
                            </span>
                            <span class="chiffres w-6 text-right text-slate-500">{{ $nombre }}</span>
                        </dd>
                    </div>
                @endforeach
            </dl>
        @else
            <p class="mt-2 text-sm text-slate-500">Aucun avis pour l'instant.</p>
        @endif

        <p class="mt-4 text-xs text-slate-500 leading-relaxed">
            Seuls les clients qui ont reçu ce produit peuvent donner leur avis. La boutique ne modifie pas les avis et ne les rémunère pas ;
            elle peut seulement masquer un avis injurieux ou hors sujet.
        </p>
    </div>

    <div class="lg:col-span-8 max-w-[68ch]">
        @if ($peutDonnerAvis)
            @php $champ = 'w-full px-3.5 py-3 bg-white border border-slate-300 rounded-lg text-[15px] text-slate-900 placeholder-slate-400 focus:outline-hidden focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition'; @endphp
            <form method="POST" action="{{ route('avis.store', $produit) }}" class="mb-8 bg-slate-50 border border-slate-200 rounded-xl p-4 sm:p-5"
                  x-data="{ note: {{ (int) old('note', $monAvis?->note ?? 0) }}, survol: 0 }">
                @csrf
                <h3 class="font-semibold text-slate-900">{{ $monAvis ? 'Modifier mon avis' : 'Vous avez reçu ce produit : donnez votre avis' }}</h3>

                <fieldset class="mt-3">
                    <legend class="text-sm text-slate-600 mb-1.5">Votre note</legend>
                    <div class="flex items-center gap-1" @mouseleave="survol = 0">
                        @foreach (range(1, 5) as $valeur)
                            <label class="cursor-pointer p-1 -m-0.5" @mouseenter="survol = {{ $valeur }}">
                                <input type="radio" name="note" value="{{ $valeur }}" x-model.number="note" class="sr-only peer" required>
                                <i class="fa-star text-2xl transition-colors peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-blue-600 rounded-sm"
                                   :class="(survol || note) >= {{ $valeur }} ? 'fa-solid text-amber-400' : 'fa-regular text-slate-300'"></i>
                                <span class="sr-only">{{ $valeur }} étoile{{ $valeur > 1 ? 's' : '' }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('note')
                        <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="mt-4">
                    <label for="commentaire" class="block text-sm text-slate-600 mb-1.5">Votre commentaire <span class="text-slate-400">(facultatif)</span></label>
                    <textarea id="commentaire" name="commentaire" rows="3" maxlength="1000"
                              placeholder="Qualité, autonomie, livraison… ce qui aidera les autres clients."
                              class="{{ $champ }}">{{ old('commentaire', $monAvis?->commentaire) }}</textarea>
                    @error('commentaire')
                        <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="mt-4 h-11 px-5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold">
                    {{ $monAvis ? 'Enregistrer les modifications' : 'Publier mon avis' }}
                </button>
            </form>
        @endif

        @if ($nbAvis > 0)
            <ul class="divide-y divide-slate-200 border-y border-slate-200">
                @foreach ($avisPublies as $avis)
                    <li class="py-5">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            @include('produits.partials.etoiles', ['note' => $avis->note, 'taille' => 'text-sm'])
                            <span class="font-semibold text-slate-900">{{ $avis->auteurAffiche() }}</span>
                            <span class="text-xs font-medium text-emerald-700 inline-flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Achat vérifié
                            </span>
                            <span class="chiffres text-xs text-slate-500">{{ $avis->created_at->translatedFormat('d F Y') }}</span>
                        </div>
                        @if ($avis->commentaire)
                            <p class="mt-2 text-[15px] leading-relaxed text-slate-700">{!! nl2br(e($avis->commentaire)) !!}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        @elseif (! $peutDonnerAvis)
            <p class="text-sm text-slate-600">Vous avez acheté ce produit ? Une fois votre commande livrée, vous pourrez donner votre avis ici.</p>
        @endif
    </div>
</section>
