@extends('layouts.app')

@push('scripts')
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')

    {{-- HEADER DE LA PAGE --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-blue-700 text-white flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-chart-line text-lg"></i>
                </div>
                <span>Analytics & Rapports Avancés</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Indicateurs de performance, statistiques des ventes & graphiques interactifs ApexCharts</p>
        </div>

        {{-- FILTRE PÉRIODE --}}
        <form method="GET" action="{{ route('gerante.analytics.index') }}" class="flex items-center gap-2">
            <div class="inline-flex bg-white p-1 rounded-2xl border border-slate-200 shadow-sm text-xs font-bold text-slate-600">
                <button type="submit" name="period" value="7" class="px-3 py-1.5 rounded-xl transition-all {{ $period == '7' ? 'bg-emerald-600 text-white shadow' : 'hover:text-slate-900' }}">
                    7 jours
                </button>
                <button type="submit" name="period" value="30" class="px-3 py-1.5 rounded-xl transition-all {{ $period == '30' ? 'bg-emerald-600 text-white shadow' : 'hover:text-slate-900' }}">
                    30 jours
                </button>
                <button type="submit" name="period" value="90" class="px-3 py-1.5 rounded-xl transition-all {{ $period == '90' ? 'bg-emerald-600 text-white shadow' : 'hover:text-slate-900' }}">
                    90 jours
                </button>
            </div>
        </form>
    </div>

    {{-- CARTE STATISTIQUES --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- CA Total -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Chiffre d'Affaires</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($caTotal, 0, ',', ' ') }} <span class="text-xs text-emerald-600">FCFA</span></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-[11px] text-slate-500 font-semibold border-t border-slate-100 pt-2">
                <span class="text-emerald-600 font-bold">POS : {{ number_format($caPos, 0, ',', ' ') }}</span> | <span>En ligne : {{ number_format($caOnline, 0, ',', ' ') }}</span> | <span>Factures : {{ number_format($totalFacturesPayees, 0, ',', ' ') }}</span>
            </div>
        </div>

        <!-- Ventes Boutique -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ventes Caisse (POS)</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $nbVentesPos }} <span class="text-xs text-slate-400 font-semibold">tickets</span></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-slate-500 font-semibold border-t border-slate-100 pt-2">
                Panier moyen : <span class="font-bold text-blue-600">{{ number_format($panierMoyenPos, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <!-- Devis Générés -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Devis Créés</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalDevis }} <span class="text-xs text-slate-400 font-semibold">propositions</span></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-slate-500 font-semibold border-t border-slate-100 pt-2">
                Factures réglées : <span class="font-bold text-amber-600">{{ number_format($totalFacturesPayees, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <!-- Conversion & Performance -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Canal Principal</p>
                    <p class="text-xl font-black text-emerald-700 mt-1">Caisse POS</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-store"></i>
                </div>
            </div>
            <div class="mt-3 text-[11px] text-slate-500 font-semibold border-t border-slate-100 pt-2">
                Génération automatique du bilan
            </div>
        </div>

    </div>

    {{-- GRAPHIQUES PRINCIPAUX APEXCHARTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Graphique Évolution du Chiffre d'Affaires --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-black text-slate-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-chart-area text-emerald-600"></i>
                        Évolution des ventes (FCFA)
                    </h2>
                    <p class="text-[11px] text-slate-400">Comparatif ventes Caisse (POS) vs Ventes en ligne sur les {{ $period }} derniers jours</p>
                </div>
            </div>

            <div id="chart-evolution" class="w-full h-72"></div>
        </div>

        {{-- Graphique Donut : Répartition des modes de paiement --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div class="mb-4">
                <h2 class="font-black text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-pie-chart text-blue-600"></i>
                    Modes de paiement (POS)
                </h2>
                <p class="text-[11px] text-slate-400">Ventilation par méthode de règlement</p>
            </div>

            <div id="chart-payments" class="w-full h-72 flex items-center justify-center"></div>
        </div>

    </div>

    {{-- DEUXIÈME RANGÉE DE GRAPHIQUES & TABLEAUX --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Ventes par Catégories --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="font-black text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-chart-bar text-violet-600"></i>
                    Ventes par Catégorie
                </h2>
                <p class="text-[11px] text-slate-400">Top des catégories les plus rentables</p>
            </div>

            <div id="chart-categories" class="w-full h-64"></div>
        </div>

        {{-- Top Produit Vendu --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="font-black text-slate-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-500"></i>
                        Top 5 Produits Vendus (Boutique POS)
                    </h2>
                    <p class="text-[11px] text-slate-400">Classement par volume de ventes</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[11px] font-bold text-slate-400 border-b border-slate-100">
                            <th class="pb-2">Produit</th>
                            <th class="pb-2 text-center">Quantité vendue</th>
                            <th class="pb-2 text-right">Chiffre réalisé</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($topProduits as $index => $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 font-semibold text-slate-800 flex items-center gap-2.5">
                                    <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 font-black text-[11px] flex items-center justify-center">
                                        #{{ $index + 1 }}
                                    </span>
                                    <span>{{ $item->designation }}</span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs">
                                        {{ $item->total_qty }} unité(s)
                                    </span>
                                </td>
                                <td class="py-3 text-right font-black text-slate-900">
                                    {{ number_format($item->total_ca, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-400 text-xs">
                                    Aucune donnée de vente sur la période.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- CONFIGURATION JS DES GRAPHIQUES APEXCHARTS --}}
    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            
            // 1. Graphique d'Évolution (Area Chart)
            const optionsEvolution = {
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                series: [
                    { name: 'Ventes POS (Caisse)', data: @json($salesPosData) },
                    { name: 'Ventes En Ligne', data: @json($salesOnlineData) }
                ],
                xaxis: {
                    categories: @json($dates),
                    labels: { style: { colors: '#94a3b8', fontSize: '11px' } }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return new Intl.NumberFormat('fr-FR').format(value) + ' F';
                        },
                        style: { colors: '#94a3b8', fontSize: '11px' }
                    }
                },
                colors: ['#059669', '#3b82f6'],
                fill: {
                    type: 'gradient',
                    gradient: { opacityFrom: 0.4, opacityTo: 0.05 }
                },
                stroke: { curve: 'smooth', width: 2 },
                dataLabels: { enabled: false },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('fr-FR').format(val) + ' FCFA';
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#chart-evolution"), optionsEvolution).render();

            // 2. Graphique Répartition Paiements (Donut Chart)
            const paymentSeries = @json($paymentData).map(Number);
            const optionsPayments = {
                chart: {
                    type: 'donut',
                    height: 280,
                    fontFamily: 'Inter, sans-serif'
                },
                series: paymentSeries,
                labels: @json($paymentLabels),
                colors: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'],
                legend: { position: 'bottom', fontSize: '12px' },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('fr-FR').format(val) + ' FCFA';
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#chart-payments"), optionsPayments).render();

            // 3. Graphique Ventes par Catégorie (Bar Chart)
            const optionsCategories = {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{
                    name: 'Total CA',
                    data: @json($catData)
                }],
                xaxis: {
                    categories: @json($catLabels),
                    labels: { style: { colors: '#94a3b8', fontSize: '11px' } }
                },
                colors: ['#8b5cf6'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        horizontal: true,
                        barHeight: '50%'
                    }
                },
                dataLabels: { enabled: false },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('fr-FR').format(val) + ' FCFA';
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#chart-categories"), optionsCategories).render();

        });
    </script>
    @endpush

@endsection
