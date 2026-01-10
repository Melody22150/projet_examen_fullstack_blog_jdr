<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../database.php';

$article_id = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;
$message = '';
$message_type = '';
$comments = [];

try {
    $sql = "SELECT c.commentaire_id, c.contenu_commentaire, c.date_commentaire, c.note,
                   a.article_id, a.titre,
                   u.auteur_id, u.pseudo
            FROM commentaire c
            INNER JOIN article a ON c.article_id = a.article_id
            INNER JOIN utilisateur u ON c.auteur_id = u.auteur_id
            " . ($article_id > 0 ? "WHERE c.article_id = :aid" : "") . "
            ORDER BY c.date_commentaire DESC";

    $stmt = $pdo->prepare($sql);
    if ($article_id > 0) { $stmt->bindValue(':aid', $article_id, PDO::PARAM_INT); }
    $stmt->execute();
    $comments = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = '❌ Erreur base: ' . htmlspecialchars($e->getMessage());
    $message_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commentaires</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4e8d8; }
        .container { max-width: 900px; margin: 40px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,.08); }
        h1 { color:#8B4513; }
        .message { padding:12px; border-radius:6px; margin-bottom:16px; }
        .message.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .message.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .comment { border:2px solid #8B4513; padding:12px; border-radius:8px; margin-bottom:14px; }
        .meta { color:#666; font-size:0.9em; }
        .actions a { margin-right:10px; font-weight:bold; text-decoration:none; }
        .del { color:#c33; }
    </style>
</head>
<body>
<div class="container">
    <h1>💬 Commentaires <?php if ($article_id>0) echo '— Article #' . $article_id; ?></h1>
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <p><a href="formulaire_creer_commentaire.php<?php echo $article_id>0 ? ('?article_id='.$article_id) : '' ; ?>" style="background:#8B4513;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none;">➕ Ajouter un commentaire</a>
       <a href="liste_articles.php" style="margin-left:10px;">↩️ Retour aux articles</a></p>

    <p><strong><?php echo count($comments); ?></strong> commentaire(s) trouvé(s)</p>

    <?php foreach ($comments as $c): ?>
        <div class="comment">
            <p><strong>Article:</strong> <?php echo htmlspecialchars($c['titre']); ?> (ID: <?php echo (int)$c['article_id']; ?>)</p>
            <p><strong>Auteur:</strong> <?php echo htmlspecialchars($c['pseudo']); ?> — <span class="meta">Note: <?php echo (int)$c['note']; ?>/5, le <?php echo htmlspecialchars($c['date_commentaire']); ?></span></p>
            <p><?php echo nl2br(htmlspecialchars($c['contenu_commentaire'])); ?></p>
            <p class="actions">
                <a class="del" href="formulaire_delete_commentaire.php?id=<?php echo (int)$c['commentaire_id']; ?>">🗑️ Supprimer</a>
            </p>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
