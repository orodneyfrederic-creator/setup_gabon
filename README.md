# SETUP-Gabon

Le site de la boutique **SETUP-Gabon**, à Libreville : ordinateurs, accessoires et matériel informatique.

Il a deux faces :

- **la vitrine**, pour les clients. On y parcourt le catalogue, on négocie un prix et on commande, avec ou sans compte. La boutique livre à Libreville, ou le client passe en boutique ;
- **l'espace gérante** (`/gerante`), pour la boutique. Produits, stock, commandes, caisse, devis, factures, service après-vente, avis, personnel et statistiques y sont réunis.

La plupart des clients arrivent depuis leur téléphone, souvent par un lien partagé sur WhatsApp. Le site a donc été pensé pour le mobile d'abord.

---

## Sommaire

1. [Qui fait quoi](#1-qui-fait-quoi)
2. [Parcours d'un visiteur sans compte](#2-parcours-dun-visiteur-sans-compte)
3. [Parcours d'un client connecté](#3-parcours-dun-client-connecté)
4. [La vie d'une commande](#4-la-vie-dune-commande)
5. [Parcours de la gérante](#5-parcours-de-la-gérante)
6. [Parcours d'un vendeur](#6-parcours-dun-vendeur)
7. [Les e-mails envoyés](#7-les-e-mails-envoyés)
8. [Les règles de la boutique](#8-les-règles-de-la-boutique)
9. [Sécurité : ce que le site protège](#9-sécurité--ce-que-le-site-protège)
10. [Conformité légale](#10-conformité-légale)
11. [Installer le projet en local](#11-installer-le-projet-en-local)
12. [Le fichier `.env`](#12-le-fichier-env)
13. [Mettre en ligne](#13-mettre-en-ligne)
14. [Les tests](#14-les-tests)
15. [Comment le code est organisé](#15-comment-le-code-est-organisé)
16. [Limites connues et prochaines étapes](#16-limites-connues-et-prochaines-étapes)

---

## 1. Qui fait quoi

| | Visiteur sans compte | Client connecté | Vendeur | Gérante |
|---|:-:|:-:|:-:|:-:|
| Parcourir le catalogue, voir les fiches produits | ✔ | ✔ | ✔ | ✔ |
| Mettre des produits au panier et commander | ✔ | ✔ | | |
| Suivre sa commande | ✔ par lien personnel | ✔ dans « Mes commandes » | | |
| Négocier un prix | | ✔ | | |
| Laisser un avis (après livraison) | | ✔ | | |
| Caisse, commandes en ligne, stock, SAV | | | ✔ | ✔ |
| Produits, prix, négociations, devis, factures, clients, avis, statistiques, personnel | | | | ✔ |

Tout le monde se connecte sur la même page (`/login`). Le site reconnaît tout seul s'il s'agit d'un client, de la gérante ou d'un vendeur, et envoie chacun au bon endroit :
- un client reste sur la vitrine ;
- la gérante arrive sur son tableau de bord ;
- un vendeur arrive sur la caisse.

Clients et personnel sont rangés dans deux tables séparées. Un client ne peut donc jamais entrer dans l'espace gérante, même en tapant l'adresse à la main.

---

## 2. Parcours d'un visiteur sans compte

### 2.1 Trouver un produit

1. Le visiteur arrive sur l'accueil, souvent depuis un lien WhatsApp ou Facebook. Chaque produit a sa propre image d'aperçu (photo, nom et prix), qui s'affiche dans la conversation avant même le clic.
2. Pour chercher, il dispose de :
   - **la recherche** par nom ;
   - **les catégories** (page `/categories`) ;
   - **les filtres** : marque, fourchette de prix, « en stock uniquement » ;
   - **le tri** : nouveautés, prix croissant ou décroissant, nom.
3. Les filtres restent dans l'adresse de la page : il peut la partager ou revenir en arrière sans les perdre.

### 2.2 La fiche produit

Elle montre :
- **les photos**, à faire défiler du doigt sur mobile, et la vidéo si la gérante en a ajouté une ;
- **le prix**, le stock disponible et la durée de garantie (1 mois pour les ordinateurs, 2 semaines pour le reste, ou la durée fixée pour la catégorie) ;
- **la fiche technique** (processeur, mémoire, écran…) et la description ;
- **les avis clients** : note moyenne et commentaires marqués « Achat vérifié » ;
- **« Dans la même catégorie »** : 4 produits proches, ceux en stock d'abord, puis par prix le plus proche ;
- un bouton pour **partager** le produit.

La négociation est réservée aux clients qui ont un compte : le visiteur est invité à se connecter.

### 2.3 Le panier

- **Ajouter au panier** place le produit dans le panier, enregistré dans le navigateur du téléphone. Le panier survit à la fermeture de la page.
- Il peut changer les quantités ou retirer un produit à tout moment.
- Le prix affiché dans le panier n'est qu'indicatif : au moment de la commande, le serveur recalcule tout à partir du catalogue (voir [Sécurité](#9-sécurité--ce-que-le-site-protège)).

### 2.4 Commander sans compte

1. Il clique sur **Finaliser ma commande**. La page lui propose « Déjà client ? Connectez-vous », mais il peut continuer sans compte.
2. Il vérifie le récapitulatif : produits, quantités, prix et total. La livraison est gratuite à Libreville.
3. Il remplit :
   - **nom et prénom** (obligatoire) ;
   - **téléphone** (obligatoire) : la boutique l'appelle pour fixer la livraison ;
   - **e-mail** (facultatif) : pour recevoir le récapitulatif et le lien de suivi ;
   - **adresse ou lieu de livraison** à Libreville (obligatoire) ;
   - **une note** pour la boutique (facultatif).
4. Il choisit **son mode de paiement** : espèces, Airtel Money ou Moov Money. Rien n'est payé sur le site : il règle à la livraison ou en boutique.
5. Il coche l'acceptation des **conditions générales de vente** (lien vers les CGV), puis clique sur **Confirmer la commande**.

### 2.5 Juste après la commande

- **La page de confirmation** s'affiche avec le numéro de commande et le récapitulatif complet. Elle tient lieu d'accusé de réception, comme l'exige la loi.
- **Le bouton « Envoyer ma commande sur WhatsApp »** ouvre WhatsApp avec un message déjà rédigé : numéro de commande, téléphone, adresse, mode de paiement, articles, total et lien de suivi. Le client n'a plus qu'à l'envoyer.
- **Le lien de suivi personnel** : c'est l'adresse de la page de confirmation (`/suivi-commande/…`). Elle est secrète, longue de 40 caractères et invisible pour Google. Le client est invité à la garder dans ses favoris : c'est sa seule clé d'accès à la commande.
- **L'e-mail d'accusé de réception**, s'il a donné son adresse, reprend le récapitulatif et le lien de suivi.
- **La boutique reçoit une alerte** par e-mail (adresse `BOUTIQUE_EMAIL`).

### 2.6 Suivre sa commande

En rouvrant son lien de suivi, il voit l'avancement : **Reçue → Confirmée → Livrée**, ou « Annulée ». S'il a donné un e-mail, il est aussi prévenu à chaque étape (voir [E-mails](#7-les-e-mails-envoyés)).

### 2.7 Ce qu'un visiteur sans compte ne peut pas faire

- négocier un prix ;
- laisser un avis ;
- retrouver ses commandes s'il perd son lien de suivi. Il faut alors passer par la boutique sur WhatsApp, avec son numéro de commande ;
- annuler lui-même une commande : il prévient la boutique sur WhatsApp.

---

## 3. Parcours d'un client connecté

### 3.1 Créer son compte et se connecter

1. **Inscription** (`/register`) : prénom, nom, e-mail, téléphone (facultatif) et mot de passe.
2. **Connexion** (`/login`) : e-mail et mot de passe. Après 5 essais ratés, la connexion est bloquée une minute, pour empêcher de deviner un mot de passe.
3. **Mot de passe oublié** :
   1. Sur la page de connexion, il clique sur « Mot de passe oublié ? » et saisit son e-mail.
   2. Il reçoit un lien valable 60 minutes. Le message affiché est le même que l'adresse existe ou non, pour ne pas révéler qui a un compte.
   3. Le lien ouvre une page où il choisit un nouveau mot de passe (8 caractères au moins), puis il se connecte.

### 3.2 Négocier un prix

Seuls certains produits se négocient : la gérante choisit lesquels (par catégorie ou produit par produit) et fixe la remise maximale.

1. Sur la fiche produit, il clique sur **Démarrer la négociation**.
2. Il saisit **son prix** en FCFA. Le système répond tout de suite :

   | Son offre | Réponse du système |
   |---|---|
   | Égale ou supérieure au prix catalogue | Acceptée au prix catalogue |
   | Entre le prix plancher et le prix catalogue | **Acceptée** à son prix |
   | Sous le prix plancher | **Contre-offre** : le système propose un prix intermédiaire, qui descend à chaque tour |
   | Au dernier tour | **Dernière offre ferme** au prix plancher : à accepter ou à abandonner |

   - **Le prix plancher** = prix catalogue moins la remise maximale. Par défaut, la remise est de 2 %, et la gérante peut monter jusqu'à 50 %. Le plancher ne descend jamais sous le prix d'achat.
   - **Le nombre de tours** est de 3 par défaut, réglable de 1 à 10 par produit.
3. Face à une contre-offre, il peut **accepter cette offre**, **proposer un autre prix** ou abandonner.
4. **Mode « décision manuelle »** : si la gérante l'a activé sur le produit, l'offre ne reçoit pas de réponse automatique. Elle part chez la gérante, qui accepte, refuse ou fait sa propre contre-offre depuis son espace.
5. **Une offre acceptée est valable 24 heures.** Un compte à rebours s'affiche. Il clique sur **Ajouter au panier à ce prix** : le prix négocié s'appliquera à sa commande, une seule fois et pour la quantité négociée.
6. Après un refus ou un abandon, un délai de 24 heures s'applique avant de pouvoir renégocier le même produit.
7. Si le prix catalogue change pendant la négociation, celle-ci expire et il faut recommencer.

### 3.3 Commander

C'est le même parcours que sans compte ([2.4](#24-commander-sans-compte)), avec trois différences :
- son nom et son e-mail sont déjà connus. Le téléphone n'est demandé que s'il ne l'a pas encore donné, et il est alors ajouté à son profil ;
- les offres négociées s'appliquent ;
- après la commande, il arrive sur le détail de sa commande dans **Mes commandes**, et non sur un lien secret.

### 3.4 Mes commandes

- **`/commandes`** liste toutes ses commandes, avec leur statut.
- **Le détail** d'une commande montre l'avancement (Reçue → Confirmée → Livrée), les articles, le prix payé (négocié ou non), l'adresse, le mode de paiement et la date d'acceptation des CGV.
- Il reçoit un e-mail à chaque étape.

### 3.5 Laisser un avis

1. Une fois une commande **livrée**, un bouton **Donner mon avis** apparaît à côté de chaque produit de la commande.
2. Sur la fiche produit, il donne une note de 1 à 5 étoiles et, s'il veut, un commentaire (1 000 caractères au plus), puis clique sur **Publier mon avis**.
3. L'avis est publié tout de suite, avec la mention « Achat vérifié ». Seuls son prénom et l'initiale de son nom sont affichés (« Paul O. »).
4. Il peut modifier son avis plus tard. Il n'en a qu'un par produit.
5. Si la gérante masque son avis (propos injurieux), il ne peut plus le republier.

### 3.6 Mon compte

Sur `/mon-compte`, il modifie son prénom, son nom, son e-mail et son téléphone. Pour changer de mot de passe, il doit saisir l'ancien.

---

## 4. La vie d'une commande

| Statut | Ce que ça veut dire | Ce que le site fait automatiquement |
|---|---|---|
| **En attente** (« Reçue ») | La commande vient d'être passée | Accusé de réception au client, alerte à la boutique. Le stock **ne bouge pas encore** |
| **Confirmée** | La boutique a vérifié la disponibilité et fixé la livraison | Le stock **diminue**, chaque sortie est notée dans l'historique. E-mail au client |
| **Livrée** | Le client a reçu sa commande | E-mail au client. Le délai de rétractation de 14 jours et la garantie commencent. Le client peut laisser un avis |
| **Annulée** | La commande est abandonnée | Si le stock avait déjà diminué, il est **remis**. L'offre négociée est libérée. E-mail au client |

Passages autorisés :
- En attente → Confirmée, Livrée ou Annulée ;
- Confirmée → Livrée ou Annulée ;
- Livrée → Annulée (retour produit) ;
- Annulée → rien : c'est définitif.

Une commande n'est **jamais supprimée**, même si le client supprime son compte : la loi (droit comptable OHADA) impose de garder les pièces de vente 10 ans. Le nom et le téléphone du client sont donc recopiés sur la commande au moment de l'achat.

---

## 5. Parcours de la gérante

### 5.1 Première connexion

1. Le premier compte gérante est créé par `php artisan db:seed --class=GeranteSeeder`.
2. Elle se connecte sur `/login` avec cet e-mail et ce mot de passe.
3. **Elle change tout de suite son mot de passe et indique son nom** dans « Mon compte et mot de passe » (menu en haut à droite). Le mot de passe du seeder est écrit dans le code, donc connu de toute personne qui y a accès.

### 5.2 Tableau de bord (`/gerante/dashboard`)

Elle y trouve d'un coup d'œil :
- le chiffre d'affaires des 30 derniers jours ;
- le nombre de clients, de commandes et de produits ;
- **les commandes en attente** et **les négociations à traiter** ;
- **les alertes de stock** : produits arrivés à leur seuil d'alerte ;
- les 5 dernières commandes ;
- **un encadré « Conformité »** qui liste les informations légales encore manquantes (RCCM, NIF, hébergeur, APDPVP…). Il disparaît quand tout est renseigné.

### 5.3 Traiter une commande en ligne (`/gerante/commandes`)

1. La liste montre chaque commande : numéro, client (avec la mention « sans compte » s'il y a lieu), téléphone, articles, total et statut. On peut filtrer par statut et chercher.
2. En ouvrant une commande, elle voit les coordonnées du client (nom, téléphone, e-mail, adresse), le mode de paiement, chaque article avec son prix catalogue et, le cas échéant, son prix négocié.
3. Un bouton ouvre WhatsApp directement sur le numéro du client.
4. Elle **change le statut** (voir [La vie d'une commande](#4-la-vie-dune-commande)) :
   - **Confirmer** sort le produit du stock. Si le stock est insuffisant, le site refuse et indique le produit concerné ;
   - **Livrer** ;
   - **Annuler**, avec un motif facultatif : le stock et l'offre négociée sont libérés.

   Le client est prévenu par e-mail à chaque changement.

### 5.4 Négociations (`/gerante/negociations`)

1. **La liste** montre toutes les négociations, avec en tête celles « en attente de la gérante » (produits en décision manuelle).
2. **Le détail** retrace l'historique complet : chaque offre du client, chaque réponse et leur date.
3. **Répondre** : elle peut **accepter**, **refuser** (avec un message) ou faire **une contre-offre** d'un montant de son choix. Le montant ne peut pas descendre sous le prix plancher.
4. **Règles** (`/gerante/negociations/regles`) : pour chaque catégorie, elle choisit si le prix se négocie et la remise maximale (de 0 à 50 %).
5. Produit par produit (dans la fiche produit), elle peut aussi :
   - forcer « négociable » ou « non négociable », quelle que soit la catégorie ;
   - fixer une remise maximale propre au produit ;
   - fixer le nombre de tours (de 1 à 10) ;
   - activer la **décision manuelle**.

### 5.5 Catalogue : produits, catégories, marques

- **Produits** (`/gerante/produits`) :
  - désignation, référence, description, prix de vente, prix d'achat (pour calculer la marge et protéger le prix plancher) ;
  - quantité en stock et seuil d'alerte ;
  - catégorie et marque ;
  - photo principale, photos supplémentaires (dans l'ordre choisi) et vidéo ;
  - **fiche technique** sous forme de lignes « libellé : valeur » ;
  - réglages de négociation.
- **Catégories** : nom et **durée de garantie** propre à la catégorie (sinon, 14 jours par défaut).
- **Marques** : utilisées dans les filtres de la vitrine.

Une modification est visible tout de suite sur la vitrine.

### 5.6 Stock (`/gerante/stock`)

- La liste montre la quantité de chaque produit, et ceux sous leur seuil d'alerte ressortent.
- **Ajouter un mouvement** : entrée (réception de marchandise) ou sortie (casse, perte, usage interne), avec une quantité et un motif obligatoire.
- **L'historique** d'un produit liste chaque mouvement avec sa date et son motif. On y retrouve aussi les mouvements automatiques : vente en ligne, vente en caisse, facture, annulation.

### 5.7 Caisse (`/gerante/pos`)

Pour les ventes au comptoir :
1. Elle ajoute les produits vendus au panier de caisse.
2. Elle choisit le moyen de paiement : espèces, carte ou mobile money.
3. Elle valide. Le stock diminue tout de suite, et la vente reçoit un numéro (`POS-…`).
4. **Le ticket de caisse** s'affiche, prêt à imprimer.

### 5.8 Devis et factures

1. **Créer un devis** (`/gerante/devis/create`) :
   - le client : nom, téléphone, et en option e-mail et entreprise ;
   - les produits et leurs quantités ;
   - des notes.

   Le devis est créé « brouillon ».
2. **Suivre le devis** : brouillon → envoyé → accepté ou refusé. Chaque devis s'exporte **en PDF**.
3. **Transformer en facture** : seul un devis **accepté** peut devenir une facture (bouton « Facturer »).
   - Le site vérifie d'abord le stock : s'il manque un produit, il refuse et dit lequel.
   - La facture reçoit un numéro (`FAC-AAAAMMJJ-XXXX`), le statut « émise », et les produits sortent du stock.
4. **Suivre la facture** :
   - **payée** : elle compte dans le chiffre d'affaires ;
   - **annulée** : les produits reviennent en stock.

   Chaque facture s'exporte **en PDF**.

### 5.9 Service après-vente (`/gerante/sav`)

1. **Nouveau ticket** : nom et téléphone du client, produit concerné, description de la panne, date de retour prévue et notes. Le ticket reçoit une référence.
2. Le ticket avance : **ouvert → en cours → résolu → clos**.
3. On peut chercher un ticket par client, référence ou produit.

### 5.10 Clients (`/gerante/clients`)

La liste des clients qui ont un compte, avec pour chacun ses coordonnées et l'historique de ses achats. Les clients sans compte n'y figurent pas : on les retrouve dans les commandes.

### 5.11 Avis clients (`/gerante/avis`)

- Elle voit la note moyenne, le nombre d'avis publiés et masqués, et chaque avis. Elle peut filtrer par statut.
- **Masquer un avis** (propos injurieux, hors sujet, données personnelles…) : un motif est obligatoire. L'avis disparaît de la vitrine.
- **Republier** un avis masqué par erreur.
- Elle **ne peut pas modifier** le texte d'un avis : ce serait tromper les autres clients.

### 5.12 Statistiques (`/gerante/analytics`)

Sur 7, 30 ou 90 jours :
- **le chiffre d'affaires**, réparti entre la caisse, les commandes en ligne (confirmées ou livrées) et les factures payées ;
- son évolution dans le temps ;
- les 5 produits les plus vendus, les ventes par catégorie et la répartition par mode de paiement. Attention, ces trois graphiques ne comptent pour l'instant que les ventes en caisse.

Sans vente sur la période, les graphiques l'indiquent clairement au lieu d'afficher des chiffres inventés.

### 5.13 Personnel (`/gerante/personnel`)

- **Créer un compte vendeur** : nom, e-mail (identifiant de connexion) et mot de passe (8 caractères au moins, avec des lettres et des chiffres).
- **Désactiver un compte**, par exemple au départ d'un employé. L'accès est coupé immédiatement, même si la personne était connectée. On peut le réactiver.
- **Réinitialiser le mot de passe** d'un membre.
- Deux garde-fous :
  - on ne peut pas désactiver la dernière gérante active ;
  - une gérante ne peut pas retirer son propre accès.

### 5.14 Mon compte (`/gerante/mon-compte`)

Chaque membre du personnel modifie son nom et son mot de passe. L'ancien mot de passe est demandé pour en choisir un nouveau.

---

## 6. Parcours d'un vendeur

1. La gérante crée son compte dans « Personnel ».
2. Il se connecte sur `/login` et arrive directement sur **la caisse**. Le menu affiche « Espace vendeur ».
3. Il a accès à :
   - **la caisse** : ventes au comptoir et tickets ;
   - **les commandes en ligne** : consulter, confirmer, livrer, annuler ;
   - **le stock** : mouvements et historique ;
   - **le SAV** : tickets de réparation et de garantie ;
   - **son compte** : nom et mot de passe.
4. Il n'a **pas** accès au tableau de bord, aux produits et aux prix, aux négociations, aux devis et factures, aux clients, aux avis, aux statistiques ni au personnel. S'il tape l'adresse d'un de ces écrans, il reçoit un refus.

---

## 7. Les e-mails envoyés

Ils partent du compte Gmail configuré dans le `.env` (voir [12](#12-le-fichier-env)).

| E-mail | Quand | À qui |
|---|---|---|
| Accusé de réception, avec le récapitulatif complet et le lien de suivi | Dès que la commande est passée | Le client (sans compte : seulement s'il a donné un e-mail) |
| Nouvelle commande | Dès que la commande est passée | La boutique (`BOUTIQUE_EMAIL`) |
| Commande confirmée | La gérante confirme | Le client |
| Commande livrée | La gérante marque la commande livrée | Le client |
| Commande annulée | La gérante annule | Le client |
| Lien pour choisir un nouveau mot de passe | Le client clique sur « Mot de passe oublié » | Le client |

Un e-mail qui ne part pas (Gmail injoignable, par exemple) **ne bloque jamais la commande** : l'erreur est notée dans `storage/logs` et la commande reste enregistrée.

Gmail limite l'envoi à environ **500 e-mails par jour**. Au-delà, il faudra passer à un service dédié (Brevo, Mailgun…) en changeant seulement les lignes `MAIL_*` du `.env`.

---

## 8. Les règles de la boutique

Elles s'affichent sur le site (CGV, fiches produits) et sont toutes dans [config/boutique.php](config/boutique.php) : si l'une change, c'est là qu'il faut la modifier.

- **Livraison** : gratuite à Libreville, sous 72 heures au plus après confirmation. Retrait possible en boutique.
- **Paiement** : à la livraison ou en boutique, en espèces, par Airtel Money ou par Moov Money. Rien n'est payé sur le site.
- **Garantie** : 1 mois pour les ordinateurs, 2 semaines pour le reste. La durée se règle par catégorie.
- **Rétractation** : 14 jours après réception pour changer d'avis, et remboursement sous 14 jours (loi gabonaise n°025/2021 sur les transactions électroniques).
- **Conditions de vente** : chaque commande garde la date d'acceptation des CGV et leur version. **Si tu modifies les CGV, change aussi `version_conditions`** dans `config/boutique.php`.
- **Coordonnées** : adresse, téléphone, numéro WhatsApp et horaires sont aussi dans ce fichier.

---

## 9. Sécurité : ce que le site protège

- **Les prix** : le serveur recalcule toujours le total à partir du catalogue. Un client qui modifie son panier dans le navigateur ne peut pas changer un prix.
- **Le stock** : une commande ne peut pas être confirmée s'il n'y a pas assez de produits.
- **Les offres négociées** : elles sont vérifiées et consommées une seule fois, même si deux commandes arrivent en même temps.
- **Les mots de passe** : enregistrés chiffrés, illisibles même pour la boutique.
- **Les connexions** : blocage d'une minute après 5 essais ratés.
- **Les formulaires** : le nombre d'envois est limité (commande, inscription, mot de passe oublié, avis), pour freiner les robots.
- **Les liens de suivi** : secrets, impossibles à deviner, et exclus de Google (`robots.txt` et balise `noindex`).
- **Les commentaires** : tout texte saisi par un client s'affiche comme du texte, jamais comme du code.
- **Les rôles** : chaque écran de l'espace gérante vérifie le rôle de la personne connectée. Un compte désactivé est déconnecté à sa prochaine action.
- **Les erreurs** : un client ne voit jamais le détail technique d'une erreur ; il reste dans les journaux du serveur.

---

## 10. Conformité légale

Pages déjà en place : [CGV](resources/views/legal/cgv.blade.php), mentions légales, politique de confidentialité, retours et garanties. Elles sont liées en bas de chaque page.

**Ce qui manque encore**, et que seule la boutique peut fournir :

| Information | Variable du `.env` |
|---|---|
| Forme juridique (ex. : « Entreprise individuelle ») | `BOUTIQUE_FORME_JURIDIQUE` |
| Numéro RCCM | `BOUTIQUE_RCCM` |
| Numéro NIF | `BOUTIQUE_NIF` |
| Responsable de la publication | `BOUTIQUE_RESPONSABLE_PUBLICATION` |
| Nom et adresse de l'hébergeur | `BOUTIQUE_HEBERGEUR_NOM`, `BOUTIQUE_HEBERGEUR_ADRESSE` |
| Récépissé de déclaration à l'APDPVP (protection des données) | `BOUTIQUE_APDPVP_RECEPISSE` |

Dès qu'une information est obtenue :
1. Remplis la ligne correspondante dans le `.env` (en local et sur le serveur).
2. Lance `php artisan config:clear`.

Elle apparaît alors sur les pages légales, et l'alerte du tableau de bord disparaît.

Fais aussi relire les pages légales par un juriste avant le lancement : elles ont été rédigées avec soin, mais ce n'est pas un avis d'avocat.

---

## 11. Installer le projet en local

Il te faut :
- PHP 8.3 avec l'extension GD (pour les images de partage) ;
- Composer ;
- MySQL ;
- Node.js.

Sous Windows, WAMP fait très bien l'affaire : c'est l'environnement utilisé pour le développement.

```bash
git clone https://github.com/orodneyfrederic-creator/setup_gabon.git
cd setup_gabon
composer install
cp .env.example .env
php artisan key:generate
```

Crée une base MySQL (par exemple `setup_gabon`) et renseigne-la dans le `.env`, puis :

```bash
php artisan migrate
php artisan db:seed --class=GeranteSeeder   # crée le compte de la gérante
php artisan storage:link                    # rend les photos produits accessibles
```

Avec WAMP, le site répond sur `http://localhost/SETUP_GABON_APP/public`. Sinon, lance `php artisan serve`.

---

## 12. Le fichier `.env`

Le `.env` contient les mots de passe et les réglages propres à chaque machine. **Il n'est jamais envoyé sur GitHub** (il est dans `.gitignore`) : il faut le remplir à la main sur chaque ordinateur et sur le serveur.

En plus des réglages Laravel habituels, voici ce qui compte pour ce projet :

```ini
APP_URL=https://ton-domaine.ga       # sert aux liens dans les e-mails et aux aperçus WhatsApp

# E-mails envoyés par Gmail
MAIL_MAILER=smtp                     # "log" pour écrire les e-mails dans storage/logs au lieu de les envoyer
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=adresse@gmail.com
MAIL_PASSWORD=xxxxxxxxxxxxxxxx       # mot de passe d'application : les 16 lettres, sans espaces
MAIL_FROM_ADDRESS=adresse@gmail.com  # Gmail impose la même adresse que MAIL_USERNAME
MAIL_FROM_NAME="SETUP-Gabon"

# Identité légale de la boutique (voir la section 10)
BOUTIQUE_FORME_JURIDIQUE=
BOUTIQUE_RCCM=
BOUTIQUE_NIF=
BOUTIQUE_RESPONSABLE_PUBLICATION=
BOUTIQUE_EMAIL=protocolees@gmail.com # reçoit les alertes "nouvelle commande", affichée comme contact
BOUTIQUE_HEBERGEUR_NOM=
BOUTIQUE_HEBERGEUR_ADRESSE=
BOUTIQUE_APDPVP_RECEPISSE=
```

**Obtenir un mot de passe d'application Gmail** :
1. Ouvre ton compte Google, puis « Sécurité ».
2. Active la validation en deux étapes si ce n'est pas déjà fait.
3. Va dans « Mots de passe des applications » et crée-en un nommé « SETUP-Gabon ».
4. Copie les 16 lettres dans `MAIL_PASSWORD`, sans les espaces.

Ce n'est pas le mot de passe de ton compte Gmail : tu peux le révoquer à tout moment sans toucher à ton compte.

Après toute modification du `.env`, lance `php artisan config:clear`.

---

## 13. Mettre en ligne

### La première fois

1. Choisis un hébergeur qui propose PHP 8.3, MySQL et un accès SSH, et note son nom et son adresse pour `BOUTIQUE_HEBERGEUR_*`.
2. Récupère le code sur le serveur (`git clone`), puis crée le `.env` à partir de `.env.example`.
3. **Recopie dans le `.env` du serveur** les lignes `MAIL_*` de ton `.env` local, ainsi que les `BOUTIQUE_*`.
4. Règle `APP_URL` sur le vrai domaine, `APP_ENV=production` et `APP_DEBUG=false`.
5. Lance les commandes ci-dessous, puis `php artisan db:seed --class=GeranteSeeder` pour créer la gérante.

### Après chaque mise à jour du code

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link        # la première fois seulement
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### À vérifier avant d'ouvrir le site au public

- [ ] `APP_ENV=production` et `APP_DEBUG=false`. Sinon, au moindre bug, les visiteurs voient le code et les réglages.
- [ ] Le site passe en HTTPS.
- [ ] Les lignes `MAIL_*` sont dans le `.env` du serveur. Pour tester : passe une commande avec ton propre e-mail.
- [ ] Les `BOUTIQUE_*` sont remplis, et le tableau de bord n'affiche plus d'alerte de conformité.
- [ ] L'extension PHP GD est activée. Sans elle, les aperçus WhatsApp utilisent la photo d'origine, parfois trop lourde pour s'afficher.
- [ ] Le mot de passe de la gérante a été changé.
- [ ] `sitemap.xml` est déclaré dans Google Search Console.

### Le style du site (Tailwind)

Le style est compilé dans `public/build/` à partir de `resources/css/app.css`. Ce dossier est **versionné dans git**, pour que le serveur n'ait pas besoin de Node.js.

**Après toute modification d'une vue ou du fichier CSS**, relance la compilation avant d'envoyer sur GitHub, sinon les nouvelles classes n'auront aucun style :

```bash
npm install      # la première fois
npm run build
```

Pendant le développement, `npm run dev` recompile automatiquement à chaque modification.

---

## 14. Les tests

```bash
php artisan test
```

Ils couvrent les parcours importants :
- la commande, avec ou sans compte ;
- la négociation ;
- le stock ;
- les factures ;
- les avis clients et les produits similaires ;
- les droits de la gérante et des vendeurs, et la gestion du personnel ;
- les e-mails et le mot de passe oublié ;
- la limitation des tentatives de connexion ;
- les pages légales ;
- le partage ;
- la galerie photo ;
- les filtres.

Ils tournent sur une base SQLite en mémoire : pas besoin de MySQL. Aucun vrai e-mail n'est envoyé pendant les tests.

Avant chaque envoi sur GitHub, lance les tests, puis formate le code :

```bash
vendor/bin/pint
```

---

## 15. Comment le code est organisé

Le projet est une application **Laravel 13** classique, en PHP 8.3, avec :
- des vues **Blade** ;
- **Tailwind CSS** pour le style ;
- **Alpine.js** pour les petites interactions (galerie, menus, filtres, négociation) ;
- **DomPDF** pour les PDF de devis et de factures.

| Dossier ou fichier | Contenu |
|---|---|
| `app/Http/Controllers/` | La vitrine : catalogue, panier, commande, suivi, compte, négociation, avis, pages légales |
| `app/Http/Controllers/Gerante/` | Un contrôleur par écran de l'espace gérante |
| `app/Services/NegociationService.php` | Toutes les règles de négociation (prix plancher, contre-offres, validité) |
| `app/Services/ChiffreAffairesService.php` | Le calcul du chiffre d'affaires, le même pour le tableau de bord et les statistiques |
| `app/Services/ImagePartageService.php` | Les images d'aperçu (1200×630, moins de 300 Ko) pour WhatsApp et Facebook |
| `app/Notifications/` | Les e-mails de commande |
| `app/Http/Middleware/CheckRole.php` | Le contrôle des rôles (gérante, vendeur) sur l'espace gérante |
| `resources/views/legal/` | Les pages légales |
| `config/boutique.php` | Les informations et les règles de la boutique |
| `routes/web.php` | Toutes les adresses du site |

Deux types de comptes, deux tables :
- les **clients** sont dans `users` ;
- le **personnel** (gérante et vendeurs) est dans `staff`, avec son propre système de connexion.

Les routes `/gerante/*` sont réservées au personnel :
- le middleware `role:gerante,vendeur` ouvre la caisse, les commandes, le stock et le SAV aux deux rôles ;
- `role:gerante` réserve tout le reste à la gérante.

---

## 16. Limites connues et prochaines étapes

**Mis de côté, en attente d'informations :**
- **Paiement en ligne Airtel Money / Moov Money** : il faut un contrat avec un agrégateur (SingPay, eBilling ou PVit), ses clés d'accès et sa documentation.
- **SMS de suivi de commande** : il faut un fournisseur de SMS.

**Limites actuelles :**
- Les clients sans compte n'apparaissent pas dans l'écran « Clients » et ne peuvent pas laisser d'avis.
- Un client ne peut ni annuler sa commande ni supprimer son compte lui-même : il passe par la boutique.
- À la caisse, le prix de vente vient de l'écran de caisse et n'est pas revérifié par le serveur.
- Dans les statistiques, les meilleures ventes, les catégories et les modes de paiement ne comptent que la caisse, pas les commandes en ligne ni les factures.
- Les e-mails partent pendant la commande, ce qui ajoute une à deux secondes d'attente.

---

## Contact

SETUP-Gabon, Petit Paris, galerie Mouenga, Libreville
Ouvert du mardi au dimanche, de 7 h 30 à 20 h
WhatsApp : +241 66 13 12 70
E-mail : protocolees@gmail.com
