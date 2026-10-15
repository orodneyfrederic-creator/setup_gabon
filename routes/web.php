<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Gerante\AnalyticsController;
use App\Http\Controllers\Gerante\CategorieController;
use App\Http\Controllers\Gerante\ClientGeranteController;
use App\Http\Controllers\Gerante\DevisController;
use App\Http\Controllers\Gerante\FactureController;
use App\Http\Controllers\Gerante\MarqueController;
use App\Http\Controllers\Gerante\PosController;
use App\Http\Controllers\Gerante\ProduitGeranteController;
use App\Http\Controllers\Gerante\SavController;
use App\Http\Controllers\Gerante\StockController;
use App\Http\Controllers\GeranteController;
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
Route::get('/categories', [ProduitController::class, 'categories'])->name('categories.index');
Route::get('/produits/{id}', [ProduitController::class, 'show'])->name('produits.show');
Route::get('/produits/{id}/video', [ProduitController::class, 'streamVideo'])->name('produits.video');

/*
|--------------------------------------------------------------------------
| 2. ROUTES GUEST (Utilisateurs non connectés)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
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
});
