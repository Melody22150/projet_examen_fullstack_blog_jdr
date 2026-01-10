<?php
/**
 * Test du hashage et de la vérification des mots de passe
 */

echo "=== TEST HASHAGE MOT DE PASSE ===\n\n";

// Test 1 : Hashage d'un mot de passe
echo "🔐 Test 1 : Hashage d'un mot de passe\n";
$mot_de_passe = "MonMotDePasseSecret123";
$hash = password_hash($mot_de_passe, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Mot de passe original : $mot_de_passe\n";
echo "Hash bcrypt (coût 12) : $hash\n";
echo "Longueur du hash : " . strlen($hash) . " caractères\n\n";

// Test 2 : Même mot de passe = hashs différents (grâce au sel aléatoire)
echo "🔐 Test 2 : Unicité du sel aléatoire\n";
$hash1 = password_hash($mot_de_passe, PASSWORD_BCRYPT, ['cost' => 12]);
$hash2 = password_hash($mot_de_passe, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Hash 1 : $hash1\n";
echo "Hash 2 : $hash2\n";
echo "Les hashs sont différents : " . ($hash1 !== $hash2 ? "✅ OUI" : "❌ NON") . "\n\n";

// Test 3 : Vérification avec le BON mot de passe
echo "🔓 Test 3 : Vérification avec le BON mot de passe\n";
$mot_de_passe_saisi = "MonMotDePasseSecret123";
$hash_stocke = $hash;

if (password_verify($mot_de_passe_saisi, $hash_stocke)) {
    echo "✅ Mot de passe correct ! Connexion autorisée.\n\n";
} else {
    echo "❌ Mot de passe incorrect.\n\n";
}

// Test 4 : Vérification avec un MAUVAIS mot de passe
echo "🔓 Test 4 : Vérification avec un MAUVAIS mot de passe\n";
$mauvais_mot_de_passe = "MauvaisMotDePasse";

if (password_verify($mauvais_mot_de_passe, $hash_stocke)) {
    echo "❌ ERREUR : Le mot de passe incorrect a été accepté !\n\n";
} else {
    echo "✅ Mot de passe incorrect refusé. Accès refusé.\n\n";
}

// Test 5 : Temps de hashage
echo "⏱️ Test 5 : Temps de hashage (coût 12)\n";
$debut = microtime(true);
password_hash("TestPerformance", PASSWORD_BCRYPT, ['cost' => 12]);
$fin = microtime(true);
$duree = round(($fin - $debut) * 1000, 2);

echo "Durée : $duree ms (~300ms attendu)\n";
echo "Ce délai rend les attaques par force brute impraticables.\n";

echo "\n=== FIN DES TESTS ===\n";
?>