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
    <title>Liste des utilisateurs</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4e8d8; }
        .container { max-width: 900px; margin: 40px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,.08); }
        h1 { color:#8B4513; }
        .message { padding:12px; border-radius:6px; margin-bottom:16px; }
        .message.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .message.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #ddd; padding:10px; }
        th { background:#8B4513; color:#fff; }
        .actions a { margin-right:10px; font-weight:bold; text-decoration:none; }
        .edit { color:#8B4513; }
        .del { color:#c33; }
    </style>
</head>
<body>
<div class="container">
    <h1>👥 Utilisateurs</h1>
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
