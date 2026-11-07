<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Partage la variable $categories avec toutes les vues Blade, chargée une seule fois par requête
        View::composer('*', function ($view) {
            $view->with('categories', once(fn () => Category::all()));
        });

        // Connexion : 5 essais par minute pour un même e-mail depuis une même adresse IP
        RateLimiter::for('connexion', function (Request $request) {
            return Limit::perMinute(5)
                ->by(Str::lower((string) $request->input('email')).'|'.$request->ip())
                ->response(fn () => back()->withInput($request->only('email'))->withErrors([
                    'email' => 'Trop de tentatives de connexion. Patientez une minute avant de réessayer.',
                ]));
        });
    }
}
