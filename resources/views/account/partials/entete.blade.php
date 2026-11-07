{{-- En-tête commun de l'espace client : titre + onglets --}}
<div class="mb-6 sm:mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">{{ $titre }}</h1>
    @isset($sousTitre)
        <p class="mt-1 text-slate-500">{{ $sousTitre }}</p>
    @endisset
    <nav class="mt-5 flex gap-6 border-b border-slate-200 text-sm font-semibold" aria-label="Espace client">
        <a href="{{ route('orders.index') }}"
           class="-mb-px pb-3 border-b-2 transition-colors {{ request()->routeIs('orders.*') ? 'border-blue-700 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-900' }}">
            Mes commandes
        </a>
        <a href="{{ route('account.profile') }}"
           class="-mb-px pb-3 border-b-2 transition-colors {{ request()->routeIs('account.*') ? 'border-blue-700 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-900' }}">
            Mes informations
        </a>
    </nav>
</div>
