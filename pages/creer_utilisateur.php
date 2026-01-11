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
            $message = "✅ utilisateur créé avec succès !";
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
    <link rel="stylesheet" href="../assets/css/style_crud.css">
    <title>Créer un utilisateur</title>
    </head>
<body>
    <div class="container">
        <h1>👤 Créer un utilisateur</h1>
        
        <div class="nav">
            <a href="../index.php">Accueil</a>
            <a href="liste_articles.php">Articles</a>
            <a href="liste_utilisateurs.php">Utilisateurs</a>
            <a href="liste_commentaires.php">Commentaires</a>
        </div>

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
                <a class="btn-cancel" href="liste_utilisateurs.php">❌ Annuler</a>
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