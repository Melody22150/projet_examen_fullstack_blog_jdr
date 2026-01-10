<?php
/**
 * Point d'entrée de l'application
 * Gère la navigation entre les pages
 */

require_once __DIR__ . '/database.php';

// Détermination de la page à afficher
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

// Inclure la page correspondante
switch($page) {
    case 'articles':
        include 'pages/liste_articles.php';
        exit;
    case 'creer-utilisateur':
        include 'pages/creer_utilisateur.php';
        exit;
    case 'accueil':
    default:
        include 'index.html';
        exit;
}
