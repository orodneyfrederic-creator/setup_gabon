@extends('legal.layout')

@section('title', 'Conditions générales de vente')
@section('meta_description', 'Conditions générales de vente de SETUP-Gabon : commande, prix, paiement, livraison à Libreville, droit de rétractation de 14 jours et garanties.')
@section('titre_legal', 'Conditions générales de vente')

@section('resume_legal')
    <strong>L'essentiel :</strong> livraison gratuite à Libreville sous {{ $boutique['livraison']['delai_heures'] }} h après confirmation,
    paiement à la réception en espèces, par Airtel Money ou par Moov Money, {{ $boutique['retractation_jours'] }} jours pour changer d'avis,
    garantie boutique de 1 mois sur les ordinateurs et de 2 semaines sur les autres produits.
@endsection

@section('contenu_legal')
    <h2 id="objet">1. Objet et acceptation</h2>
    <p>
        Les présentes conditions régissent les ventes conclues sur ce site entre {{ $boutique['nom_commercial'] }}
        (ci-après « la boutique », identifiée dans les <a href="{{ route('legal.mentions') }}">mentions légales</a>)
        et toute personne qui y passe commande (ci-après « le client »).
    </p>
    <p>
        Avant de valider sa commande, le client prend connaissance de ces conditions et les accepte en cochant la case prévue à cet effet.
        La version acceptée, ainsi que la date d'acceptation, sont enregistrées avec la commande. Seule la version en vigueur au jour de la commande s'applique.
    </p>
    <p class="reference-loi">Version {{ $boutique['version_conditions'] }}. Cadre légal : loi gabonaise n°025/2021 du 28 décembre 2021 portant réglementation des transactions électroniques.</p>

    <h2 id="produits">2. Produits</h2>
    <p>
        Chaque fiche produit présente les caractéristiques essentielles du produit, son prix, son état de stock et la durée de sa garantie.
        Les photos sont prises en boutique et montrent le produit vendu ; de légères différences d'aspect (couleur, reflets) peuvent subsister sans modifier ses caractéristiques.
    </p>
    <p>
        Le stock affiché est celui de la boutique au moment de la consultation. Si un produit n'est plus disponible au moment de la confirmation,
        la boutique en informe le client et la commande est annulée pour ce produit, sans frais.
    </p>

    <h2 id="prix">3. Prix</h2>
    <p>
        Les prix sont indiqués en francs CFA (FCFA). Le prix affiché est le prix à payer : il comprend les taxes applicables,
        et la livraison à {{ $boutique['livraison']['zone'] }} est gratuite. Aucun frais supplémentaire n'est ajouté au moment de la commande.
    </p>
    <p>
        Lorsqu'un prix a été obtenu par négociation sur le site, il s'applique au produit et à la quantité convenus, pendant la durée de validité indiquée
        au moment de l'accord (24 heures). Passé ce délai, ou si le prix catalogue du produit change, l'offre n'est plus valable et le prix catalogue s'applique.
    </p>
    <p class="reference-loi">Loi n°025/2021, art. 32 (affichage clair du prix, taxes et frais compris).</p>

    <h2 id="commande">4. Étapes de la commande</h2>
    <ol>
        <li>Le client ajoute des produits à son panier. Il peut à tout moment modifier les quantités ou retirer un produit.</li>
        <li>Il se connecte à son compte, ou commande sans compte en indiquant son nom, son téléphone et, s'il le souhaite, son adresse e-mail. La négociation de prix est réservée aux clients qui ont un compte.</li>
        <li>Sur la page « Finaliser ma commande », il vérifie le récapitulatif (produits, quantités, prix, total), indique son adresse de livraison et choisit son mode de paiement. Il peut encore corriger chacun de ces éléments.</li>
        <li>Il accepte les présentes conditions, puis confirme.</li>
        <li>La commande est immédiatement enregistrée : un accusé de réception récapitulant la commande s'affiche et est envoyé par e-mail (à l'adresse du compte, ou à celle indiquée lors d'une commande sans compte). La commande reste consultable à tout moment dans l'espace « Mes commandes » ou, sans compte, grâce au lien de suivi personnel affiché à la fin de la commande et rappelé dans l'e-mail et le message WhatsApp.</li>
        <li>Le client transmet sa commande à la boutique par WhatsApp en un clic. La boutique le recontacte pour confirmer la disponibilité et le rendez-vous de livraison.</li>
    </ol>
    <p>
        Le contrat est conclu en français. Les commandes sont archivées par la boutique et restent accessibles au client dans son compte ou par son lien de suivi.
        Ce lien donne accès à la commande sans mot de passe : le client ne doit le partager qu'avec des personnes de confiance.
    </p>
    <p class="reference-loi">Loi n°025/2021, art. 49, 51, 52 et 56 (étapes techniques, correction des erreurs, accusé de réception, confirmation).</p>

    <h2 id="paiement">5. Paiement</h2>
    <p>
        Aucun paiement n'est demandé en ligne, et aucune donnée bancaire n'est saisie sur le site. Le client règle à la livraison ou au retrait en boutique, au choix :
        en espèces, par Airtel Money ou par Moov Money.
    </p>
    <p>
        En cas de rétractation ou d'annulation après paiement, la boutique rembourse par le même moyen de paiement
        (<a href="#retractation">article 7</a>). Le produit reste la propriété de la boutique jusqu'au paiement complet.
    </p>

    <h2 id="livraison">6. Livraison et retrait</h2>
    <p>
        La livraison est gratuite à {{ $boutique['livraison']['zone'] }}. Elle intervient dans un délai maximal de
        <strong>{{ $boutique['livraison']['delai_heures'] }} heures</strong> après la confirmation de la commande par la boutique,
        à l'adresse indiquée par le client. Le client peut aussi retirer sa commande à la boutique ({{ $boutique['adresse'] }}), {{ $boutique['horaires'] }}.
    </p>
    <p>
        Le client vérifie le produit à la réception, en présence du livreur si possible, et signale immédiatement tout produit manquant ou abîmé.
    </p>
    <p>
        Si la commande n'est pas livrée dans les trente jours suivant sa réception, sauf force majeure, le client peut considérer la vente comme résolue
        et est remboursé de toute somme versée dans un délai de trente jours.
    </p>
    <p class="reference-loi">Loi n°025/2021, art. 62 et 63 (délai d'exécution de trente jours au plus, résolution et remboursement).</p>

    <h2 id="retractation">7. Droit de rétractation</h2>
    <p>
        Le client dispose de <strong>{{ $boutique['retractation_jours'] }} jours calendaires</strong> à compter de la réception du produit pour se rétracter,
        sans avoir à se justifier. Il informe la boutique de sa décision avant la fin de ce délai (WhatsApp, e-mail ou directement en boutique),
        puis rapporte ou renvoie le produit dans les {{ $boutique['retractation_jours'] }} jours suivants, complet et dans son emballage d'origine.
        Les seuls frais à sa charge sont, le cas échéant, les frais directs de renvoi du produit.
    </p>
    <p>
        La boutique rembourse toutes les sommes versées dans les {{ $boutique['remboursement_jours'] }} jours suivant l'information de la rétractation,
        par le même moyen de paiement sauf accord du client pour un autre moyen. Elle peut attendre d'avoir récupéré le produit avant de rembourser.
    </p>
    <p>
        Ce droit ne s'applique pas notamment aux biens confectionnés selon les spécifications du client ou nettement personnalisés (configuration sur mesure, gravure),
        ni aux biens susceptibles de se détériorer rapidement.
    </p>
    <p>Le détail pratique figure sur la page <a href="{{ route('legal.retours') }}">Retours et garanties</a>.</p>
    <p class="reference-loi">Loi n°025/2021, art. 57 à 60.</p>

    <h2 id="garanties">8. Garanties</h2>
    <h3>Garantie boutique</h3>
    <p>
        Tout produit vendu bénéficie d'une garantie boutique à compter de sa réception :
    </p>
    <ul>
        <li><strong>1 mois</strong> pour les ordinateurs (portables et fixes) ;</li>
        <li><strong>2 semaines</strong> pour tous les autres produits.</li>
    </ul>
    <p>
        Elle couvre les pannes et défauts de fonctionnement survenus dans des conditions normales d'utilisation. La boutique répare ou échange le produit ;
        si ni la réparation ni l'échange ne sont possibles, elle le rembourse. La durée applicable est rappelée sur chaque fiche produit.
    </p>
    <p>
        Sont exclus les dommages résultant d'une chute, d'un choc, d'un contact avec un liquide, d'une surtension électrique, d'une ouverture ou d'une réparation
        par une personne extérieure à la boutique, ainsi que l'usure normale des consommables.
    </p>
    <h3>Garantie légale</h3>
    <p>
        Indépendamment de la garantie boutique, le client bénéficie de la garantie légale contre les vices cachés prévue par le Code civil gabonais.
        Lorsque le fabricant accorde sa propre garantie, la boutique aide le client à la faire valoir.
    </p>

    <h2 id="responsabilite">9. Responsabilité</h2>
    <p>
        La boutique est responsable de la bonne exécution de la commande. Elle peut être exonérée de tout ou partie de sa responsabilité en prouvant que l'inexécution
        ou la mauvaise exécution est imputable au client ou à un cas de force majeure.
    </p>
    <p class="reference-loi">Loi n°025/2021, art. 33.</p>

    <h2 id="donnees">10. Données personnelles</h2>
    <p>
        Les informations communiquées lors de la commande servent à la traiter et à la livrer. Leur utilisation et les droits du client sont décrits dans la
        <a href="{{ route('legal.confidentialite') }}">politique de confidentialité</a>.
    </p>

    <h2 id="litiges">11. Réclamations et litiges</h2>
    <p>
        Pour toute réclamation, le client contacte la boutique : par WhatsApp ou téléphone au {{ $boutique['telephone'] }}, par e-mail à
        <a href="mailto:{{ $boutique['email'] }}">{{ $boutique['email'] }}</a>, ou directement en boutique. La boutique s'engage à rechercher une solution amiable.
    </p>
    <p>
        À défaut d'accord, le différend est d'abord soumis aux autorités administratives compétentes, notamment la Direction générale de la concurrence et de
        la consommation (DGCC). Le présent contrat est soumis au droit gabonais ; les litiges qui persistent relèvent des juridictions compétentes de Libreville.
    </p>
    <p class="reference-loi">Loi n°025/2021, art. 141 (saisine préalable des autorités administratives compétentes).</p>
@endsection
