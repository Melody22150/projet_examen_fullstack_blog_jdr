<?php
/**
 * =============================================
 * LISTE DES ARTICLES - Interface CRUD
 * =============================================
 * Description : Affiche tous les articles du blog avec leurs métadonnées
 * Auteur : Mélody
 * Date : Janvier 2026
 * Fonctionnalités :
 * - Liste complète des articles avec auteur et nombre de commentaires
 * - Actions : Modifier, Supprimer, Commenter, Voir commentaires
 * - Bouton de création d'article
 */

// Activation de l'affichage des erreurs pour le développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées et encodage -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Feuille de style commune aux pages CRUD -->
    <link rel="stylesheet" href="../assets/css/style_crud.css">
    <title>Liste des articles - Les Chroniques du JDR</title>
</head>
<body>
    <!-- Titre principal de la page -->
    <h1 class="page-title">Les Chroniques du JDR - Articles</h1>
    
    <!-- Navigation entre les sections CRUD -->
    <div class="nav">
        <a href="../index.php">Accueil</a>
        <a href="liste_articles.php">Articles</a>
        <a href="liste_utilisateurs.php">Utilisateurs</a>
        <a href="liste_commentaires.php">Commentaires</a>
    </div>
    
    <!-- Bouton pour créer un nouvel article -->
    <div class="create-article-container">
        <a href="creer_article.php" class="btn-create-article">➥ Créer un article</a>
    </div>
    
    <?php
    // Inclusion de la connexion à la base de données
    require_once __DIR__ . '/../database.php';
    
    try {
        // Requête préparée pour récupérer tous les articles
        // Jointure avec utilisateur pour obtenir le pseudo de l'auteur
        // Sous-requête pour compter les commentaires par article
        $sql = "SELECT 
                    a.article_id,
                    a.titre,
                    a.extrait,
                    a.categorie,
                    a.image_url,
                    a.date_publication,
                    u.pseudo AS auteur,
                    (SELECT COUNT(*) FROM commentaire c WHERE c.article_id = a.article_id) AS nb_commentaires
                FROM article a
                INNER JOIN utilisateur u ON a.auteur_id = u.auteur_id
                ORDER BY a.date_publication DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $articles = $stmt->fetchAll();
        
        // Affichage du nombre total d'articles trouvés
        echo "<p class='articles-count'><strong>" . count($articles) . " article(s) trouvé(s)</strong></p>";
        
        // Boucle d'affichage de chaque article
        foreach ($articles as $article) {
            echo '<div class="article-liste">';
            
            // Affichage de l'image si elle existe
            if (!empty($article['image_url'])) {
                echo '<img src="../' . htmlspecialchars($article['image_url']) . '" alt="' . htmlspecialchars($article['titre'] ?? '') . '" class="article-liste-image">';
            }
            
            echo '<h2 class="article-liste-titre">' . htmlspecialchars($article['titre'] ?? '') . '</h2>';
            echo '<p class="article-liste-extrait">' . htmlspecialchars($article['extrait'] ?? '') . '</p>';
            echo '<p><span class="categorie-badge">' . htmlspecialchars($article['categorie'] ?? '') . '</span></p>';
            echo '<p class="article-liste-meta">Par <strong>' . htmlspecialchars($article['auteur'] ?? '') . '</strong> - ' . date('d/m/Y', strtotime($article['date_publication'])) . ' — 💬 ' . (int)$article['nb_commentaires'] . ' commentaire(s)</p>';
            echo '<div class="article-liste-actions">';
            echo '<a href="update_article.php?id=' . $article['article_id'] . '" class="btn-action btn-modifier">✏️ Modifier</a>';
            echo '<a href="delete_article.php?id=' . $article['article_id'] . '" class="btn-action btn-supprimer">🗑️ Supprimer</a>';
            echo '<a href="creer_commentaire.php?article_id=' . $article['article_id'] . '" class="btn-action btn-commenter">➕ Commenter</a>';
            echo '<a href="liste_commentaires.php?article_id=' . $article['article_id'] . '" class="btn-action btn-voir">👁️ Voir commentaires</a>';
            echo '</div>';
            echo '</div>';
        }
        
    } catch(PDOException $e) {
        echo "<p class='error-message'>Erreur ❌: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
</body>
</html>