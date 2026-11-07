@extends('layouts.app')

@section('title', 'Page introuvable')

@section('content')
<div class="max-w-xl mx-auto text-center py-16 sm:py-24">
    <p class="chiffres text-sm font-semibold text-blue-700">Erreur 404</p>
    <h1 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">Cette page n'existe pas ou plus</h1>
    <p class="mt-3 text-slate-600">Le produit a peut-être été retiré du catalogue, ou le lien est incorrect.</p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a href="{{ route('home') }}" class="h-12 px-6 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-semibold flex items-center">Retour à la boutique</a>
        <a href="https://wa.me/24166131270" target="_blank" rel="noopener" class="h-12 px-5 rounded-lg border border-slate-300 text-slate-800 hover:bg-white font-semibold flex items-center gap-2">
            <i class="fa-brands fa-whatsapp"></i> Nous écrire
        </a>
    </div>
</div>
@endsection
