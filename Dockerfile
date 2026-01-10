FROM php:8.2-apache

# Installer les dépendances nécessaires pour PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite pour Apache (optionnel mais recommandé)
RUN a2enmod rewrite

# Définir le répertoire de travail
WORKDIR /var/www/html
