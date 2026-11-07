@extends('legal.layout')

@section('title', 'Retours et garanties')
@section('meta_description', 'Retours et garanties chez SETUP-Gabon : 14 jours pour changer d\'avis, garantie de 1 mois sur les ordinateurs et de 2 semaines sur les autres produits.')
@section('titre_legal', 'Retours et garanties')

@section('resume_legal')
    Vous avez <strong>{{ $boutique['retractation_jours'] }} jours</strong> pour changer d'avis après réception.
    En cas de panne, la boutique répare, échange ou rembourse pendant la durée de garantie du produit.
@endsection

@section('contenu_legal')
    <h2 id="delais">Les délais en un coup d'œil</h2>
    <table>
        <thead>
            <tr>
                <th scope="col">Situation</th>
                <th scope="col">Délai</th>
                <th scope="col">Point de départ</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Changer d'avis (rétractation)</td>
                <td><strong>{{ $boutique['retractation_jours'] }} jours</strong></td>
                <td>Réception du produit</td>
            </tr>
            <tr>
                <td>Garantie boutique : ordinateurs portables et fixes</td>
                <td><strong>1 mois</strong></td>
                <td>Réception du produit</td>
            </tr>
            <tr>
                <td>Garantie boutique : autres produits</td>
                <td><strong>2 semaines</strong></td>
                <td>Réception du produit</td>
            </tr>
            <tr>
                <td>Remboursement après rétractation</td>
                <td><strong>{{ $boutique['remboursement_jours'] }} jours</strong> au plus</td>
                <td>Information de votre décision</td>
            </tr>
        </tbody>
    </table>

    <h2 id="changer-avis">Vous changez d'avis</h2>
    <ol>
        <li>Prévenez la boutique avant la fin des {{ $boutique['retractation_jours'] }} jours : par WhatsApp au {{ $boutique['telephone'] }}, par e-mail ou en boutique. Indiquez votre numéro de commande.</li>
        <li>Rapportez le produit à la boutique ({{ $boutique['adresse'] }}), ou renvoyez-le, dans les {{ $boutique['retractation_jours'] }} jours qui suivent. Il doit être complet : accessoires, câbles, notice et emballage d'origine.</li>
        <li>Vous êtes remboursé de la totalité des sommes versées, par le même moyen de paiement, au plus tard {{ $boutique['remboursement_jours'] }} jours après avoir prévenu la boutique. Le remboursement peut attendre le retour du produit.</li>
    </ol>
    <p>
        Vous n'avez pas à vous justifier. Seuls d'éventuels frais de renvoi restent à votre charge : un retour directement en boutique est gratuit.
    </p>
    <p class="reference-loi">Loi gabonaise n°025/2021 sur les transactions électroniques, art. 57 à 60.</p>

    <h2 id="panne">Votre produit tombe en panne</h2>
    <ol>
        <li>Contactez la boutique pendant la durée de garantie, avec votre numéro de commande et une courte description du problème (une photo ou une vidéo aide).</li>
        <li>Apportez le produit à la boutique pour diagnostic.</li>
        <li>Si la panne est couverte, la boutique le répare ou l'échange ; si aucune des deux solutions n'est possible, elle vous rembourse.</li>
    </ol>
    <h3>Ce que la garantie ne couvre pas</h3>
    <ul>
        <li>Une chute, un choc ou un écran cassé ;</li>
        <li>Un contact avec un liquide ou l'humidité ;</li>
        <li>Une surtension électrique (coupure, orage) ;</li>
        <li>Un produit ouvert ou réparé par une personne extérieure à la boutique ;</li>
        <li>L'usure normale des consommables (cartouches, batteries usées par l'usage).</li>
    </ul>
    <p>
        Ces règles s'ajoutent à la garantie légale contre les vices cachés et, le cas échéant, à la garantie du fabricant, que la boutique vous aide à faire valoir.
    </p>

    <h2 id="livraison">Un problème à la livraison</h2>
    <p>
        Vérifiez votre commande à la réception. Un produit manquant, abîmé ou différent de celui commandé doit être signalé tout de suite à la boutique :
        il est remplacé ou remboursé.
    </p>

    <h2 id="contact">Nous contacter</h2>
    <ul>
        <li>WhatsApp et téléphone : <a href="https://wa.me/{{ $boutique['whatsapp'] }}" target="_blank" rel="noopener">{{ $boutique['telephone'] }}</a></li>
        <li>E-mail : <a href="mailto:{{ $boutique['email'] }}">{{ $boutique['email'] }}</a></li>
        <li>Boutique : {{ $boutique['adresse'] }}, {{ $boutique['horaires'] }}</li>
    </ul>
    <p>Les règles complètes figurent dans les <a href="{{ route('legal.cgv') }}">conditions générales de vente</a>.</p>
@endsection
