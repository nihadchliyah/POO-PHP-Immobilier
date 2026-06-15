<<<<<<< HEAD
# Nina Immo — TP POO PHP

Projet de travaux pratiques réalisé dans le cadre de la formation **IT-Akademy 2026**.  
Application de gestion immobilière complète en PHP orienté objet, avec architecture MVC, base de données SQLite et interface web responsive.

---

## Présentation

Nina Immo est une agence immobilière fictive qui permet de gérer un catalogue de biens, des propriétaires et des annonces de vente ou location. L'application est construite entièrement en PHP natif sans framework, en appliquant les concepts POO vus en cours.

---

## Fonctionnalités

### Catalogue de biens
- CRUD complet (appartements, maisons, locaux commerciaux)
- Champs spécifiques selon le type (étage/ascenseur, chambres/jardin/garage, activité)
- Statuts : disponible, loué, vendu
- Recherche en temps réel par ville, référence ou type
- Filtres par type (appartements / maisons / locaux)
- Vue carte interactive avec géolocalisation (Leaflet + Nominatim)

### Annonces
- Création d'annonces liées à un bien (vente ou location)
- Page de détail avec galerie photos, carte, infos contact, annonces similaires
- Filtres : type de transaction, statut actif/archivé
- Compteurs animés et cartes avec effet hover

### Propriétaires
- Deux types : particulier (nom, téléphone) et professionnel (raison sociale, SIRET)
- Liaison bien ↔ propriétaire avec règle 1 bien = 1 seul propriétaire
- Validation email unique

---

## Patterns de conception appliqués

| Pattern | Implémentation |
|---|---|
| **Singleton** | `DatabaseConnection` — instance unique PDO |
| **Factory** | `BienFactory`, `ProprietaireParticulierFactory`, `ProprietaireProfessionnelFactory` |
| **Observer** | `HistoriqueObserver`, `NotificationMailObserver` — changement de statut |
| **Strategy** | `SimulateurPret` avec BNP, Crédit Agricole, Société Générale, LCL |

---

## Principes SOLID

- **S** — chaque classe a une responsabilité unique (modèles, services, repositories séparés)
- **O** — `BienFactory` extensible sans modifier le code existant
- **L** — `Appartement`, `Maison`, `Local` substituables à `BienImmobilier`
- **I** — interfaces séparées : `Louable`, `Vendable`, `Estimable`, `Descriptible`
- **D** — `DataSourceInterface` permet de changer la source de données (JSON, DB…)

---

## Architecture

```
/
├── app/
│   ├── Controllers/       # BienController, ProprietaireController, AnnonceController
│   ├── Interfaces/        # Contrats (Louable, Vendable, Estimable, Observer…)
│   ├── Models/            # Entités métier (BienImmobilier, Appartement, Maison…)
│   ├── Repositories/      # Accès aux données
│   ├── Services/          # Factory, Singleton, Strategy, Observer
│   ├── Traits/            # LocationTrait, VenteTrait, EstimationTrait, StatutTrait
│   └── Views/             # Vues PHP (layout, biens, annonces, propriétaires)
├── data/
│   └── data.json          # Données initiales
├── database/
│   ├── schema.sql         # Schéma SQLite
│   └── setup.php          # Script d'initialisation
├── autoload.php           # Chargement des classes
└── index.php              # Point d'entrée + routeur
```

---

## Stack technique

- **PHP 8.1+** natif (sans framework)
- **SQLite** via PDO
- **Tailwind CSS** (CDN)
- **Leaflet.js** + OpenStreetMap pour la carte
- **Unsplash** pour les photos de biens
- **DM Sans** + **Playfair Display** (Google Fonts)

---

## Installation

```bash
# Cloner le dépôt
git clone https://github.com/nihadchliyah/POO-PHP-Immobilier.git
cd POO-PHP-Immobilier

# Créer la base de données et insérer les données de démo
php database/setup.php

# Lancer le serveur de développement
php -S localhost:8000
```

Ouvrir ensuite `http://localhost:8000` dans le navigateur.

> La base de données `database/agence.db` est générée automatiquement par `setup.php`.  
> Si elle existe déjà, la supprimer manuellement avant de relancer le script.

---

## Routeur

Le routeur est géré via les paramètres GET dans `index.php` :

```
?page=biens                        → catalogue
?page=biens&action=create          → nouveau bien
?page=biens&action=edit&id=1       → modifier un bien
?page=annonces                     → liste des annonces
?page=annonces&action=show&id=1    → détail annonce
?page=annonces&action=create       → nouvelle annonce
?page=proprietaires                → liste des propriétaires
```

---

## Modèle de données

```
biens
  id, reference, type, ville, prix, surface, statut, details (JSON), created_at

proprietaires
  id, nom, email, type, telephone, siret, raison_sociale, created_at

proprietaires_biens
  proprietaire_id, bien_id  (1 bien = 1 propriétaire max)

annonces
  id, bien_id, titre, description, type_transaction, prix_demande,
  contact_nom, contact_email, contact_telephone, statut, created_at
```

---

## Auteur

**Nihad Chliyah** — IT-Akademy 2026
=======
# POO-PHP-Immobilier
>>>>>>> 5dbae63870d326a2e99e0aa536634ca078edc459
