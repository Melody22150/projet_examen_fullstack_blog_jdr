<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../database.php';

$categories = ['Scénarios','Règles','Matériel','Univers','Conseils'];
$message = '';
$message_type = '';
$authors = [];

try {
    $stmt = $pdo->query("SELECT auteur_id, pseudo FROM utilisateur ORDER BY pseudo ASC");
    $authors = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "❌ Erreur base: " . htmlspecialchars($e->getMessage());
    $message_type = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $extrait = trim($_POST['extrait'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $auteur_id = intval($_POST['auteur_id'] ?? 0);
    $image_url = null;

    try {
        if ($titre === '' || strlen($titre) < 3 || strlen($titre) > 200) {
            throw new Exception('Le titre doit contenir entre 3 et 200 caractères.');
        }
        if ($contenu === '') {
            throw new Exception('Le contenu est obligatoire.');
        }
        if (!in_array($categorie, $categories, true)) {
            throw new Exception('Catégorie invalide.');
        }
        if ($auteur_id <= 0) {
            throw new Exception("Veuillez sélectionner un auteur.");
        }
        
        // Gestion de l'upload d'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5 MB
            
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                throw new Exception('Format d\'image non autorisé. Utilisez JPG, PNG, GIF ou WebP.');
            }
            
            if ($_FILES['image']['size'] > $max_size) {
                throw new Exception('L\'image est trop volumineuse (max 5 MB).');
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('article_', true) . '.' . $extension;
            $upload_dir = __DIR__ . '/../assets/images/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = '../assets/images/' . $filename;
            } else {
                throw new Exception('Erreur lors de l\'upload de l\'image.');
            }
        }

        $sql = "INSERT INTO article (titre, contenu, extrait, categorie, image_url, auteur_id) 
                VALUES (:titre, :contenu, :extrait, :categorie, :image_url, :auteur_id)";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            ':titre' => $titre,
            ':contenu' => $contenu,
            ':extrait' => ($extrait === '' ? null : $extrait),
            ':categorie' => $categorie,
            ':image_url' => ($image_url === '' ? null : $image_url),
            ':auteur_id' => $auteur_id,
        ]);

        if ($ok) {
            $message = '✅ Article créé avec succès !';
            $message_type = 'success';
        } else {
            throw new Exception("Impossible de créer l'article.");
        }
    } catch (Exception $e) {
        $message = '❌ ' . htmlspecialchars($e->getMessage());
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style_crud.css">
    <title>Créer un article</title>
</head>
<body>
<div class="container">
    <h1>📝 Créer un article</h1>

    <div class="nav">
        <a href="../index.php">Accueil</a>
        <a href="liste_articles.php">Articles</a>
        <a href="liste_utilisateurs.php">Utilisateurs</a>
        <a href="liste_commentaires.php">Commentaires</a>
    </div>

    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" maxlength="200" required>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="categorie">Catégorie *</label>
                <select id="categorie" name="categorie" required>
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="image">Image (JPG, PNG, GIF, WebP - Max 5 MB)</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
            </div>
        </div>
        <div class="form-group">
            <label for="extrait">Extrait</label>
            <input type="text" id="extrait" name="extrait" maxlength="300">
        </div>
        <div class="form-group">
            <label for="contenu">Contenu *</label>
            <textarea id="contenu" name="contenu" required></textarea>
        </div>
        <div class="form-group">
            <label for="auteur_id">Auteur *</label>
            <select id="auteur_id" name="auteur_id" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($authors as $a): ?>
                    <option value="<?php echo (int)$a['auteur_id']; ?>"><?php echo htmlspecialchars($a['pseudo']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="actions">
            <button type="submit">Créer l'article</button>
            <a class="btn-cancel" href="liste_articles.php">❌ Annuler</a>
        </div>
    </form>
</div>
</body>
</html>
