<?php
/**
 * Test de connexion à la base de données
 */

require_once __DIR__ . '/../database.php';

try {
    echo "✅ Connexion réussie à la base de données !\n\n";
    
    // Test : Afficher la base de données active
    $stmt = $pdo->query("SELECT DATABASE() AS db_name");
    $result = $stmt->fetch();
    echo "Base de données active : " . $result['db_name'] . "\n";
    
    // Test : Afficher la version MySQL
    $stmt = $pdo->query("SELECT VERSION() AS version");
    $result = $stmt->fetch();
    echo "Version MySQL : " . $result['version'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>