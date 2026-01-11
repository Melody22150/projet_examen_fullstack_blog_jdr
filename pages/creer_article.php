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
    $image_url = trim($_POST['image_url'] ?? '');
    $auteur_id = intval($_POST['auteur_id'] ?? 0);

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
        if ($image_url !== '' && !filter_var($image_url, FILTER_VALIDATE_URL)) {
            throw new Exception("L'URL de l'image n'est pas valide.");
        }
        if ($auteur_id <= 0) {
            throw new Exception("Veuillez sélectionner un auteur.");
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
    <title>Créer un article</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4e8d8; }
        .container { max-width: 800px; margin: 40px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,.08); }
        h1 { color:#8B4513; text-align:center; }
        .message { padding:12px; border-radius:6px; margin-bottom:16px; text-align:center; }
        .message.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .message.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .form-group { margin-bottom:16px; }
        label { display:block; font-weight:bold; color:#8B4513; margin-bottom:8px; }
        input[type=text], input[type=url], select, textarea { width:100%; padding:10px; border:2px solid #D2691E; border-radius:6px; }
        textarea { min-height:180px; resize:vertical; }
        .row { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
        .actions { display:flex; gap:12px; justify-content:center; margin-top:20px; }
        button, a.btn { padding:10px 18px; border:none; border-radius:6px; cursor:pointer; text-decoration:none; }
        button { background:#8B4513; color:#fff; }
        button:hover { background:#6b3410; }
        a.btn { background:#777; color:#fff; }
        a.btn:hover { background:#555; }
    </style>
</head>
<body>
<div class="container">
    <h1>📝 Créer un article</h1>

    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post">
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
                <label for="image_url">URL image</label>
                <input type="url" id="image_url" name="image_url" placeholder="https://...">
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
            <a class="btn" href="liste_articles.php">Retour à la liste</a>
        </div>
    </form>
</div>
</body>
</html>
