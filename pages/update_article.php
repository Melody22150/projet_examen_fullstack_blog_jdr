<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../includes/functions.php';

// ========================================
// FORMULAIRE DE MODIFICATION D'ARTICLE
// ========================================

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
        $image_url = $article['image_url']; // Conserver l'ancienne image par défaut
        
        try {
            // Gestion de l'upload d'image avec la fonction de functions.php
            if (isset($_FILES['image'])) {
                $nouvelle_image = uploadImageArticle($_FILES['image']);
                if ($nouvelle_image !== null) {
                    // Supprimer l'ancienne image si elle existe
                    if (!empty($article['image_url']) && file_exists(__DIR__ . '/../' . $article['image_url'])) {
                        unlink(__DIR__ . '/../' . $article['image_url']);
                    }
                    $image_url = $nouvelle_image;
                }
            }
            
            // Utilisation de la fonction modifierArticle de functions.php
            $resultat = modifierArticle($pdo, $article_id, $titre, $extrait, $contenu, $categorie, $image_url);
            
            if ($resultat) {
                $message = "✅ Article modifié avec succès !";
                $message_type = 'success';
                // Rafraîchir les données de l'article
                $article['titre'] = $titre;
                $article['contenu'] = $contenu;
                $article['extrait'] = $extrait;
                $article['categorie'] = $categorie;
                $article['image_url'] = $image_url;
            }
        } catch (Exception $e) {
            $message = '❌ ' . htmlspecialchars($e->getMessage());
            $message_type = 'error';
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
    <title>Modifier un article - Les Chroniques du JDR</title>
</head>
<body>
    <div class="container">
        <h1>✏️ Modifier un article</h1>
        
        <div class="nav">
            <a href="../index.php">Accueil</a>
            <a href="liste_articles.php">Articles</a>
            <a href="liste_utilisateurs.php">Utilisateurs</a>
            <a href="liste_commentaires.php">Commentaires</a>
        </div>
        
        <div class="article-id">Article ID: <?php echo $article_id; ?></div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
            <?php if ($message_type === 'success'): ?>
                <script>
                    setTimeout(function() {
                        window.location.href = 'liste_articles.php';
                    }, 2000); // Redirection après 2 secondes
                </script>
            <?php endif; ?>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
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
                    <label for="image">Image (JPG, PNG, GIF, WebP - Max 5 MB)</label>
                    <?php if (!empty($article['image_url'])): ?>
                        <p style="font-size: 14px; color: #666; margin-bottom: 8px;">🖼️ Image actuelle : <?php echo htmlspecialchars(basename($article['image_url'])); ?></p>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small style="color: #666;">Laissez vide pour conserver l'image actuelle</small>
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
