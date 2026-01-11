<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des articles - Les Chroniques du JDR</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #8B4513; }
        .article { border: 2px solid #8B4513; padding: 15px; margin: 20px 0; border-radius: 8px; }
        .article h2 { color: #D2691E; margin-top: 0; }
        .meta { color: #666; font-size: 0.9em; }
        .categorie { background: #8B4513; color: white; padding: 5px 10px; border-radius: 5px; display: inline-block; }
    </style>
</head>
<body>
    <h1>Les Chroniques du JDR - Articles</h1>
    <div style="margin: 15px 0 25px;">
        <a href="creer_article.php" style="background: #8B4513; color: #fff; padding: 10px 14px; border-radius: 6px; text-decoration: none; font-weight: bold;">➕ Créer un article</a>
    </div>
    
    <?php
    require_once __DIR__ . '/../database.php';
    
    try {
        // Requête préparée pour récupérer tous les articles avec le pseudo de l'auteur et le nombre de commentaires
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
        
        echo "<p><strong>" . count($articles) . " article(s) trouvé(s)</strong></p>";
        
        // Affichage des articles
        foreach ($articles as $article) {
            echo '<div class="article">';
            echo '<h2>' . htmlspecialchars($article['titre']) . '</h2>';
            echo '<p>' . htmlspecialchars($article['extrait']) . '</p>';
            echo '<p><span class="categorie">' . htmlspecialchars($article['categorie']) . '</span></p>';
            echo '<p class="meta">Par <strong>' . htmlspecialchars($article['auteur']) . '</strong> - ' . date('d/m/Y', strtotime($article['date_publication'])) . ' — 💬 ' . (int)$article['nb_commentaires'] . ' commentaire(s)</p>';
            echo '<p style="margin-top: 10px;">';
            echo '<a href="update_article.php?id=' . $article['article_id'] . '" style="margin-right: 10px; color: #8B4513; text-decoration: none; font-weight: bold;">✏️ Modifier</a>';
            echo '<a href="delete_article.php?id=' . $article['article_id'] . '" style="margin-right: 10px; color: #c33; text-decoration: none; font-weight: bold;">🗑️ Supprimer</a>';
            echo '<a href="creer_commentaire.php?article_id=' . $article['article_id'] . '" style="margin-right: 10px; color: #006400; text-decoration: none; font-weight: bold;">➕ Commenter</a>';
            echo '<a href="liste_commentaires.php?article_id=' . $article['article_id'] . '" style="color: #333; text-decoration: none; font-weight: bold;">👁️ Voir commentaires</a>';
            echo '</p>';
            echo '</div>';
        }
        
    } catch(PDOException $e) {
        echo "<p style='color: red;'>Erreur ❌: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
</body>
</html>