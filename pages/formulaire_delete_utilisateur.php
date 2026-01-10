<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../database.php';

$auteur_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($auteur_id <= 0) { die('❌ ID utilisateur manquant'); }

$message = '';
$message_type = '';
$user = null;

try {
    $stmt = $pdo->prepare('SELECT auteur_id, pseudo, email, date_inscription FROM utilisateur WHERE auteur_id = :id');
    $stmt->execute([':id' => $auteur_id]);
    $user = $stmt->fetch();
    if (!$user) { die('❌ Utilisateur introuvable'); }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $confirmation = $_POST['confirmation'] ?? '';
        if ($confirmation === 'OUI') {
            $del = $pdo->prepare('DELETE FROM utilisateur WHERE auteur_id = :id')->execute([':id' => $auteur_id]);
            if ($del) {
                $message = '✅ Utilisateur supprimé (articles et commentaires liés supprimés via CASCADE)';
                $message_type = 'success';
                header('Refresh: 2; url=liste_utilisateurs.php');
            } else {
                $message = '❌ Suppression échouée';
                $message_type = 'error';
            }
        } elseif ($confirmation === 'NON') {
            header('Location: liste_utilisateurs.php');
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
    <title>Supprimer un utilisateur</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4e8d8; }
        .container { max-width: 700px; margin: 40px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,.08); }
        h1 { color:#c33; text-align:center; }
        .warning { background:#fff3cd; color:#856404; border:2px solid #ffc107; padding:12px; border-radius:6px; margin-bottom:16px; }
        .message { padding:12px; border-radius:6px; margin-bottom:16px; text-align:center; }
        .message.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .message.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .info { background:#f8f9fa; border-left:4px solid #8B4513; padding:12px; margin-bottom:16px; }
        .form-group { text-align:center; margin: 16px 0; }
        .actions { display:flex; gap:12px; justify-content:center; margin-top:20px; }
        button, a.btn { padding:10px 18px; border:none; border-radius:6px; cursor:pointer; text-decoration:none; }
        button { background:#c33; color:#fff; }
        button:hover { background:#a22; }
        a.btn { background:#777; color:#fff; }
        a.btn:hover { background:#555; }
        label { font-weight:bold; }
    </style>
</head>
<body>
<div class="container">
    <h1>🗑️ Supprimer un utilisateur</h1>

    <div class="warning">⚠️ Cette action est définitive. Les articles/commentaires liés seront supprimés (CASCADE).</div>

    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="info">
        <p><strong>ID:</strong> <?php echo (int)$user['auteur_id']; ?></p>
        <p><strong>Pseudo:</strong> <?php echo htmlspecialchars($user['pseudo']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Inscription:</strong> <?php echo htmlspecialchars($user['date_inscription']); ?></p>
    </div>

    <form method="post">
        <div class="form-group">
            <label><input type="radio" name="confirmation" value="NON" checked> ❌ Non, conserver</label>
            <label><input type="radio" name="confirmation" value="OUI"> ✅ Oui, supprimer</label>
        </div>
        <div class="actions">
            <button type="submit">Confirmer la suppression</button>
            <a class="btn" href="liste_utilisateurs.php">↩️ Retour</a>
        </div>
    </form>
</div>
</body>
</html>
