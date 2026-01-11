# 📚 Les Chroniques du JDR - Blog Communautaire

Blog dédié au jeu de rôle sur table, développé pour l'association "La Compagnie des Âmes égarées".

## 🎯 Objectif du projet

Créer une plateforme web permettant de partager articles, conseils et actualités sur le jeu de rôle, tout en favorisant les échanges entre rôlistes débutants et confirmés.

---

## 🛠️ Technologies utilisées

### Front-end
- **HTML5** - Structure sémantique
- **CSS3** - Design responsive (mobile-first)
- **JavaScript vanilla** - Interactions dynamiques

### Back-end
- **PHP 8.4** - Logique serveur
- **MySQL 8.0** - Base de données relationnelle
- **PDO** - Accès sécurisé aux données

### Outils
- **VS Code** - Éditeur de code
- **MySQL Workbench** - Gestion de la base de données
- **Git/GitHub** - Versionning du code
- **PHP Built-in Server** - Serveur de développement

---

## 📋 Prérequis

Avant d'installer le projet, assurez-vous d'avoir :

- ✅ **PHP 8.4+** installé ([télécharger PHP](https://windows.php.net/download/))
- ✅ **MySQL 8.0+** installé ([télécharger MySQL](https://dev.mysql.com/downloads/installer/))
- ✅ **Git** installé (optionnel, pour cloner le repo)
- ✅ Un navigateur web moderne (Chrome, Firefox, Edge)

### Vérifier l'installation
```bash
# Vérifier PHP
php -v

# Vérifier MySQL (dans MySQL Workbench ou cmd)
mysql --version
```

---

## 🚀 Installation

### 1. Cloner ou télécharger le projet

**Option A : Avec Git**
```bash
git clone https://github.com/ton-username/blog_jdr.git
cd blog_jdr
```

**Option B : Sans Git**
- Téléchargez le ZIP du projet
- Décompressez dans un dossier de votre choix

---

### 2. Créer la base de données

**Ouvrez MySQL Workbench** et connectez-vous à votre instance MySQL locale.

**Exécutez le script SQL** `blog_jdr.sql` situé à la racine du projet :
```sql
-- Copier-coller le contenu de blog_jdr.sql dans MySQL Workbench
-- OU importer le fichier via : File > Run SQL Script
```

Ce script va :
- ✅ Créer la base de données `blog_jdr`
- ✅ Créer les 3 tables (`utilisateur`, `Article`, `Commentaire`)
- ✅ Insérer des données de test

**Vérification :**
```sql
USE blog_jdr;
SHOW TABLES;
SELECT * FROM Article;
```

Vous devriez voir 4 articles insérés.

---

### 3. Configurer la connexion à la base de données

**Ouvrez le fichier** `config/database.php` et **modifiez les identifiants** :
```php
<?php
$host = 'localhost';
$dbname = 'blog_jdr';
$username = 'root';
$password = 'VOTRE_MOT_DE_PASSE_MYSQL';  // ⚠️ Remplacez par votre mot de passe !
```

---

### 4. Lancer le serveur de développement

**Dans un terminal, à la racine du projet** :
```bash
# Naviguer vers le dossier du projet
cd chemin/vers/blog_jdr

# Lancer le serveur PHP sur le port 8000
php -S localhost:8000
```

Vous devriez voir :
```
PHP 8.4.14 Development Server (http://localhost:8000) started
```

---

### 5. Accéder au site

**Ouvrez votre navigateur** et accédez à :

- 🏠 **Page d'accueil** : [http://localhost:8000/index.php](http://localhost:8000/index.php)
- 📝 **Liste des articles (test)** : [http://localhost:8000/pages/liste_articles.php](http://localhost:8000/pages/liste_articles.php)

---

## 📁 Structure du projet
```
blog_jdr/
├── config/
│   └── database.php              # Configuration connexion BDD
├── pages/
│   ├── index.php                 # Page d'accueil
│   ├── liste_articles.php        # Affichage des articles (démo)
│   └── creer_utilisateur.php     # Test création utilisateur
├── assets/
│   ├──css/
│   │   ├── style_index.css       # Styles de base
│   │   └── responsive_index.css  # Mobile & Tablette
│   ├──fonts/                     # Polices du blog
│   ├──images/                    # Images du blog
│   └──js/                        # Scripts JavaScript du blog
├── blog_jdr.sql              # Script de création BDD
└── README.md                 # Cette documentation
```

---

## 🔒 Sécurité

Le projet implémente plusieurs mesures de sécurité :

✅ **Hashage des mots de passe** avec bcrypt (cost 12)
✅ **Requêtes préparées PDO** (protection contre injection SQL)
✅ **Protection XSS** avec `htmlspecialchars()`
✅ **Validation des entrées** (regex, filtres PHP)
✅ **Sanitization LocalStorage** (protection contre code malveillant)

---

## 🧪 Tests

### Tester la connexion à la BDD
```bash
php pages/test.php
```

Vous devriez voir : `✅ Connexion à la base de données réussie !`

### Tester la création d'utilisateur

Accédez à : [http://localhost:8000/pages/creer_utilisateur.php](http://localhost:8000/pages/creer_utilisateur.php)

---

## 🐛 Dépannage

### Erreur : "Access denied for user 'root'@'localhost'"
➡️ Vérifiez le mot de passe dans `config/database.php`

### Erreur : "Could not find driver"
➡️ Activez l'extension PDO MySQL dans `php.ini` :
```ini
extension=pdo_mysql
```

### Page blanche
➡️ Activez l'affichage des erreurs :
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Port 8000 déjà utilisé
➡️ Utilisez un autre port :
```bash
php -S localhost:8080
```

---

## 📦 Déploiement en production

**⚠️ Avant de déployer en production :**

1. **Désactiver l'affichage des erreurs** dans `php.ini` :
```ini
   display_errors = Off
```

2. **Utiliser des variables d'environnement** pour les credentials BDD

3. **Activer HTTPS** (certificat SSL)

4. **Configurer un serveur web** (Apache ou Nginx) au lieu du serveur PHP built-in

5. **Optimiser les performances** :
   - Minifier CSS/JS
   - Compresser les images
   - Activer le cache navigateur

---

## 👤 Auteur

**Mélody** - Développeuse Web & Web Mobile  
Projet réalisé dans le cadre de la formation DWWM - ENACO (2025)

---

## 📄 Licence

Ce projet est développé dans un cadre pédagogique pour l'association "La Compagnie des Âmes égarées".

---

## 🔗 Liens utiles

- [Documentation PHP](https://www.php.net/docs.php)
- [Documentation MySQL](https://dev.mysql.com/doc/)
- [Guide PDO](https://www.php.net/manual/fr/book.pdo.php)

---

**Bon développement ! 🎲✨**# projet_examen_fullstack_blog_jdr
