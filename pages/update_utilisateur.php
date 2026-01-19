<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../includes/functions.php';

// ========================================
// FORMULAIRE DE MODIFICATION D'UTILISATEUR
// ========================================

$auteur_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($auteur_id <= 0) { die('❌ ID utilisateur manquant'); }

$message = '';
$message_type = '';
$user = null;

try {
    $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE auteur_id = :id');
    $stmt->execute([':id' => $auteur_id]);
    $user = $stmt->fetch();
    if (!$user) { die('❌ utilisateur introuvable'); }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pseudo = trim($_POST['pseudo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nouveau_mdp = trim($_POST['nouveau_mdp'] ?? '');

        try {
            // Utilisation de la fonction du fichier functions.php
            $up = modifierUtilisateur($pdo, $auteur_id, $pseudo, $email, $nouveau_mdp !== '' ? $nouveau_mdp : null);
            
            if ($up) {
                $message = '✅ utilisateur modifié avec succès';
                $message_type = 'success';
                // Rafraîchir les données de l'utilisateur
                $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE auteur_id = :id');
                $stmt->execute([':id' => $auteur_id]);
                $user = $stmt->fetch();
            }
        } catch (Exception $e) {
            $message = '❌ ' . htmlspecialchars($e->getMessage());
            $message_type = 'error';
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
    <title>Modifier un utilisateur</title>
</head>
<body>
<div class="container">
    <h1>✏️ Modifier un utilisateur</h1>

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
            <input type="text" id="pseudo" name="pseudo" value="<?php echo htmlspecialchars($user['pseudo']); ?>" required maxlength="50">
        </div>
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="nouveau_mdp">Nouveau mot de passe (optionnel)</label>
            <input type="text" id="nouveau_mdp" name="nouveau_mdp" placeholder="Laisser vide pour conserver l'actuel">
        </div>
        <div class="actions">
            <button type="submit">💾 Enregistrer</button>
            <a class="btn" href="liste_utilisateurs.php">↩️ Retour</a>
        </div>
    </form>
</div>
</body>
</html>
