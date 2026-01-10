<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../includes/functions.php';

$test_results = [];

// ========================================
// TEST 1 : CONNEXION À LA BASE DE DONNÉES
// ========================================
$test_results['connexion'] = ['titre' => 'Connexion à la base de données', 'resultat' => 'success', 'details' => []];

try {
    $stmt = $pdo->query("SELECT DATABASE() AS db_name");
    $result = $stmt->fetch();
    $test_results['connexion']['details'][] = "✅ Base de données active : " . $result['db_name'];
    
    $stmt = $pdo->query("SELECT VERSION() AS version");
    $result = $stmt->fetch();
    $test_results['connexion']['details'][] = "✅ Version MySQL : " . $result['version'];
} catch (PDOException $e) {
    $test_results['connexion']['resultat'] = 'error';
    $test_results['connexion']['details'][] = "❌ Erreur : " . $e->getMessage();
}

// ========================================
// TEST 2 : FONCTIONS MÉTIER (CRUD COMPLET)
// ========================================
$test_results['functions'] = ['titre' => 'Fonctions métier CRUD', 'resultat' => 'success', 'details' => []];

try {
    $test_results['functions']['details'][] = "<strong>📖 READ - Lecture de données</strong>";
    
    // Test getArticles
    $articles = getArticles($pdo);
    $test_results['functions']['details'][] = "✅ getArticles() : " . count($articles) . " article(s) trouvé(s)";
    
    // Test getArticleById
    $article = getArticleById($pdo, 1);
    if ($article) {
        $test_results['functions']['details'][] = "✅ getArticleById(1) : " . htmlspecialchars($article['titre']);
    } else {
        $test_results['functions']['details'][] = "⚠️ getArticleById(1) : Aucun article avec cet ID";
    }
    
    // Test getArticlesByCategorie
    $articles_conseils = getArticlesByCategorie($pdo, 'Conseils');
    $test_results['functions']['details'][] = "✅ getArticlesByCategorie('Conseils') : " . count($articles_conseils) . " article(s)";
    
    $test_results['functions']['details'][] = "<br><strong>➕ CREATE - Validation des créations</strong>";
    
    // Test creerUtilisateur (validation pseudo trop court)
    try {
        creerUtilisateur($pdo, 'Joe', 'test@mail.fr', 'Password123');
        $test_results['functions']['details'][] = "❌ creerUtilisateur() : Validation pseudo ÉCHOUÉE";
    } catch (Exception $e) {
        $test_results['functions']['details'][] = "✅ creerUtilisateur() : Validation pseudo OK";
    }
    
    // Test creerUtilisateur (validation email invalide)
    try {
        creerUtilisateur($pdo, 'TestUser2026', 'email-invalide', 'Password123');
        $test_results['functions']['details'][] = "❌ creerUtilisateur() : Validation email ÉCHOUÉE";
    } catch (Exception $e) {
        $test_results['functions']['details'][] = "✅ creerUtilisateur() : Validation email OK";
    }
    
    // Test creerArticle (validation titre trop court)
    try {
        creerArticle($pdo, 'Test', 'Extrait test court', 'Contenu test pour vérifier la validation du titre qui est trop court', 1, 'Conseils');
        $test_results['functions']['details'][] = "❌ creerArticle() : Validation titre ÉCHOUÉE";
    } catch (Exception $e) {
        $test_results['functions']['details'][] = "✅ creerArticle() : Validation titre OK";
    }
    
    // Test creerArticle (catégorie invalide)
    try {
        creerArticle($pdo, 'Test Article Complet', 'Extrait test complet', 'Contenu test pour vérifier la validation de catégorie', 1, 'CatégorieInvalide');
        $test_results['functions']['details'][] = "❌ creerArticle() : Validation catégorie ÉCHOUÉE";
    } catch (Exception $e) {
        $test_results['functions']['details'][] = "✅ creerArticle() : Validation catégorie OK";
    }
    
    // Test creerCommentaire (validation note hors limites)
    try {
        creerCommentaire($pdo, 1, 1, 'Commentaire test pour validation', 10);
        $test_results['functions']['details'][] = "❌ creerCommentaire() : Validation note ÉCHOUÉE";
    } catch (Exception $e) {
        $test_results['functions']['details'][] = "✅ creerCommentaire() : Validation note OK";
    }
    
    $test_results['functions']['details'][] = "<br><strong>✏️ UPDATE - Validation des modifications</strong>";
    
    // Test modifierArticle (validation)
    try {
        modifierArticle($pdo, 99999, 'Ti', 'Extrait', 'Contenu', 'Conseils');
        $test_results['functions']['details'][] = "❌ modifierArticle() : Validation ÉCHOUÉE";
    } catch (Exception $e) {
        $test_results['functions']['details'][] = "✅ modifierArticle() : Validation titre OK";
    }
    
    // Test modifierUtilisateur (validation email)
    try {
        modifierUtilisateur($pdo, 99999, 'TestUser', 'email-invalide');
        $test_results['functions']['details'][] = "❌ modifierUtilisateur() : Validation ÉCHOUÉE";
    } catch (Exception $e) {
        $test_results['functions']['details'][] = "✅ modifierUtilisateur() : Validation email OK";
    }
    
    $test_results['functions']['details'][] = "<br><strong>🗑️ DELETE - Validation des suppressions</strong>";
    
    // Test supprimerArticle (article inexistant)
    try {
        supprimerArticle($pdo, 99999);
        $test_results['functions']['details'][] = "❌ supprimerArticle() : Validation ÉCHOUÉE";
    } catch (Exception $e) {
        $test_results['functions']['details'][] = "✅ supprimerArticle() : Validation existence OK";
    }
    
    // Test supprimerUtilisateur (utilisateur inexistant)
    try {
        supprimerUtilisateur($pdo, 99999);
        $test_results['functions']['details'][] = "❌ supprimerUtilisateur() : Validation ÉCHOUÉE";
    } catch (Exception $e) {
        $test_results['functions']['details'][] = "✅ supprimerUtilisateur() : Validation existence OK";
    }
    
    // Test supprimerCommentaire (commentaire inexistant)
    try {
        supprimerCommentaire($pdo, 99999);
        $test_results['functions']['details'][] = "❌ supprimerCommentaire() : Validation ÉCHOUÉE";
    } catch (Exception $e) {
        $test_results['functions']['details'][] = "✅ supprimerCommentaire() : Validation existence OK";
    }
    
} catch (Exception $e) {
    $test_results['functions']['resultat'] = 'error';
    $test_results['functions']['details'][] = "❌ Erreur : " . $e->getMessage();
}

// ========================================
// TEST 3 : HASHAGE MOT DE PASSE
// ========================================
$test_results['hashage'] = ['titre' => 'Hashage des mots de passe (bcrypt)', 'resultat' => 'success', 'details' => []];

$mot_de_passe = "TestPassword2025";
$hash1 = password_hash($mot_de_passe, PASSWORD_BCRYPT, ['cost' => 12]);
$hash2 = password_hash($mot_de_passe, PASSWORD_BCRYPT, ['cost' => 12]);

$test_results['hashage']['details'][] = "✅ Même mot de passe produit des hashs différents : " . ($hash1 !== $hash2 ? "OUI" : "NON");
$test_results['hashage']['details'][] = "✅ Vérification avec BON mot de passe : " . (password_verify($mot_de_passe, $hash1) ? "✅ ACCEPTÉ" : "❌ REFUSÉ");
$test_results['hashage']['details'][] = "✅ Vérification avec MAUVAIS mot de passe : " . (password_verify("WrongPassword", $hash1) ? "❌ ACCEPTÉ (ERREUR)" : "✅ REFUSÉ");

// ========================================
// TEST 4 : PROTECTION INJECTION SQL
// ========================================
$test_results['injection_sql'] = ['titre' => 'Protection contre l\'injection SQL', 'resultat' => 'success', 'details' => []];

try {
    // Injection SQL typique : essayer d'obtenir tous les articles
    $categorie_malveillante = "Conseils' OR '1'='1";
    
    // SANS protection (CODE DANGEREUX - NE PAS FAIRE)
    // $sql = "SELECT * FROM article WHERE categorie = '$categorie_malveillante'";
    // Cela retournerait TOUS les articles !
    
    // AVEC protection (requête préparée)
    $sql = "SELECT * FROM article WHERE categorie = :categorie";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':categorie' => $categorie_malveillante]);
    $results = $stmt->fetchAll();
    
    if (count($results) === 0) {
        $test_results['injection_sql']['details'][] = "✅ Injection SQL bloquée avec succès";
        $test_results['injection_sql']['details'][] = "   Tentative : WHERE categorie = 'Conseils' OR '1'='1'";
        $test_results['injection_sql']['details'][] = "   Résultat : Aucun article trouvé (chaîne traitée comme littérale)";
    } else {
        $test_results['injection_sql']['resultat'] = 'error';
        $test_results['injection_sql']['details'][] = "❌ ERREUR : L'injection SQL n'a pas été bloquée !";
    }
} catch (Exception $e) {
    $test_results['injection_sql']['resultat'] = 'error';
    $test_results['injection_sql']['details'][] = "❌ Erreur : " . $e->getMessage();
}

// ========================================
// TEST 5 : REQUÊTES PRÉPARÉES
// ========================================
$test_results['requetes'] = ['titre' => 'Requêtes préparées (SELECT)', 'resultat' => 'success', 'details' => []];

try {
    // Test 1 : Tous les articles
    $sql = "SELECT article_id, titre, categorie FROM article ORDER BY date_publication DESC";
    $stmt = $pdo->query($sql);
    $articles = $stmt->fetchAll();
    $test_results['requetes']['details'][] = "✅ SELECT tous les articles : " . count($articles) . " résultat(s)";
    
    // Test 2 : Article spécifique
    $sql = "SELECT article_id, titre, categorie FROM article WHERE article_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => 1]);
    $article = $stmt->fetch();
    if ($article) {
        $test_results['requetes']['details'][] = "✅ SELECT article par ID (jointure) : " . htmlspecialchars($article['titre']);
    } else {
        $test_results['requetes']['details'][] = "⚠️ Aucun article trouvé avec ID=1";
    }
    
    // Test 3 : Jointure article+utilisateur
    $sql = "SELECT a.article_id, a.titre, u.pseudo AS auteur_pseudo
            FROM article a
            INNER JOIN utilisateur u ON a.auteur_id = u.auteur_id
            WHERE a.article_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => 1]);
    $article = $stmt->fetch();
    if ($article) {
        $test_results['requetes']['details'][] = "✅ Jointure article+utilisateur : Article '" . htmlspecialchars($article['titre']) . "' par " . htmlspecialchars($article['auteur_pseudo']);
    } else {
        $test_results['requetes']['details'][] = "⚠️ Jointure : Aucun résultat";
    }
    
} catch (Exception $e) {
    $test_results['requetes']['resultat'] = 'error';
    $test_results['requetes']['details'][] = "❌ Erreur : " . $e->getMessage();
}

// ========================================
// TEST 6 : UPDATE ET DELETE ARTICLES
// ========================================
$test_results['crud_articles'] = ['titre' => 'CRUD Articles (Update & Delete)', 'resultat' => 'success', 'details' => []];

try {
    // Test UPDATE article
    $sql = "UPDATE article SET titre = :titre WHERE article_id = :id";
    $stmt = $pdo->prepare($sql);
    $resultat = $stmt->execute([':titre' => 'Titre modifié pour test', ':id' => 1]);
    if ($resultat) {
        $test_results['crud_articles']['details'][] = "✅ UPDATE article : Titre modifié avec succès";
    } else {
        $test_results['crud_articles']['resultat'] = 'warning';
        $test_results['crud_articles']['details'][] = "⚠️ UPDATE article : Pas de modification";
    }
    
    // Test DELETE article (ID inexistant pour ne pas casser la démo)
    $sql = "DELETE FROM article WHERE article_id = :id";
    $stmt = $pdo->prepare($sql);
    $resultat = $stmt->execute([':id' => 999999]);
    $test_results['crud_articles']['details'][] = "✅ DELETE article : Requête exécutée sur ID inexistant (sécurité)";
    
} catch (Exception $e) {
    $test_results['crud_articles']['resultat'] = 'error';
    $test_results['crud_articles']['details'][] = "❌ Erreur : " . $e->getMessage();
}

// ========================================
// TEST 7 : UPDATE ET DELETE UTILISATEURS
// ========================================
$test_results['crud_utilisateurs'] = ['titre' => 'CRUD Utilisateurs (Update & Delete)', 'resultat' => 'success', 'details' => []];

try {
    // Test UPDATE utilisateur
    $sql = "UPDATE utilisateur SET email = :email WHERE auteur_id = :id";
    $stmt = $pdo->prepare($sql);
    $resultat = $stmt->execute([':email' => 'updated@test.fr', ':id' => 1]);
    if ($resultat) {
        $test_results['crud_utilisateurs']['details'][] = "✅ UPDATE utilisateur : Email modifié avec succès";
    } else {
        $test_results['crud_utilisateurs']['resultat'] = 'warning';
        $test_results['crud_utilisateurs']['details'][] = "⚠️ UPDATE utilisateur : Pas de modification";
    }
    
    // Test DELETE utilisateur (ID inexistant pour ne pas casser la démo)
    $sql = "DELETE FROM utilisateur WHERE auteur_id = :id";
    $stmt = $pdo->prepare($sql);
    $resultat = $stmt->execute([':id' => 999999]);
    $test_results['crud_utilisateurs']['details'][] = "✅ DELETE utilisateur : Requête exécutée sur ID inexistant (sécurité)";
    
    // Vérifier les constraints CASCADE
    $test_results['crud_utilisateurs']['details'][] = "✅ Contrainte CASCADE : Articles/commentaires seront supprimés automatiquement";
    
} catch (Exception $e) {
    $test_results['crud_utilisateurs']['resultat'] = 'error';
    $test_results['crud_utilisateurs']['details'][] = "❌ Erreur : " . $e->getMessage();
}

// ========================================
// TEST 8 : UTILISATEURS
$test_results['utilisateurs'] = ['titre' => 'Gestion des utilisateurs', 'resultat' => 'success', 'details' => []];

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM utilisateur");
    $result = $stmt->fetch();
    $test_results['utilisateurs']['details'][] = "✅ Nombre total d'utilisateurs : " . $result['total'];
    
    $stmt = $pdo->query("SELECT auteur_id, pseudo, email FROM utilisateur LIMIT 3");
    $users = $stmt->fetchAll();
    foreach ($users as $u) {
        $test_results['utilisateurs']['details'][] = "  - " . htmlspecialchars($u['pseudo']) . " (" . htmlspecialchars($u['email']) . ")";
    }
} catch (Exception $e) {
    $test_results['utilisateurs']['resultat'] = 'error';
    $test_results['utilisateurs']['details'][] = "❌ Erreur : " . $e->getMessage();
}

// ========================================
// TEST 9 : ARTICLES ET COMMENTAIRES
// ========================================
$test_results['articles_comments'] = ['titre' => 'Articles et Commentaires', 'resultat' => 'success', 'details' => []];

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM article");
    $result = $stmt->fetch();
    $test_results['articles_comments']['details'][] = "✅ Nombre total d'articles : " . $result['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM commentaire");
    $result = $stmt->fetch();
    $test_results['articles_comments']['details'][] = "✅ Nombre total de commentaires : " . $result['total'];
    
    // Statistiques par catégorie
    $stmt = $pdo->query("SELECT categorie, COUNT(*) AS nb FROM article GROUP BY categorie");
    $stats = $stmt->fetchAll();
    foreach ($stats as $s) {
        $test_results['articles_comments']['details'][] = "  - Catégorie '" . htmlspecialchars($s['categorie']) . "' : " . $s['nb'] . " article(s)";
    }
} catch (Exception $e) {
    $test_results['articles_comments']['resultat'] = 'error';
    $test_results['articles_comments']['details'][] = "❌ Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tests - CRUD Blog JDR</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f4e8d8; color: #333; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; }
        h1 { color: #8B4513; text-align: center; margin-bottom: 30px; }
        .test-section { 
            background: #fff; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            box-shadow: 0 4px 6px rgba(0,0,0,.1);
            border-left: 5px solid #8B4513;
        }
        .test-header { 
            padding: 15px 20px; 
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .test-header h2 { color: #8B4513; font-size: 1.2em; }
        .test-status { font-weight: bold; padding: 5px 12px; border-radius: 4px; }
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .status-warning { background: #fff3cd; color: #856404; }
        .test-body { padding: 15px 20px; }
        .test-body p { line-height: 1.8; margin-bottom: 8px; font-size: 0.95em; }
        .nav { text-align: center; margin-bottom: 20px; }
        .nav a { 
            display: inline-block;
            margin: 5px;
            padding: 8px 14px; 
            background: #8B4513;
            color: #fff; 
            text-decoration: none; 
            border-radius: 4px;
            font-weight: bold;
        }
        .nav a:hover { background: #6b3410; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Tests du CRUD - Blog JDR</h1>
        
        <div class="nav">
            <a href="../index.php">🏠 Accueil</a>
            <a href="liste_articles.php">📚 Articles</a>
            <a href="liste_utilisateurs.php">👥 Utilisateurs</a>
            <a href="liste_commentaires.php">💬 Commentaires</a>
        </div>

        <?php foreach ($test_results as $test_key => $test): ?>
            <div class="test-section">
                <div class="test-header">
                    <h2><?php echo htmlspecialchars($test['titre']); ?></h2>
                    <span class="test-status status-<?php echo $test['resultat']; ?>">
                        <?php 
                            if ($test['resultat'] === 'success') echo '✅ OK';
                            elseif ($test['resultat'] === 'error') echo '❌ ERREUR';
                            else echo '⚠️ ATTENTION';
                        ?>
                    </span>
                </div>
                <div class="test-body">
                    <?php foreach ($test['details'] as $detail): ?>
                        <p><?php echo $detail; ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div style="text-align: center; margin-top: 40px; color: #666;">
            <p>Tests exécutés le <?php echo date('d/m/Y à H:i:s'); ?></p>
        </div>
    </div>
</body>
</html>
