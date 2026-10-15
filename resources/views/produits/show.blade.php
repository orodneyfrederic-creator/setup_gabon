@extends('layouts.app')

@section('content')

<!-- Fil d'ariane -->
<nav class="flex mb-6 text-sm text-slate-500 font-medium">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                <i class="fa-solid fa-house mr-2"></i>
                Accueil
            </a>
        </li>
        @if($produit->category)
        <li>
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                <a href="{{ route('home', ['category' => $produit->id_cat]) }}" class="hover:text-blue-600 transition-colors">{{ $produit->category->nom }}</a>
            </div>
        </li>
        @endif
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
                <span class="text-slate-800 line-clamp-1">{{ $produit->designation }}</span>
            </div>
        </li>
    </ol>
</nav>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 md:p-10">
        
        <!-- Colonne Gauche : Image -->
        <div class="bg-slate-50 rounded-xl flex items-center justify-center p-8 relative min-h-[300px]">
            @if($produit->image)
                <img src="{{ asset('storage/' . $produit->image) }}" alt="{{ $produit->designation }}" class="max-w-full max-h-[400px] object-contain">
            @else
                <div class="text-center text-slate-400">
                    <i class="fa-solid fa-image text-6xl mb-3 block opacity-50"></i>
                    <span class="font-semibold">Image non disponible</span>
                </div>
            @endif
            @if($produit->video)
                <div class="absolute top-3 right-3">
                    <span class="inline-flex items-center gap-1 bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow">
                        <i class="fa-solid fa-video"></i> Vidéo
                    </span>
                </div>
            @endif
        </div>

        <!-- Colonne Droite : Détails -->
        <div class="flex flex-col justify-center">
            
            <div class="mb-2 flex items-center gap-3 flex-wrap">
                @if($produit->category)
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-md border border-blue-200">
                    {{ $produit->category->nom }}
                </span>
                @endif
                @if($produit->marque)
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700 bg-slate-100 px-3 py-1 rounded-md border border-slate-200">
                    {{ $produit->marque->nom }}
                </span>
                @endif
                <span class="text-xs text-slate-500 font-medium">
                    <i class="fa-solid fa-barcode mr-1"></i> Réf: {{ $produit->reference }}
                </span>
            </div>

            <h1 class="text-3xl font-black text-slate-900 leading-tight mb-4">
                {{ $produit->designation }}
            </h1>

            <div class="text-4xl font-black text-slate-900 mb-6">
                {{ number_format($produit->prix_unitaire, 0, ',', ' ') }} <span class="text-xl font-bold text-blue-700">FCFA</span>
            </div>

            <div class="mb-6">
                @if($produit->quantite > 0)
                    <div class="inline-flex items-center gap-2 text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg font-bold text-sm">
                        <i class="fa-solid fa-circle-check"></i>
                        En stock ({{ $produit->quantite }} disponible{{ $produit->quantite > 1 ? 's' : '' }})
                    </div>
                @else
                    <div class="inline-flex items-center gap-2 text-red-700 bg-red-50 border border-red-200 px-4 py-2 rounded-lg font-bold text-sm">
                        <i class="fa-solid fa-circle-xmark"></i>
                        En rupture de stock
                    </div>
                @endif
            </div>

            @if($produit->description)
            <div class="mb-8">
                <h3 class="text-sm font-bold text-slate-800 mb-2 uppercase tracking-wide">Description du produit</h3>
                <div class="prose prose-sm text-slate-600">
                    {!! nl2br(e($produit->description)) !!}
                </div>
            </div>
            @endif

            <hr class="border-slate-100 mb-8">

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex items-center border border-slate-300 rounded-xl bg-white overflow-hidden w-full sm:w-auto h-[50px]">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-colors h-full" onclick="document.getElementById('qte').stepDown()">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                     <input type="number" id="qte" name="quantite" value="1" min="1" max="{{ $produit->quantite > 1 ? $produit->quantite : 1 }}" class="w-16 text-center font-bold text-slate-800 border-x border-slate-200 focus:outline-none h-full" {{ $produit->quantite == 0 ? 'disabled' : '' }} oninput="if(this.value > {{ $produit->quantite }}) this.value = {{ $produit->quantite }}; if(this.value !== '' && this.value < 1) this.value = 1;">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-blue-600 hover:bg-slate-50 transition-colors h-full" onclick="document.getElementById('qte').stepUp()">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                
                <button type="button"
                        onclick='ajouterAuPanierBoutique({{ $produit->id }}, {{ json_encode($produit->designation) }}, {{ $produit->prix_unitaire }}, {{ $produit->quantite }}, parseInt(document.getElementById("qte").value || 1))'
                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-xl shadow-md hover:shadow-blue-600/25 transition-all flex items-center justify-center gap-2 h-[50px] {{ $produit->quantite == 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $produit->quantite == 0 ? 'disabled' : '' }}>
                    <i class="fa-solid fa-cart-plus text-lg"></i>
                    <span>Ajouter au panier</span>
                </button>
            </div>

            <!-- WhatsApp Order -->
            @if(Auth::check() || Auth::guard('staff')->check())
                <a href="https://wa.me/24166131270?text={{ urlencode('Bonjour SETUP-Gabon, je souhaite commander le ' . $produit->designation) }}" target="_blank" class="mt-4 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 h-[50px]">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                    <span>Commander via WhatsApp</span>
                </a>
            @else
                <a href="{{ route('checkout.index') }}" class="mt-4 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 h-[50px]">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                    <span>Commander via WhatsApp</span>
                </a>
            @endif

            @if($produit->video)
            <!-- Bouton Vidéo -->
            <button type="button" onclick="openVideoModal()"
               class="mt-2 w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 h-[50px]">
                <i class="fa-solid fa-circle-play text-xl"></i>
                <span>Visionner la vidéo</span>
            </button>
            @endif

        </div>
    </div>
</div>

@if($produit->video)
<!-- Modal Vidéo -->
<div id="videoModal" class="fixed inset-0 z-[99999] bg-black/95 flex items-center justify-center" style="display:none;" onclick="closeVideoModal()">
    <div class="relative w-[95%] max-w-4xl mx-auto" onclick="event.stopPropagation()">
        <!-- Bouton Fermer -->
        <button onclick="closeVideoModal()" class="absolute -top-12 right-0 text-white hover:text-red-500 transition-colors bg-transparent border-0 outline-none">
            <i class="fa-solid fa-xmark text-4xl"></i>
        </button>

        <!-- Conteneur Vidéo -->
        <div class="bg-black rounded-xl overflow-hidden shadow-2xl border border-slate-800">
            <video id="productVideo"
                   controls
                   playsinline
                   preload="none"
                   disablepictureinpicture
                   class="w-full bg-black outline-none"
                   style="display:block; width:100%; max-height:85vh;">
                <source src="{{ route('produits.video', $produit->id) }}" type="video/mp4">
                Votre navigateur ne supporte pas la balise vidéo.
            </video>
        </div>
    </div>
</div>

<script>
function openVideoModal() {
    var modal = document.getElementById('videoModal');
    var v = document.getElementById('productVideo');
    modal.style.display = 'flex';
    v.load();
    v.play().catch(function(e) { console.warn('Autoplay bloqué:', e); });
}

function closeVideoModal() {
    var modal = document.getElementById('videoModal');
    var v = document.getElementById('productVideo');
    v.pause();
    v.currentTime = 0;
    modal.style.display = 'none';
}
</script>
@endif

@endsection

