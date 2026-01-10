<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../includes/functions.php';

// ========================================
// FORMULAIRE DE CRÉATION D'UTILISATEUR
// ========================================

$message = '';
$message_type = '';
$created_user = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = trim($_POST['mot_de_passe'] ?? '');

    try {
        $ok = creerUtilisateur($pdo, $pseudo, $email, $mot_de_passe);
        if ($ok) {
            $message = "✅ Utilisateur créé avec succès !";
            $message_type = 'success';

            // Récupérer l'utilisateur créé pour affichage
            $stmt = $pdo->prepare("SELECT auteur_id, pseudo, email, date_inscription FROM utilisateur WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $created_user = $stmt->fetch();
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
    <title>Créer un utilisateur</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4e8d8; }
        .container { max-width: 700px; margin: 40px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,.08); }
        h1 { color:#8B4513; text-align:center; }
        .message { padding:12px; border-radius:6px; margin-bottom:16px; text-align:center; }
        .message.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .message.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .form-group { margin-bottom:16px; }
        label { display:block; font-weight:bold; color:#8B4513; margin-bottom:8px; }
        input { width:100%; padding:10px; border:2px solid #D2691E; border-radius:6px; }
        .actions { display:flex; gap:12px; justify-content:center; margin-top:20px; }
        button, a.btn { padding:10px 18px; border:none; border-radius:6px; cursor:pointer; text-decoration:none; }
        button { background:#8B4513; color:#fff; }
        button:hover { background:#6b3410; }
        a.btn { background:#777; color:#fff; }
        a.btn:hover { background:#555; }
        .info { background:#f8f9fa; border-left:4px solid #8B4513; padding:12px; margin-top:16px; }
    </style>
    </head>
<body>
    <div class="container">
        <h1>👤 Créer un utilisateur</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="pseudo">Pseudo *</label>
                <input type="text" id="pseudo" name="pseudo" maxlength="50" required placeholder="Ex: TestUser2026">
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="text" id="email" name="email" required placeholder="exemple@domaine.fr">
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe *</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required placeholder="min. 8 caractères">
            </div>
            <div class="actions">
                <button type="submit">Créer le compte</button>
                <a class="btn" href="liste_utilisateurs.php">↩️ Voir les utilisateurs</a>
            </div>
        </form>

        <?php if ($created_user): ?>
            <div class="info">
                <p><strong>ID:</strong> <?php echo (int)$created_user['auteur_id']; ?></p>
                <p><strong>Pseudo:</strong> <?php echo htmlspecialchars($created_user['pseudo']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($created_user['email']); ?></p>
                <p><strong>Inscription:</strong> <?php echo htmlspecialchars($created_user['date_inscription']); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>