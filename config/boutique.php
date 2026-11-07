<?php

/*
|--------------------------------------------------------------------------
| Identité et engagements commerciaux de SETUP-Gabon
|--------------------------------------------------------------------------
|
| Ces informations alimentent les pages légales (mentions légales, CGV,
| confidentialité, retours et garanties). Les identifiants officiels se
| renseignent dans le fichier .env dès qu'ils sont obtenus : tant qu'un
| champ obligatoire est vide, une alerte s'affiche dans l'espace gérante.
|
*/

return [

    'nom_commercial' => 'SETUP-Gabon',

    // Ex. : « Entreprise individuelle », « SARL au capital de 1 000 000 FCFA »
    'forme_juridique' => env('BOUTIQUE_FORME_JURIDIQUE'),
    'rccm' => env('BOUTIQUE_RCCM'),
    'nif' => env('BOUTIQUE_NIF'),
    'responsable_publication' => env('BOUTIQUE_RESPONSABLE_PUBLICATION'),

    'adresse' => 'Petit Paris, galerie Mouenga, Libreville, Gabon',
    'telephone' => '+241 66 13 12 70',
    'whatsapp' => '24166131270',
    'email' => env('BOUTIQUE_EMAIL', 'protocolees@gmail.com'),
    'horaires' => 'du mardi au dimanche, de 07h30 à 20h00',

    'hebergeur' => [
        'nom' => env('BOUTIQUE_HEBERGEUR_NOM'),
        'adresse' => env('BOUTIQUE_HEBERGEUR_ADRESSE'),
    ],

    // Engagements confirmés par la gérante le 7 novembre 2026
    'livraison' => [
        'zone' => 'Libreville',
        'gratuite' => true,
        'delai_heures' => 72,
    ],

    'retractation_jours' => 14,
    'remboursement_jours' => 14,

    'garantie' => [
        'ordinateurs_jours' => 30,
        'autres_jours' => 14,
    ],

    // Numéro de récépissé de déclaration auprès de l'APDPVP, une fois obtenu
    'apdpvp_recepisse' => env('BOUTIQUE_APDPVP_RECEPISSE'),

    // À incrémenter à chaque modification substantielle des CGV : la version acceptée est enregistrée sur chaque commande
    'version_conditions' => '2026-11-07',

];
