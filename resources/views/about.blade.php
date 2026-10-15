@extends('layouts.app')

@section('content')
<div class="space-y-12 pb-16">
    
    <!-- HERO SECTION -->
    <section class="relative overflow-hidden text-white rounded-3xl p-8 sm:p-12 shadow-2xl borders">
        <!-- Image de fond avec flou et overlay sur la forme -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat filter blur-sm scale-105 opacity-30 pointer-events-none" style="background-image: url('{{ asset('img/font_app.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-blue-950/90 via-slate-950/85 to-blue-950/90 pointer-events-none"></div>  

        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <!-- <i class="fa-solid fa-laptop-code text-[280px]"></i> -->
        </div>
        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold uppercase tracking-wider border border-blue-400/30">
                <i class="fa-solid fa-shield-halved text-blue-400"></i>
                <span>Leader Informatique au Gabon</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                À propos de <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">SETUP-Gabon</span>
            </h1>
            <p class="text-slate-300 text-base sm:text-lg leading-relaxed font-light">
                Votre partenaire technologique de confiance pour l'équipement informatique, le matériel High-Tech et l'accompagnement d'excellence à Libreville et partout au Gabon.
            </p>
        </div>
    </section>

    <!-- BANNIÈRE 2 : SECTION QUI SOMMES-NOUS ? & POURQUOI CHOISIR -->
    <section class="relative overflow-hidden text-white rounded-3xl p-8 sm:p-12 shadow-2xl border">
        <!-- Image de fond avec flou et overlay sur la forme 2 -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat filter blur-sm scale-105 opacity-25 pointer-events-none" style="background-image: url('{{ asset('img/font_app.jpg') }}');"></div>
         <div class="absolute inset-0 bg-gradient-to-r from-blue-950/95 via-slate-950/90 to-blue-950/95 pointer-events-none"></div> 

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7 space-y-4">
                <div class="inline-flex items-center gap-2 text-blue-400 font-bold text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-building text-blue-400"></i>
                    <span>Qui sommes-nous ?</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-white">
                    L'Excellence du Matériel Informatique & des Services High-Tech
                </h2>
                <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                    <strong class="text-white">SETUP-Gabon</strong> est une entreprise spécialisée dans la vente de matériel informatique, d'équipements de bureau, d'accessoires de pointe et de solutions technologiques sur mesure.
                </p>
                <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                    Que vous soyez un particulier à la recherche d'un ordinateur portable performant, un professionnel soucieux d'optimiser son poste de travail ou une entreprise nécessitant une infrastructure robuste, nous vous fournissons des équipements de marques internationales prestigieuses (HP, Dell, Lenovo, Apple, Canon, etc.).
                </p>
                <div class="pt-2 grid grid-cols-2 gap-4">
                    <div class="p-4 bg-blue-900/40 rounded-2xl border border-blue-800/50 flex items-center gap-3 backdrop-blur-sm">
                        <div class="w-10 h-10 rounded-xl bg-blue-600/30 text-blue-300 flex items-center justify-center font-bold shrink-0 border border-blue-500/30">
                            <i class="fa-solid fa-check-double text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white">Produits Certifiés</h4>
                            <p class="text-xs text-slate-300">Garantis d'origine constructeur</p>
                        </div>
                    </div>
                    <div class="p-4 bg-blue-900/40 rounded-2xl border border-blue-800/50 flex items-center gap-3 backdrop-blur-sm">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600/30 text-emerald-300 flex items-center justify-center font-bold shrink-0 border border-emerald-500/30">
                            <i class="fa-solid fa-headset text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white">SAV & Support</h4>
                            <p class="text-xs text-slate-300">Service après-vente réactif</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-5">
                <div class="relative rounded-2xl p-6 sm:p-8 text-white bg-blue-900/30 border border-blue-800/60 backdrop-blur-md">
                    <div class="space-y-6">
                        <div class="w-12 h-12 rounded-2xl bg-blue-600/30 text-blue-400 flex items-center justify-center font-bold text-xl border border-blue-500/30">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Pourquoi choisir SETUP-Gabon ?</h3>
                        <ul class="space-y-3 text-sm text-slate-300">
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-400 mt-1 text-xs shrink-0"></i>
                                <span>Large catalogue d'ordinateurs, imprimantes et composants.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-400 mt-1 text-xs shrink-0"></i>
                                <span>Facturation officielle (Proforma & Facture définitive).</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-400 mt-1 text-xs shrink-0"></i>
                                <span>Commande simple et directe via WhatsApp ou en magasin.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-400 mt-1 text-xs shrink-0"></i>
                                <span>Équipe technique disponible pour l'assistance et la maintenance.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BANNIÈRE 3 : SECTION NOTRE MISSION -->
    <section class="relative overflow-hidden text-white rounded-3xl p-8 sm:p-12 shadow-2xl border">
        <!-- Image de fond avec flou et overlay sur la forme 3 -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat filter blur-sm scale-105 opacity-25 pointer-events-none" style="background-image: url('{{ asset('img/font_app.jpg') }}');"></div>
         <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-blue-950/90 to-slate-950/95 pointer-events-none"></div> 

        <div class="relative z-10 space-y-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 text-blue-400 font-bold text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-bullseye text-blue-400"></i>
                    <span>Notre Mission</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-white mt-1">
                    Démocratiser l’Accès au Matériel Informatique de Haute Qualité
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl bg-blue-950/50 border border-blue-900/60 backdrop-blur-sm space-y-3 transition-transform hover:-translate-y-1">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-md">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <h3 class="font-bold text-white text-base">Équipements de Qualité</h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Fournir du matériel authentique, robuste et adapté à la fois aux défis professionnels et aux besoins du quotidien.
                    </p>
                </div>
                <div class="p-6 rounded-2xl bg-blue-950/50 border border-blue-900/60 backdrop-blur-sm space-y-3 transition-transform hover:-translate-y-1">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold shadow-md">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <h3 class="font-bold text-white text-base">Accompagnement Sur Mesure</h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Conseiller chaque client afin qu'il choisisse la configuration idéale correspondant exactement à ses exigences technologiques et budgétaires.
                    </p>
                </div>
                <div class="p-6 rounded-2xl bg-blue-950/50 border border-blue-900/60 backdrop-blur-sm space-y-3 transition-transform hover:-translate-y-1">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shadow-md">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <h3 class="font-bold text-white text-base">Partenariat Durable</h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Construire une relation de confiance basée sur la transparence, le suivi régulier et le support technique réactif.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION : NOS VALEURS -->
    <section class="space-y-6">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <div class="inline-flex items-center gap-2 text-blue-600 font-bold text-xs uppercase tracking-wider">
                <i class="fa-solid fa-heart text-blue-600"></i>
                <span>Nos Valeurs</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">
                Ce qui anime l'équipe SETUP-Gabon
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm">
                Des principes solides guidant chacune de nos actions au quotidien.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Valeur 1 -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-3 text-center">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl mx-auto">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Fiabilité</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Des produits contrôlés, authentiques et garantis pour assurer votre sérénité professionnelle.
                </p>
            </div>

            <!-- Valeur 2 -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-3 text-center">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl mx-auto">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Innovation</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Toujours à la pointe des dernières nouveautés technologiques du marché international.
                </p>
            </div>

            <!-- Valeur 3 -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-3 text-center">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl mx-auto">
                    <i class="fa-solid fa-face-smile"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Service Client</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Une écoute attentive, un accueil chaleureux et des réponses rapides à vos besoins.
                </p>
            </div>

            <!-- Valeur 4 -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-3 text-center">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xl mx-auto">
                    <i class="fa-solid fa-tag"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Accessibilité</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Un rapport qualité-prix compétitif avec des offres adaptées à tous les budgets.
                </p>
            </div>
        </div>
    </section>

    <!-- SECTION : CTA (CALL TO ACTION) -->
    <section class="relative text-white rounded-3xl p-8 sm:p-12 text-center shadow-xl overflow-hidden bg-slate-950 border border-slate-800">
        <!-- Image de fond avec flou et overlay -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat filter blur-sm scale-105 opacity-30 pointer-events-none" style="background-image: url('{{ asset('img/font_app.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-blue-950/90 via-slate-950/85 to-blue-950/90 pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl mx-auto space-y-6">
            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                Prêt à vous équiper avec SETUP-Gabon ?
            </h2>
            <p class="text-blue-200 text-xs sm:text-sm leading-relaxed">
                Explorez notre catalogue de produits ou contactez directement nos conseillers pour obtenir une proposition ou un devis personnalisé.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm rounded-2xl shadow-lg transition-transform hover:scale-105 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-store"></i>
                    <span>Découvrir le Catalogue</span>
                </a>
                <a href="https://wa.me/24166131270?text={{ urlencode('Bonjour, je souhaite contacter l\'équipe SETUP-Gabon.') }}" target="_blank" class="w-full sm:w-auto px-6 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm rounded-2xl shadow-lg transition-transform hover:scale-105 flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                    <span>Nous contacter sur WhatsApp</span>
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
