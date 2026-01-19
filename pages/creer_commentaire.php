<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../includes/functions.php';

// ========================================
// FORMULAIRE DE CRÉATION DE COMMENTAIRE
// ========================================

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
    $auteur_id = 1; // utilisateur par défaut
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;

    try {
        if ($contenu === '') { throw new Exception('Le contenu du commentaire est obligatoire.'); }
        if ($note === null || $note < 1 || $note > 5) { throw new Exception('La note doit être comprise entre 1 et 5.'); }
        if ($article_id <= 0) { throw new Exception('Veuillez sélectionner un article.'); }

        // Utilisation de la fonction du fichier functions.php
        creerCommentaire($pdo, $article_id, $auteur_id, $contenu, $note);
        $message = '✅ Commentaire ajouté avec succès';
        $message_type = 'success';
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
    <title>Ajouter un commentaire</title>
</head>
<body>
<div class="container">
    <h1>💬 Ajouter un commentaire</h1>
    
    <div class="nav">
        <a href="../index.php">Accueil</a>
        <a href="liste_articles.php">Articles</a>
        <a href="liste_utilisateurs.php">Utilisateurs</a>
        <a href="liste_commentaires.php">Commentaires</a>
    </div>

    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php if ($message_type === 'success'): ?>
            <script>
                setTimeout(function() {
                    window.location.href = 'liste_articles.php';
                }, 2000); // Redirection après 2 secondes
            </script>
        <?php endif; ?>
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
                    <option value="<?php echo $i; ?>"><?php echo str_repeat('⭐', $i) . ' (' . $i . '/5)'; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="contenu_commentaire">Contenu *</label>
            <textarea id="contenu_commentaire" name="contenu_commentaire" required></textarea>
        </div>
        <div class="actions">
            <button type="submit">Ajouter</button>
            <a class="btn-cancel" href="liste_articles.php">❌ Annuler</a>
        </div>
    </form>
</div>
</body>
</html>
