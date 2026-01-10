<?php
/**
 * Test de protection contre l'injection SQL
 */

require_once __DIR__ . '/../database.php';

echo "=== TEST PROTECTION INJECTION SQL ===\n\n";

// Simulation d'une tentative d'injection SQL
echo "🔒 Test : Tentative d'injection SQL avec '1 OR 1=1'\n\n";

// Valeur malveillante qu'un pirate pourrait envoyer via l'URL
$id_malveillant = "1 OR 1=1";

echo "Valeur reçue (simulée) : " . $id_malveillant . "\n";
echo "URL piratée : article.php?id=" . $id_malveillant . "\n\n";

// ❌ Code DANGEREUX (pour démonstration uniquement - NE JAMAIS FAIRE ÇA)
echo "--- Sans protection (code dangereux) ---\n";
echo "Code vulnérable : SELECT * FROM article WHERE article_id = $id_malveillant\n";
echo "Résultat : Tous les articles seraient retournés ! 💀\n\n";

// ✅ Code SÉCURISÉ avec requête préparée
echo "--- Avec requête préparée (code sécurisé) ---\n";
$sql = "SELECT * FROM article WHERE article_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_malveillant]);
$article = $stmt->fetch();

if ($article) {
    echo "❌ ERREUR : Un article a été trouvé (ne devrait pas arriver)\n";
} else {
    echo "✅ SUCCÈS : Aucun article trouvé (la chaîne '1 OR 1=1' est traitée comme un ID littéral)\n";
    echo "L'injection SQL a été bloquée par PDO !\n";
}

echo "\n=== FIN DU TEST ===\n";
?>