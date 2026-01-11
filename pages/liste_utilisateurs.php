<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../database.php';

$message = '';
$message_type = '';
$users = [];

try {
    $sql = "SELECT auteur_id, pseudo, email, date_inscription FROM utilisateur ORDER BY date_inscription DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "❌ Erreur base: " . htmlspecialchars($e->getMessage());
    $message_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style_crud.css">
    <title>Liste des utilisateurs</title>
</head>
<body>
<div class="container">
    <h1>👥 Utilisateurs</h1>
    
    <div class="nav">
        <a href="../index.php">Accueil</a>
        <a href="liste_articles.php">Articles</a>
        <a href="liste_utilisateurs.php">Utilisateurs</a>
        <a href="liste_commentaires.php">Commentaires</a>
    </div>
    
    <div class="create-article-container">
        <a href="creer_utilisateur.php" class="btn-create-article">➕ Créer un utilisateur</a>
    </div>
    
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <p><strong><?php echo count($users); ?></strong> utilisateur(s) trouvé(s)</p>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Pseudo</th>
            <th>Email</th>
            <th>Inscription</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo (int)$u['auteur_id']; ?></td>
                <td><?php echo htmlspecialchars($u['pseudo']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['date_inscription']); ?></td>
                <td class="actions">
                    <a class="edit" href="update_utilisateur.php?id=<?php echo (int)$u['auteur_id']; ?>">✏️ Modifier</a>
                    <a class="del" href="delete_utilisateur.php?id=<?php echo (int)$u['auteur_id']; ?>">🗑️ Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
