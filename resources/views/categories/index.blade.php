@extends('layouts.app')

@push('styles')
<style>
    .cat-card {
        position: relative;
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    .cat-card:hover {
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15);
        transform: translateY(-4px);
    }
    /* Carte AVEC image */
    .cat-card.has-image .cat-body {
        padding: 16px;
    }
    /* Carte SANS image — fond hachuré diagonal */
    .cat-card.no-image {
        background: repeating-linear-gradient(
            -45deg,
            #e8edf3,
            #e8edf3 2px,
            #f1f5f9 2px,
            #f1f5f9 24px
        );
        justify-content: center;
        min-height: 220px;
    }
    .cat-card.no-image .cat-body {
        padding: 28px 20px 20px;
    }
    .cat-card.no-image .cat-title {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }
    .cat-card.no-image .cat-count {
        font-size: 0.95rem;
    }
    .cat-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
    }
    .cat-card:hover .cat-img {
        transform: scale(1.04);
    }
    .cat-img-wrapper {
        overflow: hidden;
    }
</style>
@endpush

@section('content')

    <!-- EN-TÊTE DE PAGE -->
    <div class="mb-6 border-b border-slate-200 pb-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-layer-group text-blue-700"></i>
                <span>Toutes nos catégories</span>
            </h1>
            @if(Auth::guard('staff')->check() && Auth::guard('staff')->user()->role === 'gerante')
                <a href="{{ route('gerante.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-sm border bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
                </a>
            @else
                <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-sm border bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
                </a>
            @endif

        </div>
        <p class="text-slate-500 text-sm mt-2">Sélectionnez une catégorie pour découvrir nos produits.</p>
    </div>

    @if(isset($categories) && $categories->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($categories as $cat)
                <a href="{{ route('home', ['category' => $cat->id]) }}"
                   class="cat-card {{ $cat->image ? 'has-image' : 'no-image' }} group">

                    @if($cat->image)
                        {{-- Carte AVEC image --}}
                        <div class="cat-img-wrapper">
                            <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->nom }}" class="cat-img">
                        </div>
                        <div class="cat-body flex flex-col flex-1">
                            <h2 class="cat-title text-base font-bold text-slate-900 group-hover:text-blue-700 transition-colors leading-tight">
                                {{ $cat->nom }}
                            </h2>
                            @if($cat->description)
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $cat->description }}</p>
                            @endif
                            <p class="cat-count text-xs font-bold text-blue-600 mt-auto pt-3">
                                <i class="fa-solid fa-box mr-1"></i>{{ $cat->produits()->count() }} produit(s)
                            </p>
                        </div>
                    @else
                        {{-- Carte SANS image — fond hachuré --}}
                        <div class="cat-body flex flex-col items-start flex-1">
                            <h2 class="cat-title text-xl font-black text-slate-900 group-hover:text-blue-700 transition-colors leading-tight">
                                {{ $cat->nom }}
                            </h2>
                            @if($cat->description)
                                <p class="cat-count text-sm text-slate-600 mt-2 line-clamp-3">{{ $cat->description }}</p>
                            @endif
                            <p class="text-xs font-bold text-blue-600 mt-auto pt-4">
                                <i class="fa-solid fa-box mr-1"></i>{{ $cat->produits()->count() }} produit(s)
                            </p>
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-white p-12 text-center rounded-2xl shadow-sm border border-slate-200 max-w-md mx-auto my-8">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Aucune catégorie</h3>
            <p class="text-slate-500 text-xs mt-1">Les catégories n'ont pas encore été configurées.</p>
        </div>
    @endif

@endsection



