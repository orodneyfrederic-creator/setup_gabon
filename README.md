# SETUP-Gabon

Le site de la boutique SETUP-Gabon, à Libreville : ordinateurs, accessoires et matériel informatique.

Le site a deux faces :

- **la vitrine**, pour les clients : ils parcourent le catalogue, négocient un prix et commandent en ligne. On les livre à Libreville, ou ils passent à la boutique ;
- **l'espace gérante**, pour la boutique : produits, stock, commandes, caisse, devis, factures, service après-vente et statistiques, tout au même endroit.

La plupart des clients arrivent depuis leur téléphone, souvent par un lien partagé sur WhatsApp. Le site a été pensé pour le mobile d'abord.

---

## Ce que fait le site

### Côté client

- **Catalogue** : recherche, catégories, filtres (marque, fourchette de prix, en stock) et tri (nouveautés, prix, nom).
- **Fiche produit** : plusieurs photos avec balayage au doigt, vidéo, fiche technique, garantie, stock disponible.
- **Négociation** : le client propose un prix, la gérante accepte, refuse ou fait une contre-offre. Si l'offre est acceptée, le prix négocié s'applique à sa commande.
- **Commande** : panier, adresse de livraison, choix du paiement (espèces, Airtel Money ou Moov Money, réglé à la livraison ou en boutique) et acceptation des CGV. Le client reçoit un accusé de réception par e-mail et peut aussi envoyer sa commande sur WhatsApp.
- **Compte** : historique et suivi des commandes, modification du profil, mot de passe oublié.
- **Partage** : chaque produit a sa propre image d'aperçu, pour qu'un lien collé dans WhatsApp ou Facebook affiche la bonne photo, le nom et le prix.
- **Pages légales** : CGV, mentions légales, politique de confidentialité, retours et garanties.

Aucun paiement ne se fait sur le site pour l'instant. Le client choisit seulement son mode de paiement, et le règlement a lieu à la livraison ou en boutique.

### Côté gérante (`/gerante`)

| Écran | À quoi il sert |
|---|---|
| Tableau de bord | Ventes du jour, commandes en attente, négociations à traiter, alertes de stock, et rappel de ce qui manque pour être en règle |
| Produits, catégories, marques | Le catalogue : photos, vidéo, fiche technique, durée de garantie par catégorie |
| Stock | Entrées et sorties, avec l'historique de chaque mouvement |
| Commandes en ligne | Confirmer, livrer ou annuler une commande. Le client est prévenu par e-mail à chaque changement |
| Négociations | Répondre aux offres, et choisir par catégorie si le prix se négocie et jusqu'à quelle remise (50 % au plus) |
| Caisse (POS) | Ventes au comptoir, avec ticket |
| Devis et factures | Création, export en PDF, transformation d'un devis en facture |
| SAV | Tickets de réparation et de garantie |
| Clients | Fiches clients et historique d'achats |
| Analytique | Chiffre d'affaires, meilleures ventes, évolution dans le temps |

---

## Les règles de la boutique

Ces règles s'affichent sur le site (CGV, fiches produits). Elles sont toutes dans [config/boutique.php](config/boutique.php) : si l'une change, c'est là qu'il faut la modifier.

- **Livraison** : gratuite à Libreville, sous 72 heures au plus.
- **Garantie** : 1 mois pour les ordinateurs, 2 semaines pour le reste. La durée se règle par catégorie dans l'espace gérante.
- **Rétractation** : 14 jours pour les achats en ligne, et remboursement sous 14 jours (loi gabonaise n°025/2021 sur les transactions électroniques).
- **Conditions de vente** : chaque commande garde la date d'acceptation des CGV et leur version. Si tu modifies les CGV, change aussi `version_conditions` dans le fichier de config.

### Ce qui manque encore pour être en règle

Les pages légales sont prêtes, mais il manque des informations que seule la boutique peut fournir :

- l'immatriculation : RCCM, NIF et forme juridique ;
- la déclaration à l'APDPVP (protection des données personnelles) ;
- le nom et l'adresse de l'hébergeur du site.

Dès que tu les as, mets-les dans le `.env` (variables `BOUTIQUE_*`, voir plus bas). Tant qu'il en manque une, le tableau de bord de la gérante affiche une alerte. Fais aussi relire les pages légales par un juriste avant le lancement : elles ont été rédigées avec soin, mais ce n'est pas un avis d'avocat.

---

## Installer le projet en local

Il te faut PHP 8.3 avec l'extension GD (pour les images de partage), Composer, MySQL et Node.js. Sous Windows, WAMP fait très bien l'affaire : c'est l'environnement utilisé pour le développement.

```bash
git clone https://github.com/orodneyfrederic-creator/setup_gabon.git
cd setup_gabon
composer install
cp .env.example .env
php artisan key:generate
```

Crée une base MySQL (par exemple `setup_gabon`), renseigne-la dans le `.env`, puis :

```bash
php artisan migrate
php artisan db:seed --class=GeranteSeeder   # crée le compte de la gérante
php artisan storage:link                    # rend les photos produits accessibles
```

Avec WAMP, le site répond sur `http://localhost/SETUP_GABON_APP/public`. Sinon, lance `php artisan serve`.

### Se connecter

Tout le monde passe par la même page de connexion (`/login`). Le site reconnaît tout seul s'il s'agit d'un client ou de la gérante, et l'envoie au bon endroit.

- **Client** : il crée son compte sur `/register`.
- **Gérante** : le compte est créé par `GeranteSeeder`. **Change le mot de passe tout de suite** : celui écrit dans le seeder est visible par toute personne qui a accès au code.

---

## Le fichier `.env`

En plus des réglages Laravel habituels, voici ce qui compte pour ce projet :

```ini
APP_URL=https://ton-domaine.ga       # sert aux liens dans les e-mails et aux aperçus WhatsApp

MAIL_MAILER=smtp                     # "log" en local : les e-mails sont écrits dans storage/logs au lieu d'être envoyés
MAIL_HOST=...
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=...

# Identité légale de la boutique : alimente les mentions légales, les CGV et la page de confidentialité
BOUTIQUE_FORME_JURIDIQUE=
BOUTIQUE_RCCM=
BOUTIQUE_NIF=
BOUTIQUE_RESPONSABLE_PUBLICATION=
BOUTIQUE_EMAIL=protocolees@gmail.com # reçoit les alertes "nouvelle commande"
BOUTIQUE_HEBERGEUR_NOM=
BOUTIQUE_HEBERGEUR_ADRESSE=
BOUTIQUE_APDPVP_RECEPISSE=
```

L'adresse, le téléphone, le numéro WhatsApp et les horaires sont dans [config/boutique.php](config/boutique.php).

---

## Mettre en ligne

Sur le serveur, après chaque mise à jour du code :

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link        # la première fois seulement
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

À vérifier avant d'ouvrir le site au public :

- [ ] `APP_ENV=production` et `APP_DEBUG=false`. Sinon, au moindre bug, les visiteurs voient le code et les réglages.
- [ ] Le site passe en HTTPS.
- [ ] Un vrai serveur d'e-mails est configuré. Sans lui, ni les accusés de réception ni les liens « mot de passe oublié » ne partent.
- [ ] L'extension PHP GD est activée. Sans elle, les aperçus WhatsApp utilisent la photo d'origine, parfois trop lourde pour s'afficher.
- [ ] Le mot de passe de la gérante a été changé.
- [ ] `sitemap.xml` est déclaré dans Google Search Console.

> **Point connu :** le style du site (Tailwind) est chargé depuis un CDN, ce que Tailwind déconseille en production parce que les pages sont plus lentes, surtout en données mobiles. Il faudra passer à une compilation avec `npm run build` (Tailwind est déjà dans `package.json`).

---

## Les tests

```bash
php artisan test
```

Ils couvrent les parcours importants :
- commande ;
- négociation ;
- stock ;
- factures ;
- pages légales ;
- partage ;
- galerie photo ;
- e-mails ;
- mot de passe oublié ;
- filtres.

Ils tournent sur une base SQLite en mémoire : pas besoin de MySQL pour les lancer. Avant chaque envoi sur GitHub, lance-les et formate le code :

```bash
vendor/bin/pint
```

---

## Comment le code est organisé

Le projet est une application **Laravel 13** classique, en PHP 8.3, avec des vues **Blade**. Le style passe par **Tailwind CSS**, les petites interactions (galerie, menus, filtres) par **Alpine.js**, et les PDF de devis et factures par **DomPDF**.

- `app/Http/Controllers/` : la vitrine (catalogue, panier, commande, compte, négociation, pages légales).
- `app/Http/Controllers/Gerante/` : un contrôleur par écran de l'espace gérante.
- `app/Notifications/` : les e-mails (accusé de réception, nouvelle commande, changement de statut).
- `app/Services/ImagePartageService.php` : fabrique les images d'aperçu (1200×630, moins de 300 Ko) pour WhatsApp et Facebook.
- `resources/views/legal/` : les pages légales.
- `config/boutique.php` : les informations et les règles de la boutique.

Il y a deux types de comptes, rangés dans deux tables séparées :
- les **clients** sont dans `users` ;
- le **personnel** (la gérante) est dans `staff`, avec son propre système de connexion.

Toutes les routes `/gerante/*` sont réservées au rôle `gerante`.

Côté serveur, le site vérifie toujours :
- que les prix sont les bons ;
- que le stock est suffisant ;
- que l'offre négociée est valide.

Un client ne peut donc pas changer un prix en bricolant son panier dans le navigateur.

---

## Prochaines étapes

1. Paiement Airtel Money et Moov Money directement sur le site, via un agrégateur local.
2. Avis clients et suggestions de produits similaires.
3. Commande sans créer de compte.

Ensuite :
- un rôle vendeur ou caissier ;
- les SMS de suivi de commande (il faut un fournisseur) ;
- la compilation de Tailwind pour la production.

---

## Contact

SETUP-Gabon, Petit Paris, galerie Mouenga, Libreville
Ouvert du mardi au dimanche, de 7 h 30 à 20 h
WhatsApp : +241 66 13 12 70
E-mail : protocolees@gmail.com
