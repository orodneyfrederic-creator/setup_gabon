@extends('legal.layout')

@section('title', 'Politique de confidentialité')
@section('meta_description', 'Comment SETUP-Gabon collecte et protège vos données personnelles, et comment exercer vos droits (loi gabonaise n°001/2011 modifiée).')
@section('titre_legal', 'Politique de confidentialité')

@section('resume_legal')
    Nous collectons uniquement ce qui sert à traiter et livrer vos commandes. Nous ne vendons pas vos données et ne vous envoyons pas de publicité sans votre accord.
    Vous pouvez à tout moment consulter, corriger ou faire supprimer vos informations.
@endsection

@section('contenu_legal')
    <h2 id="responsable">1. Responsable du traitement</h2>
    <p>
        Le responsable du traitement est {{ $boutique['nom_commercial'] }}, {{ $boutique['adresse'] }}
        (voir les <a href="{{ route('legal.mentions') }}">mentions légales</a>). Contact : <a href="mailto:{{ $boutique['email'] }}">{{ $boutique['email'] }}</a>
        ou {{ $boutique['telephone'] }}.
    </p>
    <p class="reference-loi">
        Cadre légal : loi gabonaise n°001/2011 du 25 septembre 2011 relative à la protection des données à caractère personnel, modifiée par la loi n°025/2023 du 9 juillet 2023.
        @if($boutique['apdpvp_recepisse'])
            Traitement déclaré auprès de l'APDPVP sous le n° {{ $boutique['apdpvp_recepisse'] }}.
        @endif
    </p>

    <h2 id="donnees">2. Données collectées</h2>
    <table>
        <thead>
            <tr>
                <th scope="col">Quand</th>
                <th scope="col">Données</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Création du compte</td>
                <td>Nom, prénom, adresse e-mail, téléphone (facultatif), mot de passe (enregistré sous forme chiffrée, illisible y compris pour la boutique)</td>
            </tr>
            <tr>
                <td>Commande</td>
                <td>Produits, quantités, prix, adresse de livraison, mode de paiement choisi, date et version des conditions de vente acceptées</td>
            </tr>
            <tr>
                <td>Commande sans compte</td>
                <td>Nom, téléphone et, si vous le donnez, adresse e-mail, enregistrés avec la commande</td>
            </tr>
            <tr>
                <td>Avis sur un produit</td>
                <td>Note, commentaire et date. Sur le site, l'avis est affiché avec votre prénom et l'initiale de votre nom uniquement</td>
            </tr>
            <tr>
                <td>Négociation de prix</td>
                <td>Offres proposées et réponses, avec leur date</td>
            </tr>
            <tr>
                <td>Navigation</td>
                <td>Cookies techniques de session et de sécurité ; contenu du panier, enregistré uniquement dans votre navigateur</td>
            </tr>
        </tbody>
    </table>
    <p>
        Aucune donnée bancaire et aucun code secret de mobile money ne sont collectés : aucun paiement n'est effectué sur le site.
        Ne communiquez jamais votre code secret, ni à la boutique ni à personne.
    </p>

    <h2 id="finalites">3. Pourquoi nous les utilisons</h2>
    <ul>
        <li><strong>Gérer votre compte et vos commandes</strong> : enregistrement, confirmation, livraison, suivi, service après-vente. C'est nécessaire à l'exécution de la vente.</li>
        <li><strong>Publier votre avis</strong>, si vous choisissez d'en laisser un, pour informer les autres clients. Vous pouvez le modifier ou demander sa suppression.</li>
        <li><strong>Vous écrire au sujet de vos commandes</strong> : accusé de réception, confirmation, livraison ou annulation, et lien de réinitialisation du mot de passe à votre demande. Ce sont des messages de service, pas de la publicité.</li>
        <li><strong>Respecter nos obligations légales</strong> : conservation des commandes et pièces comptables, preuve de l'acceptation des conditions de vente.</li>
        <li><strong>Sécuriser le site</strong> : authentification, protection contre les envois de formulaires frauduleux.</li>
    </ul>
    <p>
        Nous ne vous adressons pas de messages publicitaires sans votre accord préalable, et chaque message de ce type vous permet de demander à ne plus en recevoir.
    </p>
    <p class="reference-loi">Loi n°025/2021 sur les transactions électroniques, art. 36 et 37 (prospection électronique et consentement préalable).</p>

    <h2 id="destinataires">4. Qui y a accès</h2>
    <ul>
        <li>La gérante et le personnel de la boutique, pour traiter vos commandes ;</li>
        <li>L'hébergeur du site, qui stocke les données pour notre compte ;</li>
        <li>Google (Gmail), qui achemine les e-mails que nous vous envoyons ;</li>
        <li>WhatsApp (Meta), uniquement lorsque vous choisissez d'envoyer votre commande ou un message par ce service depuis votre téléphone.</li>
    </ul>
    <p>Vos données ne sont ni vendues ni louées.</p>
    <p>
        Pour s'afficher, le site charge des polices et des outils techniques depuis des services spécialisés (Google Fonts, cdnjs, jsDelivr),
        qui reçoivent à cette occasion l'adresse IP de votre appareil. Aucune donnée de votre compte ne leur est transmise.
    </p>

    <h2 id="conservation">5. Durée de conservation</h2>
    <table>
        <thead>
            <tr>
                <th scope="col">Données</th>
                <th scope="col">Durée</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Compte client</td>
                <td>Tant que le compte est utilisé, puis suppression sur demande</td>
            </tr>
            <tr>
                <td>Commandes, factures et pièces comptables</td>
                <td>10 ans (obligation comptable du droit OHADA)</td>
            </tr>
            <tr>
                <td>Avis sur un produit</td>
                <td>Tant que le produit est au catalogue, ou jusqu'à ce que vous demandiez sa suppression</td>
            </tr>
            <tr>
                <td>Négociations de prix</td>
                <td>Pendant la durée de la négociation et de l'offre, puis rattachées à la commande qui l'utilise</td>
            </tr>
            <tr>
                <td>Cookie de session</td>
                <td>{{ config('session.lifetime') }} minutes d'inactivité</td>
            </tr>
            <tr>
                <td>Panier (dans votre navigateur)</td>
                <td>Jusqu'à la commande ou jusqu'à ce que vous le vidiez</td>
            </tr>
        </tbody>
    </table>

    <h2 id="cookies">6. Cookies</h2>
    <p>
        Le site utilise uniquement des cookies techniques, indispensables à son fonctionnement : le cookie de session (rester connecté) et le cookie de sécurité
        des formulaires. Il n'utilise ni cookie publicitaire, ni outil de mesure d'audience. Votre panier est conservé dans la mémoire de votre navigateur,
        sur votre appareil.
    </p>

    <h2 id="securite">7. Sécurité</h2>
    <p>
        Les mots de passe sont chiffrés et ne sont jamais lisibles. L'espace de gestion est réservé au personnel de la boutique et protégé par un accès séparé.
        Si une violation de données venait à se produire, l'autorité de contrôle en serait informée sans délai.
    </p>

    <h2 id="droits">8. Vos droits</h2>
    <p>Vous pouvez à tout moment demander :</p>
    <ul>
        <li>à <strong>accéder</strong> à vos données et en obtenir une copie ;</li>
        <li>à les <strong>corriger ou compléter</strong> (vous pouvez aussi le faire vous-même dans « Mon compte ») ;</li>
        <li>à les faire <strong>supprimer</strong>, sous réserve des données que la loi nous oblige à conserver ;</li>
        <li>à vous <strong>opposer</strong> à leur utilisation pour un motif légitime.</li>
    </ul>
    <p>
        Écrivez-nous à <a href="mailto:{{ $boutique['email'] }}">{{ $boutique['email'] }}</a>, sur WhatsApp au {{ $boutique['telephone'] }} ou en boutique,
        en justifiant de votre identité. Nous répondons dans les meilleurs délais.
    </p>
    <p>
        Si vous estimez que vos droits ne sont pas respectés, vous pouvez saisir l'<strong>Autorité pour la Protection des Données Personnelles et de la Vie Privée (APDPVP)</strong>,
        Haut de Gué-Gué, en face de l'entrée de service de l'hôtel Radisson Blu, Libreville. Téléphone : +241 01 44 31 34.
        Site : <a href="https://www.apdpvp.ga" target="_blank" rel="noopener">apdpvp.ga</a>.
    </p>

    <h2 id="modifications">9. Modifications</h2>
    <p>Cette politique peut évoluer. La date de dernière mise à jour figure en haut de la page.</p>
@endsection
