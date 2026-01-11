<?php
/**
 * Formulaire de suppression d'un article
 * Auteur : Mélody
 * Date : Janvier 2026
 */

require_once __DIR__ . '/../database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$article = null;
$message = '';
$message_type = '';

// Récupération de l'ID de l'article à supprimer
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($article_id === 0) {
    die("❌ Erreur : ID article manquant");
}

try {
    // Récupérer l'article existant
    $sql = "SELECT a.*, u.pseudo AS auteur FROM article a
            INNER JOIN utilisateur u ON a.auteur_id = u.auteur_id
            WHERE a.article_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $article_id]);
    $article = $stmt->fetch();
    
    if (!$article) {
        die("❌ Erreur : Article non trouvé");
    }
    
    // Traitement de la suppression
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $confirmation = isset($_POST['confirmation']) ? $_POST['confirmation'] : '';
        
        if ($confirmation === 'OUI') {
            // Supprimer l'article
            $sql_delete = "DELETE FROM article WHERE article_id = :article_id";
            $stmt_delete = $pdo->prepare($sql_delete);
            $resultat = $stmt_delete->execute([':article_id' => $article_id]);
            
            if ($resultat) {
                $message = "✅ Article supprimé avec succès !";
                $message_type = 'success';
                // Redirection après 2 secondes
                header('Refresh: 2; url=liste_articles.php');
            } else {
                $message = "❌ Erreur lors de la suppression de l'article";
                $message_type = 'error';
            }
        } else if ($confirmation === 'NON') {
            header('Location: liste_articles.php');
            exit;
        }
    }
    
} catch(PDOException $e) {
    die("❌ Erreur base de données : " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style_crud.css">
    <title>Supprimer un article - Les Chroniques du JDR</title>
</head>
<body>
    <div class="container">
        <h1>🗑️ Supprimer un article</h1>
        
        <div class="nav">
            <a href="../index.php">Accueil</a>
            <a href="liste_articles.php">Articles</a>
            <a href="liste_utilisateurs.php">Utilisateurs</a>
            <a href="liste_commentaires.php">Commentaires</a>
        </div>
        
        <div class="warning">
            <strong>⚠️ Attention !</strong> Cette action est définitive et ne peut pas être annulée. Les commentaires associés seront également supprimés.
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($article && empty($message)): ?>
            <div class="article-info">
                <h2><?php echo htmlspecialchars($article['titre']); ?></h2>
                <p><strong>Auteur:</strong> <?php echo htmlspecialchars($article['auteur']); ?></p>
                <p><strong>Date de publication:</strong> <?php echo date('d/m/Y', strtotime($article['date_publication'])); ?></p>
                <p><strong>Catégorie:</strong> <span class="categorie-badge"><?php echo htmlspecialchars($article['categorie']); ?></span></p>
                <p><strong>Extrait:</strong> <?php echo htmlspecialchars(substr($article['extrait'] ?? '', 0, 100)) . '...'; ?></p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label>
                        <input type="radio" name="confirmation" value="NON" checked> ❌ Non, conserver l'article
                    </label>
                    <label>
                        <input type="radio" name="confirmation" value="OUI"> ✅ Oui, supprimer l'article
                    </label>
                </div>
                
                <div class="form-buttons">
                    <button type="submit">🗑️ Confirmer la suppression</button>
                    <a href="liste_articles.php" class="btn-cancel">↩️ Retour</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
