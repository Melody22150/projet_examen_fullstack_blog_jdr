<?php
/**
 * =============================================
 * LISTE DES COMMENTAIRES - Interface CRUD
 * =============================================
 * Description : Affiche tous les commentaires ou ceux d'un article spécifique
 * Auteur : Mélody
 * Date : Janvier 2026
 * Fonctionnalités :
 * - Liste des commentaires avec détails (auteur, article, note, date)
 * - Filtrage optionnel par article via paramètre GET
 * - Action : Supprimer un commentaire
 * - Bouton pour ajouter un commentaire
 */

// Activation de l'affichage des erreurs pour le développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclusion de la connexion à la base de données
require_once __DIR__ . '/../database.php';

// Récupération de l'ID de l'article pour filtrage (optionnel)
$article_id = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;

// Variables pour gérer les messages et les données
$message = '';
$message_type = '';
$comments = [];

try {
    $sql = "SELECT c.commentaire_id, c.contenu_commentaire, c.date_commentaire, c.note,
                   a.article_id, a.titre,
                   u.auteur_id, u.pseudo
            FROM commentaire c
            INNER JOIN article a ON c.article_id = a.article_id
            INNER JOIN utilisateur u ON c.auteur_id = u.auteur_id
            " . ($article_id > 0 ? "WHERE c.article_id = :aid" : "") . "
            ORDER BY c.date_commentaire DESC";

    $stmt = $pdo->prepare($sql);
    if ($article_id > 0) { $stmt->bindValue(':aid', $article_id, PDO::PARAM_INT); }
    $stmt->execute();
    $comments = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = '❌ Erreur base: ' . htmlspecialchars($e->getMessage());
    $message_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style_crud.css">
    <title>Commentaires</title>
</head>
<body>
<div class="container">
    <h1 class="page-title">💬 Commentaires <?php if ($article_id>0) echo '— Article #' . $article_id; ?></h1>
    
    <div class="nav">
        <a href="../index.php">Accueil</a>
        <a href="liste_articles.php">Articles</a>
        <a href="liste_utilisateurs.php">Utilisateurs</a>
        <a href="liste_commentaires.php">Commentaires</a>
    </div>
    
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="crud-actions">
        <a href="creer_commentaire.php<?php echo $article_id>0 ? ('?article_id='.$article_id) : '' ; ?>" class="btn-action btn-commenter">➕ Ajouter un commentaire</a>
        <a href="liste_articles.php" class="btn-action btn-retour">↩️ Retour aux articles</a>
    </div>

    <p class="items-count"><strong><?php echo count($comments); ?></strong> commentaire(s) trouvé(s)</p>

    <?php foreach ($comments as $c): ?>
        <div class="comment">
            <p><strong>Article:</strong> <?php echo htmlspecialchars($c['titre']); ?> (ID: <?php echo (int)$c['article_id']; ?>)</p>
            <p><strong>Auteur:</strong> <?php echo htmlspecialchars($c['pseudo']); ?> — <span class="meta">Note: <?php echo str_repeat('⭐', (int)$c['note']); ?> (<?php echo (int)$c['note']; ?>/5), le <?php echo htmlspecialchars($c['date_commentaire']); ?></span></p>
            <p><?php echo nl2br(htmlspecialchars($c['contenu_commentaire'])); ?></p>
            <p class="actions">
                <a class="del" href="delete_commentaire.php?id=<?php echo (int)$c['commentaire_id']; ?>">🗑️ Supprimer</a>
            </p>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
