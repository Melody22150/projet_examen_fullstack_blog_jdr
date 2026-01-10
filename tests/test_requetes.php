<?php
/**
 * Exemples de requêtes préparées sécurisées
 */

require_once __DIR__ . '/../database.php';

echo "=== TEST DES REQUÊTES PRÉPARÉES ===\n\n";

// Test 1 : Requête simple sans paramètres
echo "📚 Test 1 : Récupérer tous les articles\n";
$sql = "SELECT article_id, titre, categorie FROM article ORDER BY date_publication DESC";
$stmt = $pdo->query($sql);
$articles = $stmt->fetchAll();

echo "Nombre d'articles trouvés : " . count($articles) . "\n";
foreach ($articles as $article) {
    echo "  - Article #" . $article['article_id'] . " : " . $article['titre'] . "\n";
}
echo "\n";

// Test 2 : Requête préparée avec paramètre (sécurisée)
echo "📄 Test 2 : Récupérer un article spécifique (ID = 1)\n";
$article_id = 1;

$sql = "SELECT article_id, titre, categorie FROM article WHERE article_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $article_id]);
$article = $stmt->fetch();

if ($article) {
    echo "✅ Article trouvé : " . $article['titre'] . "\n";
    echo "   Catégorie : " . $article['categorie'] . "\n";
} else {
    echo "❌ Article introuvable\n";
}
echo "\n";

// Test 3 : Requête avec jointure (article + auteur)
echo "🔗 Test 3 : Récupérer un article avec son auteur\n";
$sql = "SELECT a.article_id, a.titre, u.pseudo AS auteur_pseudo
        FROM article a
        INNER JOIN utilisateur u ON a.auteur_id = u.auteur_id
        WHERE a.article_id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => 1]);
$article = $stmt->fetch();

if ($article) {
    echo "✅ Article : " . $article['titre'] . "\n";
    echo "   Auteur : " . $article['auteur_pseudo'] . "\n";
} else {
    echo "❌ Article introuvable\n";
}

echo "\n=== FIN DES TESTS ===\n";
?>