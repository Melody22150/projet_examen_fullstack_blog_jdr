use blog_jdr;

DROP TABLE IF EXISTS `utilisateur`;

CREATE TABLE if not EXISTS `utilisateur` (
  `auteur_id` int NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_inscription` date NOT NULL DEFAULT (curdate()),
  PRIMARY KEY (`auteur_id`),
  UNIQUE KEY `pseudo` (`pseudo`),
  UNIQUE KEY `email` (`email`)
);

DROP TABLE IF EXISTS `article`;

CREATE TABLE `article` (
  `article_id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `contenu` text NOT NULL,
  `extrait` varchar(300) DEFAULT NULL,
  `categorie` enum('Scénarios','Règles','Matériel','Univers','Conseils') NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `date_publication` date NOT NULL DEFAULT (curdate()),
  `auteur_id` int NOT NULL,
  PRIMARY KEY (`article_id`),
  KEY `auteur_id` (`auteur_id`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`auteur_id`) REFERENCES `utilisateur` (`auteur_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS `commentaire`;

CREATE TABLE `commentaire` (
  `commentaire_id` int NOT NULL AUTO_INCREMENT,
  `contenu_commentaire` text NOT NULL,
  `date_commentaire` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `note` tinyint DEFAULT NULL,
  `auteur_id` int NOT NULL,
  `article_id` int NOT NULL,
  PRIMARY KEY (`commentaire_id`),
  KEY `auteur_id` (`auteur_id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `commentaire_ibfk_1` FOREIGN KEY (`auteur_id`) REFERENCES `utilisateur` (`auteur_id`) ON DELETE CASCADE,
  CONSTRAINT `commentaire_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `article` (`article_id`) ON DELETE CASCADE,
  CONSTRAINT `commentaire_chk_1` CHECK ((`note` between 1 and 5))
);

INSERT INTO `utilisateur` VALUES (1,'MelodyMJ','melody@jdr-blog.fr','$2y$12$abcDEF123xyz456ABC789DEFghiJKLmnoPQRSTUvwxYZ01','2025-11-11'),
(3,'SophieJoueuse','sophie@jdr-blog.fr','$2y$12$stuvWX456yza789BCD123EFGstuvWXYZ012345678903','2025-11-11'),
(5,'TestUser2025','test@exemple.fr','$2y$12$rw7d5PS55hKCg/Y5PRMbpObW5Cj5ZJXate7umRJwwcKCCof74eK2.','2025-11-11');

INSERT INTO `article` VALUES (1,'Par où commencer ?',"Vous êtes curieux du JDR mais vous ne savez pas par où débuter ? Voici les bases pour bien démarrer une aventure autour d'une table. 
Le jeu de rôle est avant tout une activité sociale et créative où l'imagination n'a pas de limites...",'Les bases pour bien démarrer une aventure JDR','Conseils','images/article1_jdr.webp','2025-11-11',1),
(2,'Créer un personnage vivant en 5 étapes','Nom, race, classe, motivation... Je vous guide pas à pas pour concevoir un personnage mémorable et immersif qui marquera vos parties de JDR. Un bon personnage 
a une histoire, des motivations et des défauts qui le rendent attachant...','Guide complet pour créer un personnage mémorable','Conseils','images/article2_jdr_440x440.webp','2025-11-11',1);

INSERT INTO `commentaire` VALUES (2,'Très utile, merci pour les conseils pratiques.','2025-11-11 18:02:24',4,3,1),
(3,'Le guide est clair et bien expliqué, parfait pour les débutants.','2025-11-11 18:02:24',5,3,2);

