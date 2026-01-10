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
    <title>Supprimer un article - Les Chroniques du JDR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4e8d8;
            color: #333;
        }
        
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #c33;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .warning {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            color: #856404;
        }
        
        .article-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #8B4513;
        }
        
        .article-info h2 {
            color: #8B4513;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        
        .article-info p {
            color: #666;
            margin-bottom: 8px;
        }
        
        .article-info strong {
            color: #333;
        }
        
        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input[type="radio"] {
            margin: 0 5px;
            cursor: pointer;
        }
        
        .form-group label input {
            margin-right: 8px;
        }
        
        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
        }
        
        button,
        a.btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        
        button[type="submit"] {
            background-color: #c33;
            color: white;
        }
        
        button[type="submit"]:hover {
            background-color: #a22;
        }
        
        a.btn-cancel {
            background-color: #999;
            color: white;
        }
        
        a.btn-cancel:hover {
            background-color: #777;
        }
        
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗑️ Supprimer un article</h1>
        
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
                <p><strong>Catégorie:</strong> <span style="background: #8B4513; color: white; padding: 3px 8px; border-radius: 3px;"><?php echo htmlspecialchars($article['categorie']); ?></span></p>
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
