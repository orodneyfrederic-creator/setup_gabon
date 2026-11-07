@extends('legal.layout')

@section('title', 'Mentions légales')
@section('meta_description', 'Mentions légales du site SETUP-Gabon : éditeur, contact, hébergement et propriété intellectuelle.')
@section('titre_legal', 'Mentions légales')

@section('contenu_legal')
    <h2 id="editeur">Éditeur du site et vendeur</h2>
    <ul>
        <li><strong>Nom commercial :</strong> {{ $boutique['nom_commercial'] }}</li>
        @if($boutique['forme_juridique'])
            <li><strong>Forme juridique :</strong> {{ $boutique['forme_juridique'] }}</li>
        @endif
        @if($boutique['rccm'])
            <li><strong>RCCM :</strong> {{ $boutique['rccm'] }}</li>
        @endif
        @if($boutique['nif'])
            <li><strong>NIF :</strong> {{ $boutique['nif'] }}</li>
        @endif
        <li><strong>Adresse :</strong> {{ $boutique['adresse'] }}</li>
        <li><strong>Téléphone et WhatsApp :</strong> <a href="tel:+{{ $boutique['whatsapp'] }}">{{ $boutique['telephone'] }}</a></li>
        <li><strong>E-mail :</strong> <a href="mailto:{{ $boutique['email'] }}">{{ $boutique['email'] }}</a></li>
        <li><strong>Horaires :</strong> {{ $boutique['horaires'] }}</li>
        @if($boutique['responsable_publication'])
            <li><strong>Responsable de la publication :</strong> {{ $boutique['responsable_publication'] }}</li>
        @endif
    </ul>
    <p class="reference-loi">Loi gabonaise n°025/2021 sur les transactions électroniques, art. 23 et 31 : accès facile, direct et permanent aux informations d'identification.</p>

    @if($boutique['hebergeur']['nom'])
        <h2 id="hebergeur">Hébergement</h2>
        <p>
            {{ $boutique['hebergeur']['nom'] }}@if($boutique['hebergeur']['adresse']), {{ $boutique['hebergeur']['adresse'] }}@endif.
        </p>
    @endif

    <h2 id="propriete">Propriété intellectuelle</h2>
    <p>
        Le nom, le logo SETUP-Gabon, les textes et les photographies prises en boutique sont la propriété de {{ $boutique['nom_commercial'] }}.
        Toute reproduction sans autorisation est interdite. Les marques citées (HP, Dell, Lenovo, Apple, Canon…) appartiennent à leurs titulaires respectifs
        et ne sont mentionnées que pour désigner les produits vendus.
    </p>

    <h2 id="donnees">Données personnelles</h2>
    <p>Voir la <a href="{{ route('legal.confidentialite') }}">politique de confidentialité</a>.</p>

    <h2 id="droit">Droit applicable</h2>
    <p>Le site et son contenu sont soumis au droit gabonais.</p>
@endsection
