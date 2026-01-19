<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../includes/functions.php';

// ========================================
// FORMULAIRE DE SUPPRESSION DE COMMENTAIRE
// ========================================

$commentaire_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($commentaire_id <= 0) { die('❌ ID commentaire manquant'); }

$message = '';
$message_type = '';
$comment = null;

try {
    $stmt = $pdo->prepare('SELECT c.commentaire_id, c.contenu_commentaire, c.date_commentaire, c.note, a.titre AS article_titre, u.pseudo AS auteur
                           FROM commentaire c INNER JOIN article a ON c.article_id = a.article_id INNER JOIN utilisateur u ON c.auteur_id = u.auteur_id
                           WHERE c.commentaire_id = :id');
    $stmt->execute([':id' => $commentaire_id]);
    $comment = $stmt->fetch();
    if (!$comment) { die('❌ Commentaire introuvable'); }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $confirmation = $_POST['confirmation'] ?? '';
        if ($confirmation === 'OUI') {
            // Utilisation de la fonction du fichier functions.php
            $del = supprimerCommentaire($pdo, $commentaire_id);
            if ($del) {
                $message = '✅ Commentaire supprimé';
                $message_type = 'success';
                header('Refresh: 2; url=liste_commentaires.php');
            } else {
                $message = '❌ Suppression échouée';
                $message_type = 'error';
            }
        } elseif ($confirmation === 'NON') {
            header('Location: liste_commentaires.php');
            exit;
        }
    }
} catch (PDOException $e) {
    die('❌ Erreur base: ' . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style_crud.css">
    <title>Supprimer un commentaire</title>
</head>
<body>
<div class="container">
    <h1>🗑️ Supprimer un commentaire</h1>

    <div class="nav">
        <a href="../index.php">Accueil</a>
        <a href="liste_articles.php">Articles</a>
        <a href="liste_utilisateurs.php">Utilisateurs</a>
        <a href="liste_commentaires.php">Commentaires</a>
    </div>

    <div class="warning">⚠️ Cette action est définitive.</div>

    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="info">
        <p><strong>Article:</strong> <?php echo htmlspecialchars($comment['article_titre']); ?></p>
        <p><strong>Auteur:</strong> <?php echo htmlspecialchars($comment['auteur']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($comment['date_commentaire']); ?></p>
        <p><strong>Note:</strong> <?php echo (int)$comment['note']; ?>/5</p>
        <p><?php echo nl2br(htmlspecialchars($comment['contenu_commentaire'])); ?></p>
    </div>

    <form method="post">
        <div class="form-group">
            <label><input type="radio" name="confirmation" value="NON" checked> ❌ Non, conserver</label>
            <label><input type="radio" name="confirmation" value="OUI"> ✅ Oui, supprimer</label>
        </div>
        <div class="actions">
            <button type="submit">Confirmer la suppression</button>
            <a class="btn" href="liste_commentaires.php">↩️ Retour</a>
        </div>
    </form>
</div>
</body>
</html>
