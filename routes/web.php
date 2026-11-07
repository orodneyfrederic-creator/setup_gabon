<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Gerante\AnalyticsController;
use App\Http\Controllers\Gerante\CategorieController;
use App\Http\Controllers\Gerante\ClientGeranteController;
use App\Http\Controllers\Gerante\CommandeGeranteController;
use App\Http\Controllers\Gerante\DevisController;
use App\Http\Controllers\Gerante\FactureController;
use App\Http\Controllers\Gerante\MarqueController;
use App\Http\Controllers\Gerante\NegociationGeranteController;
use App\Http\Controllers\Gerante\PosController;
use App\Http\Controllers\Gerante\ProduitGeranteController;
use App\Http\Controllers\Gerante\SavController;
use App\Http\Controllers\Gerante\StockController;
use App\Http\Controllers\GeranteController;
use App\Http\Controllers\MotDePasseOublieController;
use App\Http\Controllers\NegociationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProduitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. ROUTES WEB PUBLIQUES (Visiteurs / Invités & Connectés)
|--------------------------------------------------------------------------
*/
Route::get('/', [ProduitController::class, 'index'])->name('home');
Route::get('/a-propos', [PageController::class, 'about'])->name('about');

// Pages légales (loi n°025/2021 sur les transactions électroniques, loi n°001/2011 modifiée sur les données personnelles)
Route::get('/conditions-generales-de-vente', [PageController::class, 'cgv'])->name('legal.cgv');
Route::get('/mentions-legales', [PageController::class, 'mentionsLegales'])->name('legal.mentions');
Route::get('/confidentialite', [PageController::class, 'confidentialite'])->name('legal.confidentialite');
Route::get('/retours-et-garanties', [PageController::class, 'retoursGaranties'])->name('legal.retours');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/categories', [ProduitController::class, 'categories'])->name('categories.index');
Route::get('/produits/{id}', [ProduitController::class, 'show'])->name('produits.show');
Route::get('/produits/{id}/video', [ProduitController::class, 'streamVideo'])->name('produits.video');
Route::get('/produits/{id}/apercu.jpg', [ProduitController::class, 'imagePartage'])->whereNumber('id')->name('produits.apercu');

/*
|--------------------------------------------------------------------------
| 2. ROUTES GUEST (Utilisateurs non connectés)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    // Limite les tentatives pour empêcher de deviner un mot de passe par essais successifs
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:connexion');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');

    // Mot de passe oublié (comptes clients)
    Route::get('/mot-de-passe-oublie', [MotDePasseOublieController::class, 'demande'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [MotDePasseOublieController::class, 'envoyerLien'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reinitialiser-mot-de-passe/{token}', [MotDePasseOublieController::class, 'formulaire'])->name('password.reset');
    Route::post('/reinitialiser-mot-de-passe', [MotDePasseOublieController::class, 'enregistrer'])->middleware('throttle:6,1')->name('password.update');
});

/*
|--------------------------------------------------------------------------
| 3. ROUTES PROTÉGÉES CLIENT & AUTHENTIFIÉS (Client Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:web,staff')->group(function () {
    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Espace Mon Compte Client
    Route::get('/mon-compte', [AccountController::class, 'profile'])->name('account.profile');
    Route::post('/mon-compte', [AccountController::class, 'updateProfile'])->name('account.profile.update');

    // Historique et Détails des Commandes
    Route::get('/commandes', [AccountController::class, 'orders'])->name('orders.index');
    Route::get('/commandes/{id}', [AccountController::class, 'showOrder'])->name('orders.show');

    // Négociation de Prix Client
    Route::post('/produits/{produit}/negocier', [NegociationController::class, 'initier'])->name('negociations.initier');
    Route::post('/negociations/{negociation}/offre', [NegociationController::class, 'proposer'])->middleware('throttle:15,1')->name('negociations.proposer');
    Route::post('/negociations/{negociation}/accepter', [NegociationController::class, 'accepterContreOffre'])->name('negociations.accepter');
    Route::post('/negociations/{negociation}/abandonner', [NegociationController::class, 'abandonner'])->name('negociations.abandonner');

    // Passage à la Caisse / Validation de Commande
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

/*
|--------------------------------------------------------------------------
| 4. ESPACE GÉRANTE / ADMINISTRATION (Réservé au rôle 'gerante')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:staff,web', 'role:gerante'])->prefix('gerante')->name('gerante.')->group(function () {

    Route::get('/dashboard', [GeranteController::class, 'dashboard'])->name('dashboard');

    // Devis
    Route::resource('devis', DevisController::class);
    Route::get('devis/{devi}/pdf', [DevisController::class, 'pdf'])->name('devis.pdf');
    Route::patch('devis/{devi}/status', [DevisController::class, 'status'])->name('devis.status');

    // Factures
    Route::get('factures', [FactureController::class, 'index'])->name('factures.index');
    Route::get('factures/{facture}', [FactureController::class, 'show'])->name('factures.show');
    Route::get('factures/{facture}/pdf', [FactureController::class, 'pdf'])->name('factures.pdf');
    Route::patch('factures/{facture}/status', [FactureController::class, 'status'])->name('factures.status');
    Route::post('devis/{devi}/facturer', [FactureController::class, 'createFromDevis'])->name('factures.create_from_devis');

    // Stock
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/{produit}/historique', [StockController::class, 'historique'])->name('stock.historique');
    Route::get('stock/{produit}/mouvement', [StockController::class, 'createMouvement'])->name('stock.create_mouvement');
    Route::post('stock/{produit}/mouvement', [StockController::class, 'storeMouvement'])->name('stock.store_mouvement');

    // Produits (avec Upload vidéo)
    Route::resource('produits', ProduitGeranteController::class);

    // Catégories
    Route::resource('categories', CategorieController::class)->except(['show']);

    // Marques
    Route::resource('marques', MarqueController::class)->except(['show']);

    // Clients
    Route::get('clients', [ClientGeranteController::class, 'index'])->name('clients.index');
    Route::get('clients/{id}', [ClientGeranteController::class, 'show'])->name('clients.show');

    // SAV
    Route::resource('sav', SavController::class)->except(['edit', 'destroy']);

    // Caisse (POS)
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('pos/{id}/receipt', [PosController::class, 'receipt'])->name('pos.receipt');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Négociations Gérante
    Route::get('negociations', [NegociationGeranteController::class, 'index'])->name('negociations.index');
    Route::get('negociations/regles', [NegociationGeranteController::class, 'regles'])->name('negociations.regles');
    Route::post('negociations/regles', [NegociationGeranteController::class, 'updateRegles'])->name('negociations.regles.update');
    Route::get('negociations/{negociation}', [NegociationGeranteController::class, 'show'])->name('negociations.show');
    Route::post('negociations/{negociation}/decision', [NegociationGeranteController::class, 'decider'])->name('negociations.decider');

    // Commandes en ligne Gérante (Confirmation et Sortie Stock)
    Route::get('commandes', [CommandeGeranteController::class, 'index'])->name('commandes.index');
    Route::get('commandes/{commande}', [CommandeGeranteController::class, 'show'])->name('commandes.show');
    Route::patch('commandes/{commande}/statut', [CommandeGeranteController::class, 'updateStatut'])->name('commandes.statut');
});
