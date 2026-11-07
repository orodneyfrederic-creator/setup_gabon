@extends('layouts.app')

@section('title', 'À propos')
@section('meta_description', 'SETUP-Gabon, boutique de matériel informatique à Petit Paris (galerie Mouenga), Libreville : ordinateurs, imprimantes, accessoires, devis et assistance.')

@section('content')
<div class="space-y-16 sm:space-y-20">

    {{-- QUI SOMMES-NOUS --}}
    <section class="grid lg:grid-cols-12 gap-8 lg:gap-14 items-center">
        <div class="lg:col-span-7 order-2 lg:order-1">
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-[1.08] text-balance">
                Une boutique informatique au cœur de Libreville.
            </h1>
            <div class="mt-6 space-y-4 text-[17px] leading-relaxed text-slate-700 max-w-[62ch]">
                <p>
                    <strong class="font-semibold text-slate-900">SETUP-Gabon</strong> vend du matériel informatique, des équipements de bureau,
                    des accessoires et des solutions technologiques sur mesure, depuis sa boutique de Petit Paris, galerie Mouenga.
                </p>
                <p>
                    Particulier à la recherche d'un ordinateur portable, professionnel qui équipe son poste de travail ou entreprise
                    qui monte son infrastructure : nous proposons des marques internationales comme HP, Dell, Lenovo, Apple et Canon,
                    et nous vous aidons à choisir la configuration adaptée à votre budget.
                </p>
            </div>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('home') }}" class="h-12 px-6 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-semibold flex items-center transition-colors">
                    Voir le catalogue
                </a>
                <a href="https://wa.me/24166131270?text={{ rawurlencode('Bonjour SETUP-Gabon, je souhaite un conseil.') }}" target="_blank" rel="noopener"
                   class="h-12 px-5 rounded-lg border border-slate-300 text-slate-800 hover:bg-white font-semibold flex items-center gap-2 transition-colors">
                    <i class="fa-brands fa-whatsapp"></i>
                    Demander un conseil
                </a>
            </div>
        </div>
        <figure class="lg:col-span-5 order-1 lg:order-2">
            <img src="{{ asset('img/boutique-setup.jpg') }}" alt="Les étagères de la boutique SETUP-Gabon : ordinateurs portables, écrans, routeurs et imprimantes"
                 class="w-full aspect-[4/3] lg:aspect-[4/5] object-cover rounded-2xl" loading="eager">
            <figcaption class="mt-2 text-xs text-slate-500">La boutique, Petit Paris, galerie Mouenga.</figcaption>
        </figure>
    </section>

    {{-- CE QUE NOUS FAISONS --}}
    <section class="grid lg:grid-cols-12 gap-6 lg:gap-14 pt-12 border-t border-slate-200">
        <div class="lg:col-span-4">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Ce que vous trouvez chez nous</h2>
            <p class="mt-2 text-slate-600">Notre mission : rendre le matériel informatique de qualité accessible, avec un vrai conseil et un suivi après l'achat.</p>
        </div>
        <dl class="lg:col-span-8 divide-y divide-slate-200 border-y border-slate-200">
            <div class="py-5 grid sm:grid-cols-3 gap-1 sm:gap-6">
                <dt class="font-semibold text-slate-900">Un catalogue large</dt>
                <dd class="sm:col-span-2 text-slate-600">Ordinateurs portables et fixes, imprimantes, stockage, réseau, câbles et accessoires, en stock à la boutique.</dd>
            </div>
            <div class="py-5 grid sm:grid-cols-3 gap-1 sm:gap-6">
                <dt class="font-semibold text-slate-900">Devis et factures</dt>
                <dd class="sm:col-span-2 text-slate-600">Facture proforma et facture définitive pour les entreprises et administrations.</dd>
            </div>
            <div class="py-5 grid sm:grid-cols-3 gap-1 sm:gap-6">
                <dt class="font-semibold text-slate-900">Commande simple</dt>
                <dd class="sm:col-span-2 text-slate-600">En ligne, sur WhatsApp ou directement en magasin. Paiement en espèces, Airtel Money ou Moov Money.</dd>
            </div>
            <div class="py-5 grid sm:grid-cols-3 gap-1 sm:gap-6">
                <dt class="font-semibold text-slate-900">Assistance et maintenance</dt>
                <dd class="sm:col-span-2 text-slate-600">Une équipe technique disponible pour l'assistance et la maintenance de votre matériel.</dd>
            </div>
        </dl>
    </section>

    {{-- NOUS RENDRE VISITE --}}
    <section class="bg-blue-700 text-white rounded-2xl px-6 py-10 sm:px-12 sm:py-12 grid lg:grid-cols-2 gap-10">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Passez nous voir</h2>
            <p class="mt-3 text-blue-100 max-w-md">Venez voir le matériel en boutique, ou écrivez-nous directement sur WhatsApp.</p>
            <a href="https://wa.me/24166131270?text={{ rawurlencode('Bonjour, je souhaite contacter l\'équipe SETUP-Gabon.') }}" target="_blank" rel="noopener"
               class="mt-6 inline-flex h-12 px-6 rounded-lg bg-white text-blue-800 hover:bg-blue-50 font-semibold items-center gap-2 transition-colors">
                <i class="fa-brands fa-whatsapp"></i>
                Écrire sur WhatsApp
            </a>
        </div>
        <dl class="grid sm:grid-cols-2 gap-6 text-sm">
            <div>
                <dt class="text-blue-200">Adresse</dt>
                <dd class="mt-1 font-semibold text-base">Petit Paris, galerie Mouenga<br>Libreville, Gabon</dd>
            </div>
            <div>
                <dt class="text-blue-200">Horaires</dt>
                <dd class="mt-1 font-semibold text-base">Mardi – dimanche<br><span class="chiffres">07h30 – 20h00</span></dd>
            </div>
            <div>
                <dt class="text-blue-200">Téléphone</dt>
                <dd class="mt-1 font-semibold text-base"><a href="tel:+24166131270" class="chiffres hover:underline">+241 66 13 12 70</a></dd>
            </div>
            <div>
                <dt class="text-blue-200">E-mail</dt>
                <dd class="mt-1 font-semibold text-base break-all"><a href="mailto:protocolees@gmail.com" class="hover:underline">protocolees@gmail.com</a></dd>
            </div>
        </dl>
    </section>

</div>
@endsection
