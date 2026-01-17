<?php
/**
 * =============================================
 * LISTE DES UTILISATEURS - Interface CRUD
 * =============================================
 * Description : Affiche tous les utilisateurs inscrits
 * Auteur : Mélody
 * Date : Janvier 2026
 * Fonctionnalités :
 * - Liste complète des utilisateurs avec leurs informations
 * - Actions : Modifier, Supprimer
 * - Bouton de création d'utilisateur
 */

// Activation de l'affichage des erreurs pour le développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclusion de la connexion à la base de données
require_once __DIR__ . '/../database.php';

// Variables pour gérer les messages et les données
$message = '';
$message_type = '';
$users = [];

try {
    // Requête pour récupérer tous les utilisateurs
    // Tri par date d'inscription décroissante (les plus récents en premier)
    $sql = "SELECT auteur_id, pseudo, email, date_inscription FROM utilisateur ORDER BY date_inscription DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    // Gestion des erreurs de base de données
    $message = "❌ Erreur base: " . htmlspecialchars($e->getMessage());
    $message_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées et encodage -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Feuille de style commune aux pages CRUD -->
    <link rel="stylesheet" href="../assets/css/style_crud.css">
    <title>Liste des utilisateurs</title>
</head>
<body>
<div class="container">
    <!-- Titre principal -->
    <h1>👥 Utilisateurs</h1>
    
    <!-- Navigation entre les sections CRUD -->
    <div class="nav">
        <a href="../index.php">Accueil</a>
        <a href="liste_articles.php">Articles</a>
        <a href="liste_utilisateurs.php">Utilisateurs</a>
        <a href="liste_commentaires.php">Commentaires</a>
    </div>
    
    <!-- Bouton pour créer un nouvel utilisateur -->
    <div class="create-article-container">
        <a href="creer_utilisateur.php" class="btn-create-article">➕ Créer un utilisateur</a>
    </div>
    
    <!-- Affichage des messages (succès ou erreur) -->
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Compteur d'utilisateurs -->
    <p><strong><?php echo count($users); ?></strong> utilisateur(s) trouvé(s)</p>

    <!-- Tableau des utilisateurs -->
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
                <!-- Affichage sécurisé des données utilisateur -->
                <td><?php echo (int)$u['auteur_id']; ?></td>
                <td><?php echo htmlspecialchars($u['pseudo']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['date_inscription']); ?></td>
                <!-- Boutons d'action -->
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
