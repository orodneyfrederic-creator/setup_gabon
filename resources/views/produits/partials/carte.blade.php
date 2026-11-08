{{-- Carte produit du catalogue : accueil, recherche, catégories, produits similaires --}}
@php
    $enStock = $produit->quantite > 0;
    $stockFaible = $enStock && $produit->quantite <= 3;
@endphp
<article class="group bg-white rounded-xl border border-slate-200 hover:border-slate-300 hover:shadow-lg transition-[box-shadow,border-color] duration-200 flex flex-col overflow-hidden">
    <a href="{{ route('produits.show', $produit->id) }}" class="flex-1 flex flex-col">
        <div class="aspect-[4/5] bg-slate-100 relative overflow-hidden">
            @if($produit->image)
                <img src="{{ asset('storage/' . $produit->image) }}" alt="{{ $produit->designation }}" loading="lazy"
                     class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500 ease-out">
            @else
                <div class="w-full h-full flex items-center justify-center text-slate-300">
                    <i class="fa-solid fa-laptop text-4xl"></i>
                </div>
            @endif
            @unless($enStock)
                <span class="absolute top-2 left-2 px-2 py-1 rounded-md bg-slate-900/80 text-white text-[11px] font-semibold">Rupture</span>
            @endunless
        </div>

        <div class="p-3 sm:p-4 flex-1 flex flex-col">
            @if($produit->category)
                <p class="text-xs text-slate-500 truncate">{{ $produit->category->nom }}</p>
            @endif
            <h3 class="mt-0.5 text-sm sm:text-[15px] font-semibold text-slate-900 leading-snug line-clamp-2 group-hover:text-blue-700 transition-colors">
                {{ $produit->designation }}
            </h3>
            <div class="mt-auto pt-3">
                <p class="chiffres text-base sm:text-lg font-bold text-slate-900">
                    {{ number_format($produit->prix_unitaire, 0, ',', ' ') }} <span class="text-xs font-semibold text-slate-500">FCFA</span>
                </p>
                <p class="mt-1 text-xs flex items-center gap-1.5 {{ ! $enStock ? 'text-slate-500' : ($stockFaible ? 'text-amber-700' : 'text-emerald-700') }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ ! $enStock ? 'bg-slate-400' : ($stockFaible ? 'bg-amber-500' : 'bg-emerald-600') }}"></span>
                    @if(! $enStock)
                        Bientôt de retour
                    @elseif($stockFaible)
                        Plus que {{ $produit->quantite }} en stock
                    @else
                        En stock
                    @endif
                </p>
            </div>
        </div>
    </a>

    <div class="px-3 pb-3 sm:px-4 sm:pb-4">
        @if($enStock)
            <button type="button"
                    onclick='ajouterAuPanierBoutique({{ $produit->id }}, {{ json_encode($produit->designation) }}, {{ $produit->prix_unitaire }}, {{ $produit->quantite }})'
                    class="w-full h-10 rounded-lg bg-blue-50 text-blue-800 hover:bg-blue-700 hover:text-white text-sm font-semibold flex items-center justify-center gap-2 transition-colors">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Ajouter<span class="hidden sm:inline"> au panier</span></span>
            </button>
        @else
            <a href="https://wa.me/{{ config('boutique.whatsapp') }}?text={{ rawurlencode('Bonjour, le produit « '.$produit->designation.' » sera-t-il bientôt disponible ?') }}" target="_blank" rel="noopener"
               class="w-full h-10 rounded-lg border border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50 text-sm font-semibold flex items-center justify-center gap-2 transition-colors">
                <i class="fa-brands fa-whatsapp"></i>
                <span>Me prévenir</span>
            </a>
        @endif
    </div>
</article>
