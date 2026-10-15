@extends('layouts.app')

@section('content')

        <!-- PAGE D'ACCUEIL / RECHERCHE : CHIPS DE NAVIGATION -->
        @if(isset($categories) && $categories->count() > 0)
        <div class="mb-6 overflow-x-auto pb-2 scrollbar-none">
            <div class="flex items-center gap-2 sm:gap-3 min-w-max">
                <a href="{{ route('home') }}" class="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-sm border {{ !request('category') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600' }}">
                    <i class="fa-solid fa-border-all mr-1"></i> Tous
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('home', ['category' => $cat->id]) }}" class="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-sm border {{ request('category') == $cat->id ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600' }}">
                        {{ $cat->nom }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif

    <!-- TITRE DE SECTION / FILTRE ACTIF (Toujours affiché) -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-blue-700"></i>
            <span>{{ request('search') ? 'Résultats de recherche' : (request('category') ? ($categories->firstWhere('id', request('category'))->nom ?? 'Produits') : 'Tous nos produits') }}</span>
        </h1>
        <span class="text-slate-600 text-xs font-semibold bg-white px-3.5 py-1.5 rounded-full border border-slate-200 shadow-sm inline-flex items-center gap-1.5 self-start sm:self-auto">
            <i class="fa-solid fa-layer-group text-blue-600 text-[10px]"></i>
            <span>{{ $produits->total() }} produit(s) disponible(s)</span>
        </span>
    </div>

    <!-- GRILLE DE PRODUITS (Toujours affichée) -->
    @if($produits->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($produits as $produit)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden border border-slate-200/80 group">
                    
                    <a href="{{ route('produits.show', $produit->id) }}" class="flex-1 flex flex-col">
                        <!-- Image Produit -->
                        <div class="h-56 w-full bg-slate-100 relative overflow-hidden">
                            @if($produit->image)
                                <img src="{{ asset('storage/' . $produit->image) }}" alt="{{ $produit->designation }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50">
                                    <i class="fa-solid fa-image text-4xl mb-2 block opacity-50"></i>
                                    <span class="text-xs font-semibold">Sans image</span>
                                </div>
                            @endif
                        </div>

                        <!-- Infos Produit -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <h2 class="text-[15px] font-bold text-slate-900 line-clamp-2 group-hover:text-blue-700 transition-colors">
                                    {{ $produit->designation }}
                                </h2>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <div class="text-xl font-black text-slate-900">
                                    {{ number_format($produit->prix_unitaire, 0, ',', ' ') }} <span class="text-sm font-bold text-blue-700">FCFA</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    
                    <div class="px-5 pb-5 mt-auto">
                        <!-- Bouton Ajouter au Panier -->
                        <button type="button" 
                                onclick='ajouterAuPanierBoutique({{ $produit->id }}, {{ json_encode($produit->designation) }}, {{ $produit->prix_unitaire }}, {{ $produit->quantite }})' 
                                class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-4 rounded-xl shadow-md hover:shadow-blue-600/25 transition-all text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-cart-plus"></i>
                            <span>Ajouter au panier</span>
                        </button>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- PAGINATION -->
        <div class="mt-8">
            {{ $produits->links() }}
        </div>
    @else
        <div class="bg-white p-12 text-center rounded-2xl shadow-sm border border-slate-200 max-w-md mx-auto my-8">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Aucun produit trouvé</h3>
            <p class="text-slate-500 text-xs mt-1">Aucun produit ne correspond à votre recherche actuelle.</p>
        </div>
    @endif

@endsection

