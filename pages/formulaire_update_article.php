<?php
/**
 * Formulaire de modification d'un article
 * Auteur : Mélody
 * Date : Janvier 2026
 */

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../includes/functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$article = null;
$message = '';
$message_type = '';

// Récupération de l'ID de l'article à modifier
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($article_id === 0) {
    die("❌ Erreur : ID article manquant");
}

try {
    // Récupérer l'article existant
    $sql = "SELECT * FROM article WHERE article_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $article_id]);
    $article = $stmt->fetch();
    
    if (!$article) {
        die("❌ Erreur : Article non trouvé");
    }
    
    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre = trim($_POST['titre'] ?? '');
        $extrait = trim($_POST['extrait'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $categorie = trim($_POST['categorie'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');
        
        // Validation basique
        if (empty($titre) || empty($contenu) || empty($categorie)) {
            $message = "❌ Erreur : Tous les champs obligatoires doivent être remplis";
            $message_type = 'error';
        } else {
            // Appel de la fonction updateArticle
            $sql_update = "UPDATE article 
                          SET titre = :titre, 
                              contenu = :contenu, 
                              extrait = :extrait, 
                              categorie = :categorie, 
                              image_url = :image_url
                          WHERE article_id = :article_id";
            
            $stmt_update = $pdo->prepare($sql_update);
            $resultat = $stmt_update->execute([
                ':titre' => $titre,
                ':contenu' => $contenu,
                ':extrait' => $extrait,
                ':categorie' => $categorie,
                ':image_url' => $image_url,
                ':article_id' => $article_id
            ]);
            
            if ($resultat) {
                $message = "✅ Article modifié avec succès !";
                $message_type = 'success';
                // Rafraîchir les données de l'article
                $article['titre'] = $titre;
                $article['contenu'] = $contenu;
                $article['extrait'] = $extrait;
                $article['categorie'] = $categorie;
                $article['image_url'] = $image_url;
            } else {
                $message = "❌ Erreur lors de la modification de l'article";
                $message_type = 'error';
            }
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
    <title>Modifier un article - Les Chroniques du JDR</title>
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
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #8B4513;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .article-id {
            text-align: center;
            color: #666;
            font-size: 0.9em;
            margin-bottom: 20px;
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
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #8B4513;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="url"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #D2691E;
            border-radius: 4px;
            font-family: 'Arial', sans-serif;
            font-size: 1em;
            color: #333;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="url"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #8B4513;
            box-shadow: 0 0 5px rgba(139, 69, 19, 0.3);
        }
        
        textarea {
            resize: vertical;
            min-height: 200px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
            background-color: #8B4513;
            color: white;
        }
        
        button[type="submit"]:hover {
            background-color: #6b3410;
        }
        
        a.btn-cancel {
            background-color: #999;
            color: white;
        }
        
        a.btn-cancel:hover {
            background-color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Modifier un article</h1>
        <div class="article-id">Article ID: <?php echo $article_id; ?></div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($article['titre']); ?>" required maxlength="200">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="categorie">Catégorie *</label>
                    <select id="categorie" name="categorie" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="Scénarios" <?php echo $article['categorie'] === 'Scénarios' ? 'selected' : ''; ?>>Scénarios</option>
                        <option value="Règles" <?php echo $article['categorie'] === 'Règles' ? 'selected' : ''; ?>>Règles</option>
                        <option value="Matériel" <?php echo $article['categorie'] === 'Matériel' ? 'selected' : ''; ?>>Matériel</option>
                        <option value="Univers" <?php echo $article['categorie'] === 'Univers' ? 'selected' : ''; ?>>Univers</option>
                        <option value="Conseils" <?php echo $article['categorie'] === 'Conseils' ? 'selected' : ''; ?>>Conseils</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image_url">URL Image</label>
                    <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($article['image_url'] ?? ''); ?>" placeholder="https://...">
                </div>
            </div>
            
            <div class="form-group">
                <label for="extrait">Extrait</label>
                <input type="text" id="extrait" name="extrait" value="<?php echo htmlspecialchars($article['extrait'] ?? ''); ?>" maxlength="300">
            </div>
            
            <div class="form-group">
                <label for="contenu">Contenu *</label>
                <textarea id="contenu" name="contenu" required><?php echo htmlspecialchars($article['contenu']); ?></textarea>
            </div>
            
            <div class="form-buttons">
                <button type="submit">💾 Modifier l'article</button>
                <a href="liste_articles.php" class="btn-cancel">❌ Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>
