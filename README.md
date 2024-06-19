# mak2com-quotes

**mak2com-quotes** est un plugin WordPress pour la gestion des demandes de devis. Il permet aux utilisateurs de soumettre des demandes de devis via un formulaire sur le site public, et aux administrateurs de gérer ces demandes depuis le tableau de bord WordPress.

## Fonctionnalités

- Formulaire de demande de devis pour les utilisateurs
- Gestion des demandes de devis dans le tableau de bord
- Changement de statut des demandes de devis (en attente, en cours, terminé)
- Pagination des demandes de devis
- Notifications de succès et d'erreur

## Installation

1. Téléchargez le plugin depuis le dépôt GitHub ou le fichier ZIP.
2. Extrayez le contenu du fichier ZIP dans le répertoire `wp-content/plugins/`.
3. Activez le plugin via le menu "Plugins" dans WordPress.

## Utilisation

### Formulaire de demande de devis

Pour afficher le formulaire de demande de devis sur une page, utilisez le shortcode suivant :
[mak2com_quote_form]


### Gestion des demandes de devis

Les demandes de devis peuvent être gérées depuis le tableau de bord WordPress, sous le menu "Devis".

### Changement de statut des demandes

Les statuts des demandes peuvent être modifiés en cliquant sur le bouton de statut dans la liste des demandes. Les statuts disponibles sont :

- En attente
- En cours
- Terminé

## Développement

### Structure du projet

```
mak2com-quotes/
├── admin/
│   ├── partials/
│   │   └── admin-display.php
│   ├── css/
│   │   └── admin-style.css
│   └── js/
│       └── admin-script.js
├── public/
│   ├── partials/
│   │   ├── public-display.php
│   │   ├── quote-form.php
│   │   └── quote-popup.php
│   ├── css/
│   │   └── public-style.css
│   └── js/
│       ├── public-script.js
│       └── public-ajax.js
├── includes/
│   ├── admin/
│   │   ├── class-mak2com-quotes-admin.php
│   │   └── class-mak2com-quotes-list-table.php
│   ├── public/
│   │   └── class-mak2com-quotes-public.php
│   ├── class-mak2com-quotes.php
│   ├── class-mak2com-quotes-activator.php
│   └── class-mak2com-quotes-deactivator.php
├── assets/
│   ├── img/
│   └── css/
├── languages/
├── uninstall.php
└── mak2com-quotes.php
```