<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../database.php';

$message = '';
$message_type = '';
$authors = [];
$articles = [];
$pref_article_id = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;

try {
    $authors = $pdo->query("SELECT auteur_id, pseudo FROM utilisateur ORDER BY pseudo ASC")->fetchAll();
    $articles = $pdo->query("SELECT article_id, titre FROM article ORDER BY date_publication DESC")->fetchAll();
} catch (PDOException $e) {
    $message = '❌ Erreur base: ' . htmlspecialchars($e->getMessage());
    $message_type = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu_commentaire'] ?? '');
    $note = isset($_POST['note']) ? (int)$_POST['note'] : null;
    $auteur_id = 1; // Utilisateur par défaut
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;

    try {
        if ($contenu === '') { throw new Exception('Le contenu du commentaire est obligatoire.'); }
        if ($note === null || $note < 1 || $note > 5) { throw new Exception('La note doit être comprise entre 1 et 5.'); }
        if ($article_id <= 0) { throw new Exception('Veuillez sélectionner un article.'); }

        $sql = 'INSERT INTO commentaire (contenu_commentaire, note, auteur_id, article_id) VALUES (:contenu, :note, :auteur_id, :article_id)';
        $ok = $pdo->prepare($sql)->execute([
            ':contenu' => $contenu,
            ':note' => $note,
            ':auteur_id' => $auteur_id,
            ':article_id' => $article_id,
        ]);
        if ($ok) {
            $message = '✅ Commentaire ajouté avec succès';
            $message_type = 'success';
        } else {
            throw new Exception('Insertion du commentaire échouée');
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
    <title>Ajouter un commentaire</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4e8d8; }
        .container { max-width: 800px; margin: 40px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,.08); }
        h1 { color:#8B4513; text-align:center; }
        .message { padding:12px; border-radius:6px; margin-bottom:16px; text-align:center; }
        .message.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .message.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .form-group { margin-bottom:16px; }
        label { display:block; font-weight:bold; color:#8B4513; margin-bottom:8px; }
        input, select, textarea { width:100%; padding:10px; border:2px solid #D2691E; border-radius:6px; }
        textarea { min-height:140px; }
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
    <h1>💬 Ajouter un commentaire</h1>

    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="article_id">Article *</label>
            <select id="article_id" name="article_id" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($articles as $art): $aid = (int)$art['article_id']; ?>
                    <option value="<?php echo $aid; ?>" <?php echo ($pref_article_id === $aid) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($art['titre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="auteur_id">Note *</label>
            <select id="note" name="note" required>
                <option value="">-- Sélectionner --</option>
                <?php for ($i=1; $i<=5; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="contenu_commentaire">Contenu *</label>
            <textarea id="contenu_commentaire" name="contenu_commentaire" required></textarea>
        </div>
        <div class="actions">
            <button type="submit">Ajouter</button>
            <a class="btn" href="liste_commentaires.php">Voir les commentaires</a>
            <a class="btn" href="liste_articles.php">Retour aux articles</a>
        </div>
    </form>
</div>
</body>
</html>
