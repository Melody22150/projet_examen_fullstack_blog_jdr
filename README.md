# 📚 Les Chroniques du JDR - Blog Communautaire

Blog dédié au jeu de rôle sur table, développé avec Docker pour une architecture complète et sécurisée.

## 🎯 Objectif du projet

Créer une plateforme web permettant de partager articles, conseils et actualités sur le jeu de rôle, avec un système CRUD complet, gestion d'images et tests automatisés.

---

## 🛠️ Technologies utilisées

### Infrastructure
- **Docker** - Conteneurisation (Apache + MySQL)
- **Docker Compose** - Orchestration des services

### Front-end
- **HTML5** - Structure sémantique
- **CSS3** - Design responsive (desktop-first)
- **JavaScript** - Interactions dynamiques

### Back-end
- **PHP 8.4** - Logique serveur
- **MySQL 8.0** - Base de données relationnelle
- **PDO** - Accès sécurisé aux données
- **Architecture procédurale structurée** - Séparation logique métier/présentation

### Sécurité
- **Bcrypt** - Hashage des mots de passe (cost 12)
- **Requêtes préparées** - Protection injection SQL
- **Validation stricte** - Entrées utilisateur
- **Upload sécurisé** - Images avec vérification MIME

---

## 📋 Prérequis

Avant d'installer le projet, assurez-vous d'avoir :

- ✅ **Docker Desktop** installé ([télécharger Docker](https://www.docker.com/products/docker-desktop))
- ✅ **Git** installé (pour cloner le repo)
- ✅ Un navigateur web moderne (Chrome, Firefox, Edge)

### Vérifier l'installation
```bash
# Vérifier Docker
docker --version
docker-compose --version
```

---

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/ton-username/blog_jdr_DOCKER.git
cd blog_jdr_DOCKER
```

---

### 2. Lancer les conteneurs Docker

**À la racine du projet** :
```bash
docker-compose up -d
```

Ce qui lance :
- ✅ Conteneur **Apache + PHP 8.4** (port 80)
- ✅ Conteneur **MySQL 8.0** (port 3306)
- ✅ Import automatique de la base de données

**Vérifier que les conteneurs tournent :**
```bash
docker-compose ps
```

---

### 3. Accéder au site

**Ouvrez votre navigateur** :

- 🏠 **Page d'accueil** : [http://localhost/](http://localhost/)
- 📝 **Liste des articles (CRUD)** : [http://localhost/pages/liste_articles.php](http://localhost/pages/liste_articles.php)
- 🧪 **Tests automatisés** : [http://localhost/pages/tests.php](http://localhost/pages/tests.php)

---

## 📁 Structure du projet

```
blog_jdr_DOCKER/
├── docker-compose.yml            # Configuration Docker
├── Dockerfile                    # Image PHP + Apache
├── database.php                  # Connexion PDO centralisée
├── index.php                     # Page d'accueil
├── database/
│   └── blog_jdr.sql              # Script SQL (tables + données)
├── includes/
│   └── functions.php             # Fonctions métier CRUD
├── pages/
│   ├── articles.html             # Affichage articles (front)
│   ├── liste_articles.php        # CRUD liste articles
│   ├── creer_article.php         # Création article + upload
│   ├── update_article.php        # Modification article
│   ├── delete_article.php        # Suppression article
│   ├── liste_utilisateurs.php    # CRUD liste utilisateurs
│   ├── creer_utilisateur.php     # Création utilisateur
│   ├── update_utilisateur.php    # Modification utilisateur
│   ├── delete_utilisateur.php    # Suppression utilisateur
│   ├── liste_commentaires.php    # CRUD liste commentaires
│   ├── creer_commentaire.php     # Création commentaire
│   ├── delete_commentaire.php    # Suppression commentaire
│   └── tests.php                 # Tests automatisés
└── assets/
    ├── css/                      # Styles (6 fichiers + responsive)
    ├── js/                       # Scripts JavaScript
    ├── fonts/                    # Polices personnalisées
    └── images/                   # Images uploadées
```

---

## 🗄️ Base de données

### Schéma relationnel

**3 tables principales :**

1. **`utilisateur`**
   - `auteur_id` (PK, AUTO_INCREMENT)
   - `pseudo` (UNIQUE)
   - `email` (UNIQUE)
   - `mot_de_passe` (hashé bcrypt)
   - `date_inscription`

2. **`article`**
   - `article_id` (PK, AUTO_INCREMENT)
   - `titre`
   - `contenu`
   - `extrait`
   - `categorie` (ENUM: Scénarios, Règles, Matériel, Univers, Conseils)
   - `image_url`
   - `date_publication`
   - `auteur_id` (FK → utilisateur, CASCADE)

3. **`commentaire`**
   - `commentaire_id` (PK, AUTO_INCREMENT)
   - `contenu_commentaire`
   - `date_commentaire`
   - `note` (1-5, CHECK constraint)
   - `auteur_id` (FK → utilisateur, CASCADE)
   - `article_id` (FK → article, CASCADE)

**Contraintes CASCADE** : La suppression d'un utilisateur supprime automatiquement ses articles et commentaires.

---

## 🔧 Fonctionnalités

### CRUD Complet

**Articles :**
- ✅ Création avec upload d'images sécurisé
- ✅ Modification (conserve ou remplace l'image)
- ✅ Suppression (+ suppression automatique de l'image)
- ✅ Liste avec pagination et filtres

**Utilisateurs :**
- ✅ Création avec hashage bcrypt
- ✅ Modification (pseudo, email, mot de passe optionnel)
- ✅ Suppression (CASCADE vers articles/commentaires)

**Commentaires :**
- ✅ Création avec note (1-5 étoiles)
- ✅ Affichage par article
- ✅ Suppression

### Upload d'images
- Formats : JPG, PNG, GIF, WebP
- Taille max : 5 MB
- Noms uniques : `article_[uniqid].ext`
- Suppression automatique lors de la suppression d'article

### Sécurité
- **Requêtes préparées PDO** : Protection injection SQL
- **Validation stricte** : Regex, filtres, contraintes
- **Hashage bcrypt** : Mot de passe (cost 12)
- **Sanitization** : `htmlspecialchars()` sur toutes les sorties
- **Upload sécurisé** : Vérification MIME type + taille

---

## 🧪 Tests automatisés

**Page de tests** : [http://localhost/pages/tests.php](http://localhost/pages/tests.php)

**9 catégories testées :**
1. ✅ Connexion base de données
2. ✅ Fonctions métier CRUD (toutes les fonctions de functions.php)
3. ✅ Hashage bcrypt
4. ✅ Protection injection SQL
5. ✅ Requêtes préparées
6. ✅ CRUD Articles
7. ✅ CRUD Utilisateurs
8. ✅ Gestion utilisateurs
9. ✅ Articles et commentaires

**Résultat** : Affichage en temps réel avec statut ✅/❌ pour chaque test.

---

## 🔒 Principe DRY (Don't Repeat Yourself)

**Fichier `includes/functions.php`** : Toutes les opérations CRUD centralisées

**Fonctions disponibles :**
- `getArticles()`, `getArticleById()`, `getArticlesByCategorie()`
- `creerArticle()`, `modifierArticle()`, `supprimerArticle()`
- `creerUtilisateur()`, `modifierUtilisateur()`, `supprimerUtilisateur()`
- `creerCommentaire()`, `supprimerCommentaire()`
- `uploadImageArticle()` - Gestion upload sécurisé
- `nettoyerImagesOrphelines()` - Nettoyage automatique

**Avantages :**
- ✅ Pas de duplication de code
- ✅ Maintenance simplifiée
- ✅ Validations uniformes
- ✅ Tests centralisés

---

## 🐳 Commandes Docker utiles

```bash
# Démarrer les conteneurs
docker-compose up -d

# Arrêter les conteneurs
docker-compose down

# Voir les logs
docker-compose logs -f

# Accéder au conteneur PHP
docker exec -it blog_jdr_web bash

# Accéder à MySQL
docker exec -it blog_jdr_mysql mysql -u root -prootpassword blog_jdr

# Reconstruire les conteneurs
docker-compose up -d --build
```

---

## 🐛 Dépannage

### Les conteneurs ne démarrent pas
```bash
# Vérifier les logs
docker-compose logs

# Nettoyer et reconstruire
docker-compose down -v
docker-compose up -d --build
```

### Erreur "Port 80 déjà utilisé"
➡️ Modifiez le port dans `docker-compose.yml` :
```yaml
ports:
  - "8080:80"
```

### Images non affichées
➡️ Vérifiez les permissions du dossier `assets/images/` :
```bash
chmod -R 755 assets/images/
```

---

## 📦 Déploiement en production

**⚠️ Avant de déployer :**

1. **Désactiver l'affichage des erreurs** dans PHP
2. **Changer les credentials** MySQL (pas root/rootpassword)
3. **Utiliser HTTPS** (certificat SSL)
4. **Optimiser les images** (compression)
5. **Activer le cache** navigateur
6. **Sauvegardes régulières** de la BDD

---

## 👤 Auteur

**Mélody** - Développeuse Web & Web Mobile  
Projet réalisé dans le cadre de la formation ENACO DWWM (2026)

---

## 📄 Licence

Ce projet est développé dans un cadre pédagogique.

---

## 🔗 Liens utiles

- [Documentation Docker](https://docs.docker.com/)
- [Documentation PHP](https://www.php.net/docs.php)
- [Documentation MySQL](https://dev.mysql.com/doc/)
- [Guide PDO](https://www.php.net/manual/fr/book.pdo.php)

---

**Bon développement ! 🎲✨**