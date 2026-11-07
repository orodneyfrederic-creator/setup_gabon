@extends('layouts.auth', ['title' => 'Mot de passe oublié'])

@section('content')
<div class="w-full max-w-md">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 sm:p-8">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Mot de passe oublié</h1>
        <p class="text-sm text-slate-500 mt-1 mb-6">Indiquez l'adresse e-mail de votre compte : nous vous envoyons un lien pour choisir un nouveau mot de passe.</p>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5" novalidate>
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-800 mb-1.5">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" inputmode="email"
                       class="w-full h-12 px-3.5 bg-white border rounded-lg text-[15px] text-slate-900 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition @error('email') border-rose-500 @else border-slate-300 @enderror">
                @error('email')
                    <p class="text-rose-600 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full h-12 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg transition-colors">
                Recevoir le lien
            </button>
        </form>
    </div>

    <p class="text-center text-sm text-slate-600 mt-6">
        Vous vous en souvenez ?
        <a href="{{ route('login') }}" class="font-semibold text-blue-700 hover:text-blue-800 underline">Se connecter</a>
    </p>
    <p class="text-center text-xs text-slate-500 mt-3">
        Pas d'accès à votre e-mail ? Écrivez à la boutique sur
        <a href="https://wa.me/{{ config('boutique.whatsapp') }}" target="_blank" rel="noopener" class="font-semibold text-blue-700 underline">WhatsApp</a>.
    </p>
</div>
@endsection
