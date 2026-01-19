<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../includes/functions.php';

// ========================================
// FORMULAIRE DE SUPPRESSION D'UTILISATEUR
// ========================================

$auteur_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($auteur_id <= 0) { die('❌ ID utilisateur manquant'); }

$message = '';
$message_type = '';
$user = null;

try {
    $stmt = $pdo->prepare('SELECT auteur_id, pseudo, email, date_inscription FROM utilisateur WHERE auteur_id = :id');
    $stmt->execute([':id' => $auteur_id]);
    $user = $stmt->fetch();
    if (!$user) { die('❌ utilisateur introuvable'); }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $confirmation = $_POST['confirmation'] ?? '';
        if ($confirmation === 'OUI') {
            // Utilisation de la fonction du fichier functions.php
            $del = supprimerUtilisateur($pdo, $auteur_id);
            if ($del) {
                $message = '✅ utilisateur supprimé (articles et commentaires liés supprimés via CASCADE)';
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
    <link rel="stylesheet" href="../assets/css/style_crud.css">
    <title>Supprimer un utilisateur</title>
</head>
<body>
<div class="container">
    <h1>🗑️ Supprimer un utilisateur</h1>

    <div class="nav">
        <a href="../index.php">Accueil</a>
        <a href="liste_articles.php">Articles</a>
        <a href="liste_utilisateurs.php">Utilisateurs</a>
        <a href="liste_commentaires.php">Commentaires</a>
    </div>

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
