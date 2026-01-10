<?php

declare(strict_types=1);  // Mode strict activé pour tout le fichier

/**
 * Récupère tous les articles avec leurs auteurs
 * @param PDO $pdo - Connexion à la base de données
 * @return array - Tableau d'articles triés par date décroissante
 */
function getArticles(PDO $pdo): array {
    $sql = "SELECT a.article_id, a.titre, a.extrait, a.categorie, 
                   a.image_url, a.date_publication, 
                   u.pseudo AS auteur_pseudo
            FROM article a
            INNER JOIN utilisateur u ON a.auteur_id = u.auteur_id
            ORDER BY a.date_publication DESC";
    
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Récupère un article par son ID avec les informations de l'auteur
 * @param PDO $pdo - Connexion à la base de données
 * @param int $article_id - ID de l'article à récupérer
 * @return array|false - Tableau de l'article ou false si introuvable
 */
function getArticleById(PDO $pdo, int $article_id) {
    $sql = "SELECT a.*, u.pseudo AS auteur_pseudo
            FROM article a
            INNER JOIN utilisateur u ON a.auteur_id = u.auteur_id
            WHERE a.article_id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $article_id]);
    return $stmt->fetch();
}

/**
 * Récupère les articles d'une catégorie spécifique
 * @param PDO $pdo - Connexion à la base de données
 * @param string $categorie - Catégorie à filtrer
 * @return array - Tableau d'articles de cette catégorie
 */
function getArticlesByCategorie(PDO $pdo, string $categorie): array {
    $sql = "SELECT a.article_id, a.titre, a.extrait, a.categorie, 
                   a.image_url, a.date_publication, 
                   u.pseudo AS auteur_pseudo
            FROM article a
            INNER JOIN utilisateur u ON a.auteur_id = u.auteur_id
            WHERE a.categorie = :categorie
            ORDER BY a.date_publication DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':categorie' => $categorie]);
    return $stmt->fetchAll();
}

/**
 * Crée un nouvel utilisateur dans la base de données
 * @param PDO $pdo - Connexion à la base de données
 * @param string $pseudo - Pseudonyme (5-50 caractères)
 * @param string $email - Adresse email valide
 * @param string $mot_de_passe - Mot de passe en clair (min 8 caractères)
 * @return int - ID de l'utilisateur créé
 * @throws Exception - En cas d'erreur de validation ou de doublon
 */
function creerUtilisateur(PDO $pdo, string $pseudo, string $email, string $mot_de_passe): int {
    
    // === VALIDATION DU PSEUDO ===
    if (strlen($pseudo) < 5 || strlen($pseudo) > 50) {
        throw new Exception("Le pseudo doit contenir entre 5 et 50 caractères.");
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $pseudo)) {
        throw new Exception("Le pseudo ne peut contenir que des lettres, chiffres et underscore.");
    }
    
    // === VALIDATION DE L'EMAIL ===
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("L'adresse email n'est pas valide.");
    }
    
    // === VALIDATION DU MOT DE PASSE ===
    if (strlen($mot_de_passe) < 8) {
        throw new Exception("Le mot de passe doit contenir au moins 8 caractères.");
    }
    
    // === VÉRIFICATION DOUBLON PSEUDO ===
    $sql = "SELECT COUNT(*) FROM Utilisateur WHERE pseudo = :pseudo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pseudo' => $pseudo]);
    
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ce pseudo est déjà utilisé.");
    }
    
    // === VÉRIFICATION DOUBLON EMAIL ===
    $sql = "SELECT COUNT(*) FROM Utilisateur WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Cette adresse email est déjà utilisée.");
    }
    
    // === HASHAGE SÉCURISÉ DU MOT DE PASSE ===
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT, ['cost' => 12]);
    
    // === INSERTION EN BASE DE DONNÉES ===
    $sql = "INSERT INTO Utilisateur (pseudo, email, mot_de_passe) 
            VALUES (:pseudo, :email, :mot_de_passe)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pseudo' => $pseudo,
        ':email' => $email,
        ':mot_de_passe' => $mot_de_passe_hash
    ]);
    
    // Retourne l'ID de l'utilisateur nouvellement créé
    return (int) $pdo->lastInsertId();
}

/**
 * Crée un nouvel article dans la base de données
 * @param PDO $pdo - Connexion à la base de données
 * @param string $titre - Titre de l'article
 * @param string $extrait - Extrait court
 * @param string $contenu - Contenu complet
 * @param int $auteur_id - ID de l'auteur
 * @param string $categorie - Catégorie (Conseils, Critiques, Actualités)
 * @param string|null $image_url - URL de l'image (optionnel)
 * @return int - ID de l'article créé
 * @throws Exception - En cas d'erreur de validation
 */
function creerArticle(PDO $pdo, string $titre, string $extrait, string $contenu, int $auteur_id, string $categorie, ?string $image_url = null): int {
    
    // Validation titre
    if (strlen($titre) < 5 || strlen($titre) > 200) {
        throw new Exception("Le titre doit contenir entre 5 et 200 caractères.");
    }
    
    // Validation extrait
    if (strlen($extrait) < 10 || strlen($extrait) > 500) {
        throw new Exception("L'extrait doit contenir entre 10 et 500 caractères.");
    }
    
    // Validation contenu
    if (strlen($contenu) < 50) {
        throw new Exception("Le contenu doit contenir au moins 50 caractères.");
    }
    
    // Validation catégorie
    $categories_valides = ['Conseils', 'Critiques', 'Actualités'];
    if (!in_array($categorie, $categories_valides)) {
        throw new Exception("Catégorie invalide. Choisissez parmi : " . implode(', ', $categories_valides));
    }
    
    // Vérification auteur existe
    $sql = "SELECT COUNT(*) FROM utilisateur WHERE auteur_id = :auteur_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':auteur_id' => $auteur_id]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("L'auteur spécifié n'existe pas.");
    }
    
    // Insertion
    $sql = "INSERT INTO article (titre, extrait, contenu, auteur_id, categorie, image_url) 
            VALUES (:titre, :extrait, :contenu, :auteur_id, :categorie, :image_url)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titre' => $titre,
        ':extrait' => $extrait,
        ':contenu' => $contenu,
        ':auteur_id' => $auteur_id,
        ':categorie' => $categorie,
        ':image_url' => $image_url
    ]);
    
    return (int) $pdo->lastInsertId();
}

/**
 * Met à jour un article existant
 * @param PDO $pdo - Connexion à la base de données
 * @param int $article_id - ID de l'article à modifier
 * @param string $titre - Nouveau titre
 * @param string $extrait - Nouvel extrait
 * @param string $contenu - Nouveau contenu
 * @param string $categorie - Nouvelle catégorie
 * @param string|null $image_url - Nouvelle URL d'image
 * @return bool - True si succès
 * @throws Exception - En cas d'erreur
 */
function modifierArticle(PDO $pdo, int $article_id, string $titre, string $extrait, string $contenu, string $categorie, ?string $image_url = null): bool {
    
    // Vérifier que l'article existe
    $sql = "SELECT COUNT(*) FROM article WHERE article_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $article_id]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("Article introuvable.");
    }
    
    // Validations
    if (strlen($titre) < 5 || strlen($titre) > 200) {
        throw new Exception("Le titre doit contenir entre 5 et 200 caractères.");
    }
    
    $categories_valides = ['Conseils', 'Critiques', 'Actualités'];
    if (!in_array($categorie, $categories_valides)) {
        throw new Exception("Catégorie invalide.");
    }
    
    // Mise à jour
    $sql = "UPDATE article 
            SET titre = :titre, 
                extrait = :extrait, 
                contenu = :contenu, 
                categorie = :categorie, 
                image_url = :image_url
            WHERE article_id = :id";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':id' => $article_id,
        ':titre' => $titre,
        ':extrait' => $extrait,
        ':contenu' => $contenu,
        ':categorie' => $categorie,
        ':image_url' => $image_url
    ]);
}

/**
 * Supprime un article et ses commentaires associés
 * @param PDO $pdo - Connexion à la base de données
 * @param int $article_id - ID de l'article à supprimer
 * @return bool - True si succès
 * @throws Exception - Si l'article n'existe pas
 */
function supprimerArticle(PDO $pdo, int $article_id): bool {
    
    // Vérifier existence
    $sql = "SELECT COUNT(*) FROM article WHERE article_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $article_id]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("Article introuvable.");
    }
    
    // Suppression (CASCADE supprime les commentaires automatiquement)
    $sql = "DELETE FROM article WHERE article_id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':id' => $article_id]);
}

/**
 * Met à jour un utilisateur
 * @param PDO $pdo - Connexion à la base de données
 * @param int $auteur_id - ID de l'utilisateur
 * @param string $pseudo - Nouveau pseudo
 * @param string $email - Nouvel email
 * @param string|null $nouveau_mdp - Nouveau mot de passe (null si pas de changement)
 * @return bool - True si succès
 * @throws Exception - En cas d'erreur
 */
function modifierUtilisateur(PDO $pdo, int $auteur_id, string $pseudo, string $email, ?string $nouveau_mdp = null): bool {
    
    // Vérifier existence
    $sql = "SELECT COUNT(*) FROM utilisateur WHERE auteur_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $auteur_id]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("Utilisateur introuvable.");
    }
    
    // Validation
    if (strlen($pseudo) < 5 || strlen($pseudo) > 50) {
        throw new Exception("Le pseudo doit contenir entre 5 et 50 caractères.");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email invalide.");
    }
    
    // Vérifier doublons (exclure l'utilisateur actuel)
    $sql = "SELECT COUNT(*) FROM utilisateur WHERE pseudo = :pseudo AND auteur_id != :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pseudo' => $pseudo, ':id' => $auteur_id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ce pseudo est déjà utilisé.");
    }
    
    $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email AND auteur_id != :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email, ':id' => $auteur_id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Cet email est déjà utilisé.");
    }
    
    // Mise à jour avec ou sans mot de passe
    if ($nouveau_mdp !== null) {
        if (strlen($nouveau_mdp) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères.");
        }
        $hash = password_hash($nouveau_mdp, PASSWORD_BCRYPT, ['cost' => 12]);
        $sql = "UPDATE utilisateur SET pseudo = :pseudo, email = :email, mot_de_passe = :mdp WHERE auteur_id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id' => $auteur_id, ':pseudo' => $pseudo, ':email' => $email, ':mdp' => $hash]);
    } else {
        $sql = "UPDATE utilisateur SET pseudo = :pseudo, email = :email WHERE auteur_id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id' => $auteur_id, ':pseudo' => $pseudo, ':email' => $email]);
    }
}

/**
 * Supprime un utilisateur (et ses articles/commentaires en CASCADE)
 * @param PDO $pdo - Connexion à la base de données
 * @param int $auteur_id - ID de l'utilisateur à supprimer
 * @return bool - True si succès
 * @throws Exception - Si l'utilisateur n'existe pas
 */
function supprimerUtilisateur(PDO $pdo, int $auteur_id): bool {
    
    // Vérifier existence
    $sql = "SELECT COUNT(*) FROM utilisateur WHERE auteur_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $auteur_id]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("Utilisateur introuvable.");
    }
    
    // Suppression (CASCADE supprime articles et commentaires)
    $sql = "DELETE FROM utilisateur WHERE auteur_id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':id' => $auteur_id]);
}

/**
 * Crée un nouveau commentaire
 * @param PDO $pdo - Connexion à la base de données
 * @param int $article_id - ID de l'article commenté
 * @param int $auteur_id - ID de l'auteur du commentaire
 * @param string $contenu - Contenu du commentaire
 * @param int $note - Note de 1 à 5
 * @return int - ID du commentaire créé
 * @throws Exception - En cas d'erreur
 */
function creerCommentaire(PDO $pdo, int $article_id, int $auteur_id, string $contenu, int $note): int {
    
    // Validation note
    if ($note < 1 || $note > 5) {
        throw new Exception("La note doit être entre 1 et 5.");
    }
    
    // Validation contenu
    if (strlen($contenu) < 10) {
        throw new Exception("Le commentaire doit contenir au moins 10 caractères.");
    }
    
    // Vérifier que l'article existe
    $sql = "SELECT COUNT(*) FROM article WHERE article_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $article_id]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("Article introuvable.");
    }
    
    // Insertion
    $sql = "INSERT INTO commentaire (article_id, auteur_id, contenu, note) 
            VALUES (:article_id, :auteur_id, :contenu, :note)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':article_id' => $article_id,
        ':auteur_id' => $auteur_id,
        ':contenu' => $contenu,
        ':note' => $note
    ]);
    
    return (int) $pdo->lastInsertId();
}

/**
 * Supprime un commentaire
 * @param PDO $pdo - Connexion à la base de données
 * @param int $commentaire_id - ID du commentaire à supprimer
 * @return bool - True si succès
 * @throws Exception - Si le commentaire n'existe pas
 */
function supprimerCommentaire(PDO $pdo, int $commentaire_id): bool {
    
    // Vérifier existence
    $sql = "SELECT COUNT(*) FROM commentaire WHERE commentaire_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $commentaire_id]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("Commentaire introuvable.");
    }
    
    // Suppression
    $sql = "DELETE FROM commentaire WHERE commentaire_id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':id' => $commentaire_id]);
}
?>