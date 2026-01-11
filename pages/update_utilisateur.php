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
    $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE auteur_id = :id');
    $stmt->execute([':id' => $auteur_id]);
    $user = $stmt->fetch();
    if (!$user) { die('❌ utilisateur introuvable'); }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pseudo = trim($_POST['pseudo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nouveau_mdp = trim($_POST['nouveau_mdp'] ?? '');

        try {
            if ($pseudo === '' || !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $pseudo)) {
                throw new Exception('Le pseudo doit contenir entre 3 et 50 caractères alphanumériques.');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("L'email n'est pas valide.");
            }

            $mdp_hash = $user['mot_de_passe'];
            if ($nouveau_mdp !== '') {
                if (strlen($nouveau_mdp) < 8) {
                    throw new Exception('Le mot de passe doit contenir au moins 8 caractères.');
                }
                $mdp_hash = password_hash($nouveau_mdp, PASSWORD_BCRYPT);
            }

            $sql = 'UPDATE utilisateur SET pseudo = :pseudo, email = :email, mot_de_passe = :mdp WHERE auteur_id = :id';
            $up = $pdo->prepare($sql)->execute([
                ':pseudo' => $pseudo,
                ':email' => $email,
                ':mdp' => $mdp_hash,
                ':id' => $auteur_id,
            ]);

            if ($up) {
                $message = '✅ utilisateur modifié avec succès';
                $message_type = 'success';
                $user['pseudo'] = $pseudo;
                $user['email'] = $email;
                $user['mot_de_passe'] = $mdp_hash;
            } else {
                throw new Exception('La modification a échoué');
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
