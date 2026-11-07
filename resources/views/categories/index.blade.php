@extends('layouts.app')

@section('title', 'Catégories')

@section('content')

    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Catégories</h1>
        <p class="mt-1 text-slate-500">Choisissez un rayon pour voir les produits disponibles.</p>
    </div>

    @if($categories->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($categories as $cat)
                @php
                    $nbProduits = $cat->produits->count();
                    $apercu = $cat->image ?: $cat->produits->firstWhere('image')?->image;
                @endphp
                <a href="{{ route('home', ['category' => $cat->id]) }}#catalogue"
                   class="group bg-white border border-slate-200 hover:border-slate-300 hover:shadow-lg rounded-xl overflow-hidden flex flex-col transition-[box-shadow,border-color]">
                    <div class="aspect-[4/3] bg-slate-100 overflow-hidden">
                        @if($apercu)
                            <img src="{{ asset('storage/' . $apercu) }}" alt="" loading="lazy" class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500 ease-out">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="fa-solid fa-layer-group text-3xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-3 sm:p-4 flex-1 flex flex-col">
                        <h2 class="font-semibold text-slate-900 group-hover:text-blue-700 leading-snug">{{ $cat->nom }}</h2>
                        @if($cat->description)
                            <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $cat->description }}</p>
                        @endif
                        <p class="chiffres mt-auto pt-3 text-sm text-slate-500">
                            {{ $nbProduits }} {{ $nbProduits > 1 ? 'produits' : 'produit' }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-xl px-6 py-14 text-center">
            <h2 class="text-lg font-bold text-slate-900">Aucune catégorie pour le moment</h2>
            <p class="text-sm text-slate-500 mt-1">Le catalogue est en cours de mise en ligne.</p>
            <a href="{{ route('home') }}" class="mt-6 inline-flex items-center h-11 px-5 bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm rounded-lg">Voir tous les produits</a>
        </div>
    @endif

@endsection
