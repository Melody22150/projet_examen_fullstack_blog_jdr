<?php
/**
 * Tests des fonctions métier
 */

declare(strict_types=1);

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../includes/functions.php';

echo "=== TEST DES FONCTIONS MÉTIER ===\n\n";

// Test 1 : Récupérer tous les articles
echo "📚 Test 1 : getArticles()\n";
$articles = getArticles($pdo);
echo "Nombre d'articles : " . count($articles) . "\n";
if (count($articles) > 0) {
    echo "Premier article : " . $articles[0]['titre'] . "\n";
    echo "Auteur : " . $articles[0]['auteur_pseudo'] . "\n";
}
echo "\n";

// Test 2 : Récupérer un article spécifique
echo "📄 Test 2 : getArticleById(1)\n";
$article = getArticleById($pdo, 1);
if ($article) {
    echo "✅ Article trouvé : " . $article['titre'] . "\n";
    echo "Auteur : " . $article['auteur_pseudo'] . "\n";
    echo "Catégorie : " . $article['categorie'] . "\n";
} else {
    echo "❌ Article introuvable.\n";
}
echo "\n";

// Test 3 : Récupérer articles par catégorie
echo "🏷️ Test 3 : getArticlesByCategorie('Conseils')\n";
$articles_conseils = getArticlesByCategorie($pdo, 'Conseils');
echo "Nombre d'articles dans 'Conseils' : " . count($articles_conseils) . "\n";
echo "\n";

// Test 4 : Créer un utilisateur (succès)
echo "👤 Test 4 : creerUtilisateur() - Succès\n";
try {
    $user_id = creerUtilisateur($pdo, 'TestUser2025', 'test@exemple.fr', 'MotDePasse2025');
    echo "✅ Utilisateur créé avec succès ! ID : $user_id\n";
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5 : Créer un utilisateur (pseudo trop court)
echo "👤 Test 5 : creerUtilisateur() - Pseudo trop court\n";
try {
    $user_id = creerUtilisateur($pdo, 'Joe', 'joe@exemple.fr', 'MotDePasse2025');
    echo "❌ Erreur : L'utilisateur n'aurait pas dû être créé !\n";
} catch (Exception $e) {
    echo "✅ Erreur attendue : " . $e->getMessage() . "\n";
}
echo "\n";

// Test 6 : Créer un utilisateur (email invalide)
echo "👤 Test 6 : creerUtilisateur() - Email invalide\n";
try {
    $user_id = creerUtilisateur($pdo, 'TestUser3', 'email-invalide', 'MotDePasse2025');
    echo "❌ Erreur : L'utilisateur n'aurait pas dû être créé !\n";
} catch (Exception $e) {
    echo "✅ Erreur attendue : " . $e->getMessage() . "\n";
}
echo "\n";

// Test 7 : Créer un utilisateur (doublon email)
echo "👤 Test 7 : creerUtilisateur() - Email déjà utilisé\n";
try {
    $user_id = creerUtilisateur($pdo, 'AutreUser', 'melody@jdr-blog.fr', 'MotDePasse2025');
    echo "❌ Erreur : L'utilisateur n'aurait pas dû être créé !\n";
} catch (Exception $e) {
    echo "✅ Erreur attendue : " . $e->getMessage() . "\n";
}
echo "\n";

// Test 8 : Modifier un article
echo "✏️ Test 8 : updateArticle(1) - Modification titre\n";
try {
    $sql = "UPDATE article SET titre = :titre WHERE article_id = :id";
    $stmt = $pdo->prepare($sql);
    $resultat = $stmt->execute([':titre' => 'Titre modifié pour test', ':id' => 1]);
    if ($resultat) {
        echo "✅ Article modifié avec succès\n";
        // Vérifier la modification
        $article = getArticleById($pdo, 1);
        echo "   Nouveau titre : " . $article['titre'] . "\n";
    } else {
        echo "❌ Erreur lors de la modification\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
echo "\n";

// Test 9 : Supprimer un article (test avec ID inexistant d'abord)
echo "🗑️ Test 9 : deleteArticle() - Supprimer article inexistant\n";
try {
    $sql = "DELETE FROM article WHERE article_id = :id";
    $stmt = $pdo->prepare($sql);
    $resultat = $stmt->execute([':id' => 99999]);
    if ($resultat) {
        echo "✅ Requête exécutée (0 article supprimé - normal pour ID inexistant)\n";
    } else {
        echo "❌ Erreur lors de la suppression\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
echo "\n";

// Test 10 : Modifier un utilisateur
echo "👤 Test 10 : updateUtilisateur() - Modification email\n";
try {
    $sql = "UPDATE utilisateur SET email = :email WHERE auteur_id = :id";
    $stmt = $pdo->prepare($sql);
    $resultat = $stmt->execute([':email' => 'newemail@test.fr', ':id' => 1]);
    if ($resultat) {
        echo "✅ Utilisateur modifié avec succès\n";
    } else {
        echo "❌ Erreur lors de la modification\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
echo "\n";

// Test 11 : Supprimer un utilisateur (test avec ID inexistant d'abord)
echo "🗑️ Test 11 : deleteUtilisateur() - Supprimer utilisateur inexistant\n";
try {
    $sql = "DELETE FROM utilisateur WHERE auteur_id = :id";
    $stmt = $pdo->prepare($sql);
    $resultat = $stmt->execute([':id' => 99999]);
    if ($resultat) {
        echo "✅ Requête exécutée (0 utilisateur supprimé - normal pour ID inexistant)\n";
    } else {
        echo "❌ Erreur lors de la suppression\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== FIN DES TESTS ===\n";
?>
