@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="checkoutComponent()">
    <!-- Entête -->
    <div class="bg-gradient-to-r from-blue-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center font-bold text-2xl shadow">
                <i class="fa-solid fa-cart-check"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold">Finaliser la Commande</h1>
                <p class="text-xs text-blue-200 mt-1">Vérifiez vos articles, indiquez votre adresse et validez pour ouvrir WhatsApp.</p>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- ECRAN PANIER VIDE -->
    <template x-if="items.length === 0">
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-200 text-center space-y-4 max-w-lg mx-auto">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 text-3xl">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-800">Votre panier est vide</h2>
            <p class="text-xs text-slate-500 leading-relaxed">
                Vous devez ajouter au moins un produit à votre panier pour pouvoir passer et finaliser une commande.
            </p>
            <div class="pt-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-all shadow-md">
                    <i class="fa-solid fa-shop"></i>
                    <span>Découvrir nos produits</span>
                </a>
            </div>
        </div>
    </template>

    <!-- FORMULAIRE SI PANIER NON VIDE -->
    <template x-if="items.length > 0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulaire de commande (2 cols) -->
            <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-6">
                <form id="checkoutForm" method="POST" action="{{ route('checkout.store') }}" @submit="submitCheckout($event)">
                    @csrf
                    <input type="hidden" name="items" :value="JSON.stringify(items)">

                    <!-- Information Client -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-blue-600"></i>
                            <span>Informations de Livraison</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nom & Prénom</label>
                                <input type="text" value="{{ $user->prenom }} {{ $user->nom }}" readonly
                                       class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Téléphone</label>
                                <input type="text" value="{{ $user->tel ?? 'Non renseigné' }}" readonly
                                       class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600">
                            </div>
                        </div>

                        <div>
                            <label for="adresse_livraison" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                Adresse ou lieu de livraison à Libreville *
                            </label>
                            <input type="text" id="adresse_livraison" name="adresse_livraison" required
                                   placeholder="Ex: Akanda, Carrefour GP, Quartier Louis..."
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                            @error('adresse_livraison')
                                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Mode de paiement -->
                    <div class="space-y-4 pt-6">
                        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
                            <i class="fa-solid fa-wallet text-blue-600"></i>
                            <span>Mode de Paiement</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="p-3 border rounded-2xl cursor-pointer flex flex-col items-center gap-2 text-center transition-all"
                                   :class="payment === 'especes' ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-500/20' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="methode_paiement" value="especes" x-model="payment" class="sr-only">
                                <i class="fa-solid fa-money-bill-wave text-emerald-600 text-xl"></i>
                                <span class="text-xs font-bold text-slate-800">Espèces</span>
                                <span class="text-[10px] text-slate-400">À la livraison</span>
                            </label>

                            <label class="p-3 border rounded-2xl cursor-pointer flex flex-col items-center gap-2 text-center transition-all"
                                   :class="payment === 'airtel_money' ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-500/20' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="methode_paiement" value="airtel_money" x-model="payment" class="sr-only">
                                <i class="fa-solid fa-mobile-screen-button text-red-600 text-xl"></i>
                                <span class="text-xs font-bold text-slate-800">Airtel Money</span>
                                <span class="text-[10px] text-slate-400">Paiement Mobile</span>
                            </label>

                            <label class="p-3 border rounded-2xl cursor-pointer flex flex-col items-center gap-2 text-center transition-all"
                                   :class="payment === 'moov_money' ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-500/20' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="methode_paiement" value="moov_money" x-model="payment" class="sr-only">
                                <i class="fa-solid fa-mobile-retro text-blue-600 text-xl"></i>
                                <span class="text-xs font-bold text-slate-800">Moov Money</span>
                                <span class="text-[10px] text-slate-400">Paiement Mobile</span>
                            </label>
                        </div>
                    </div>

                    <!-- Bouton de confirmation WhatsApp -->
                    <div class="pt-6">
                        <button type="submit" :disabled="items.length === 0"
                                class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold text-sm rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>Confirmer et Passer la Commande sur WhatsApp</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Récapitulatif du Panier (1 col) -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4 h-fit">
                <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-shopping-bag text-blue-600"></i>
                    <span>Récapitulatif ( <span x-text="countArticles"></span> )</span>
                </h3>

                <div class="space-y-3 max-h-72 overflow-y-auto divide-y divide-slate-100">
                    <template x-for="item in items" :key="item.id">
                        <div class="pt-2.5 first:pt-0 flex items-center justify-between gap-2 text-xs">
                            <div>
                                <p class="font-bold text-slate-800" x-text="item.designation"></p>
                                <p class="text-slate-400" x-text="formatPrix(item.prix) + ' FCFA × ' + item.quantite"></p>
                            </div>
                            <span class="font-bold text-slate-900 shrink-0" x-text="formatPrix(item.prix * item.quantite) + ' FCFA'"></span>
                        </div>
                    </template>
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-2">
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>Livraison :</span>
                        <span class="font-bold text-emerald-600">Gratuite</span>
                    </div>
                    <div class="flex justify-between text-sm font-black text-blue-700 pt-2 border-t border-slate-100">
                        <span>Total à payer :</span>
                        <span x-text="formatPrix(totalMontant) + ' FCFA'"></span>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function checkoutComponent() {
    return {
        items: JSON.parse(localStorage.getItem('client_panier') || '[]'),
        payment: 'especes',

        get countArticles() {
            return this.items.reduce((total, i) => total + i.quantite, 0);
        },

        get totalMontant() {
            return this.items.reduce((total, i) => total + (i.prix * i.quantite), 0);
        },

        formatPrix(val) {
            return new Intl.NumberFormat('fr-FR').format(val);
        },

        submitCheckout(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Votre panier est vide. Vous ne pouvez pas passer de commande.');
                return false;
            }
            // Vider le panier dans le navigateur une fois validé
            localStorage.setItem('client_panier', JSON.stringify([]));
        }
    }
}
</script>
@endsection
