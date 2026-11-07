@extends('layouts.app')

@section('content')
@php
    $pagesLegales = [
        'legal.cgv' => 'Conditions générales de vente',
        'legal.retours' => 'Retours et garanties',
        'legal.confidentialite' => 'Confidentialité',
        'legal.mentions' => 'Mentions légales',
    ];
@endphp
<div class="grid lg:grid-cols-12 gap-8 lg:gap-12 items-start">
    <aside class="min-w-0 lg:col-span-3 lg:sticky lg:top-24">
        <nav aria-label="Informations légales" class="-mx-4 px-4 lg:mx-0 lg:px-0 overflow-x-auto scrollbar-none">
            <ul class="flex lg:flex-col gap-2 lg:gap-0.5 min-w-max lg:min-w-0 text-sm">
                @foreach($pagesLegales as $route => $libelle)
                    @php $actif = request()->routeIs($route); @endphp
                    <li>
                        <a href="{{ route($route) }}" @if($actif) aria-current="page" @endif
                           class="flex items-center h-10 px-3.5 rounded-lg whitespace-nowrap transition-colors {{ $actif ? 'bg-blue-50 text-blue-800 font-semibold' : 'text-slate-600 hover:bg-white hover:text-slate-900 max-lg:border max-lg:border-slate-200 max-lg:bg-white' }}">
                            {{ $libelle }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
        <p class="hidden lg:block mt-6 px-3.5 text-xs text-slate-500 leading-relaxed">
            Une question sur ces conditions ?<br>
            <a href="https://wa.me/{{ config('boutique.whatsapp') }}" target="_blank" rel="noopener" class="font-semibold text-blue-700 hover:underline">Écrivez-nous sur WhatsApp</a>
        </p>
    </aside>

    <article class="min-w-0 lg:col-span-9 bg-white border border-slate-200 rounded-xl px-5 py-7 sm:px-10 sm:py-10">
        <header class="mb-8 pb-6 border-b border-slate-100">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 text-balance">@yield('titre_legal')</h1>
            <p class="mt-2 text-sm text-slate-500">Dernière mise à jour : 7 novembre 2026</p>
            @hasSection('resume_legal')
                <div class="mt-5 bg-blue-50 text-blue-950 rounded-lg px-4 py-3.5 text-[15px] leading-relaxed">
                    @yield('resume_legal')
                </div>
            @endif
        </header>

        <div class="texte-legal max-w-[68ch] text-[15px] leading-relaxed text-slate-700">
            @yield('contenu_legal')
        </div>
    </article>
</div>
@endsection

@push('styles')
<style>
    .texte-legal h2 { margin-top: 2.25rem; margin-bottom: 0.75rem; font-size: 1.125rem; font-weight: 700; color: #151b2b; scroll-margin-top: 6rem; }
    .texte-legal h2:first-child { margin-top: 0; }
    .texte-legal h3 { margin-top: 1.5rem; margin-bottom: 0.5rem; font-weight: 600; color: #232a3d; }
    .texte-legal p + p { margin-top: 0.75rem; }
    .texte-legal ul { margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc; }
    .texte-legal ul li + li { margin-top: 0.35rem; }
    .texte-legal ol { margin-top: 0.5rem; padding-left: 1.25rem; list-style: decimal; }
    .texte-legal ol li + li { margin-top: 0.35rem; }
    .texte-legal a { color: #283888; font-weight: 600; text-decoration: underline; }
    .texte-legal strong { color: #151b2b; font-weight: 600; }
    .texte-legal :is(ol, ul, table) + p { margin-top: 0.75rem; }
    .texte-legal table { width: 100%; table-layout: auto; overflow-wrap: break-word; margin-top: 0.75rem; font-size: 0.9rem; border-collapse: collapse; }
    .texte-legal th, .texte-legal td { text-align: left; padding: 0.6rem 0.75rem; border-bottom: 1px solid #e1e4ec; vertical-align: top; }
    .texte-legal th { color: #151b2b; font-weight: 600; background: #f6f7fa; }
    .texte-legal td strong { white-space: nowrap; }
    @media (max-width: 639px) { .texte-legal th, .texte-legal td { padding: 0.55rem 0.5rem; } }
    .texte-legal .reference-loi { font-size: 0.8125rem; color: #666e85; }
</style>
@endpush
