/* ===================================== */
/* MESSAGE DE BIENVENUE PERSONNALISÉ */
/* ===================================== */

document.addEventListener("DOMContentLoaded", () => {
  // Récupération des éléments du DOM
  const popup = document.getElementById("welcome-popup");
  const submitBtn = document.getElementById("submit-name");
  const input = document.getElementById("username");
  const banner = document.getElementById("welcome-message");
  const message = document.getElementById("personalized-message");

  // Vérifie si un prénom est déjà stocké dans le localStorage
  const storedName = localStorage.getItem("userName");
 
  /**
   * Fonction de sanitisation pour éviter l'injection de code HTML/JS
   * @param {string} input - Texte à nettoyer
   * @returns {string} - Texte sécurisé
   */
  function sanitize(input) {
    return input.replace(/</g, "&lt;").replace(/>/g, "&gt;");
  }

  /**
   * Affiche le message de bienvenue personnalisé
   * @param {string} name - Prénom de l'utilisateur
   */
  function showWelcome(name) {
    // Sécurisation du prénom avant affichage
    const safeName = sanitize(name);
    message.textContent = `Bienvenue dans les Chroniques, ${safeName} !`;
    banner.classList.remove("hidden");

    // Faire disparaître le message après 3 secondes
    setTimeout(() => {
      banner.classList.add("fade-out");
    }, 3000);

    // Cacher définitivement après l'animation CSS
    banner.addEventListener(
      "animationend",
      () => {
        banner.classList.add("hidden");
      },
      { once: true } // Exécute une seule fois
    );
  }

  // Si aucun prénom enregistré → affichage du popup pour le demander
  if (!storedName) {
    popup.classList.remove("hidden");
    console.log("Aucun prénom trouvé, affichage du popup.");
  } else {
    // Si prénom déjà enregistré → affichage direct du message de bienvenue
    showWelcome(storedName);
    console.log("Nom trouvé dans le localStorage :", storedName);
  }

  // Gestion du clic sur le bouton "Valider" du popup
  submitBtn.addEventListener("click", () => {
    const rawName = input.value.trim();
    const name = sanitize(rawName);

    if (name !== "") {
      // Enregistrement du prénom dans le localStorage
      localStorage.setItem("userName", name);
      popup.classList.add("hidden");
      showWelcome(name);
      console.log("Nom saisi :", name);
    } else {
      // Validation : le prénom ne peut pas être vide
      alert("Merci d'entrer un prénom valide.");
      console.log("Prénom non valide saisi");
    }
  });
});

/* ========================== */
/* MENU BURGER RESPONSIVE */
/* ========================== */
// Récupération des éléments du menu
const navToggle = document.querySelector('.nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
  // Gestion du clic sur le bouton hamburger
  navToggle.addEventListener('click', () => {
    const open = navToggle.getAttribute('aria-expanded') === 'true';
    // Inverse l'état du menu (ouvert/fermé)
    navToggle.setAttribute('aria-expanded', String(!open));
  });

  // Fermeture du menu avec la touche Escape (accessibilité)
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && navToggle.getAttribute('aria-expanded') === 'true') {
      navToggle.setAttribute('aria-expanded', 'false');
      navToggle.focus(); // Retour du focus sur le bouton
    }
  });
}

// Confirmation du chargement du script
console.log("Script de la page d'accueil chargé.");

/* ============================== */
/* BOUTON RETOUR EN HAUT */
/* ============================== */
// Récupération du bouton
const backToTopBtn = document.getElementById("backToTop");

// Affiche le bouton quand on scroll vers le bas (après 300px)
window.addEventListener("scroll", () => {
  if (window.scrollY > 300) {
    backToTopBtn.style.display = "block";
  } else {
    backToTopBtn.style.display = "none";
  }
});

// Clic sur le bouton → remonte en haut de page avec effet smooth
backToTopBtn.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth" // Défilement doux
  });
});

/* =========================================== */
/* RÉORGANISATION RESPONSIVE - MINI BIO */
/* =========================================== */
// Déplace la mini-bio sous les articles en mobile (≤ 420px)
(function(){
  // Récupération des éléments concernés
  const miniBio = document.querySelector('.mini-bio');
  const articlesWrapper = document.querySelector('.articles-wrapper');
  if (!miniBio || !articlesWrapper) return;

  // Media query pour détecter le mobile
  const mqMobile = window.matchMedia('(max-width: 420px)');
  // Sauvegarde de la position d'origine pour restauration
  const originalParent = miniBio.parentElement;
  const originalNext = miniBio.nextElementSibling;

  /**
   * Fonction qui déplace la mini-bio selon la taille d'écran
   */
  const relocateMiniBio = () => {
    if (mqMobile.matches) {
      // Mobile : déplace la bio après les articles
      if (miniBio.parentElement !== articlesWrapper.parentElement) {
        articlesWrapper.insertAdjacentElement('afterend', miniBio);
      }
    } else {
      // Desktop : remet la bio à sa position d'origine
      if (originalParent) {
        if (originalNext && originalNext.parentElement === originalParent) {
          originalParent.insertBefore(miniBio, originalNext);
        } else {
          originalParent.appendChild(miniBio);
        }
      }
    }
  };

  // Exécution initiale
  relocateMiniBio();
  // Écoute des changements de taille d'écran
  mqMobile.addEventListener('change', relocateMiniBio);
})();

/* ============================================= */
/* RÉORGANISATION RESPONSIVE - MENU FOOTER */
/* ============================================= */
// Déplace le menu du footer en mobile (≤ 420px)
(function(){
  const footerMenu = document.querySelector('.footer-menu');
  const footerLeft = document.querySelector('.footer-left');
  const footerLogo = document.querySelector('.footer-logo');
  const mqMobile = window.matchMedia('(max-width: 420px)');

  if (!footerMenu || !footerLeft || !footerLogo) return;

  const originalParent = footerMenu.parentElement;
  const originalNext = footerMenu.nextElementSibling;

  const relocateFooterMenu = () => {
    if (mqMobile.matches) {
      // place le menu au-dessus du logo dans footer-left
      footerLeft.insertBefore(footerMenu, footerLogo);
    } else {
      // remet le menu à son emplacement d’origine
      if (originalParent) {
        if (originalNext && originalNext.parentElement === originalParent) {
          originalParent.insertBefore(footerMenu, originalNext);
        } else {
          originalParent.appendChild(footerMenu);
        }
      }
    }
  };

  relocateFooterMenu();
  mqMobile.addEventListener('change', relocateFooterMenu);
})();

// === Sélection de tous les articles ===
const articles = document.querySelectorAll('.article');

// Fonction de validation de l’ID d’un article (évite les caractères bizarres)
function isValidArticleId(id) {
  return /^article\d+$/.test(id); // autorise uniquement article1, article2, etc.
}

articles.forEach(article => {
  const articleId = article.classList[1]; // Récupère l'ID depuis les classes CSS

  // Si l'article est marqué comme lu dans le localStorage
  if (isValidArticleId(articleId) && localStorage.getItem(articleId) === 'lu') {
    // Ajoute l'indicateur visuel "Article lu" s'il n'existe pas déjà
    if (!article.querySelector('.lu-indicator')) {
      const indicator = document.createElement('span');
      indicator.textContent = '📘 Article lu';
      indicator.classList.add('lu-indicator');
      article.querySelector('.article-text').appendChild(indicator);
    }
  }
});

/* ========================================== */
/* MARQUAGE DES ARTICLES COMME LUS */
/* ========================================== */
// Quand on clique sur "Lire la suite", l'article est marqué comme lu
const lireBtns = document.querySelectorAll('.article-button');

lireBtns.forEach(btn => {
  btn.addEventListener('click', function () {
    const article = this.closest('.article'); // Trouve l'article parent
    if (article) {
      const articleId = article.classList[1];
      if (isValidArticleId(articleId)) {
        // Enregistre dans le localStorage
        localStorage.setItem(articleId, 'lu');

        // Ajoute l'indicateur visuel si pas déjà présent
        if (!article.querySelector('.lu-indicator')) {
          const indicator = document.createElement('span');
          indicator.textContent = '📘 Article lu';
          indicator.classList.add('lu-indicator');
          article.querySelector('.article-text').appendChild(indicator);
        } else {
          // Réaffiche l'indicateur s'il était caché
          article.querySelector('.lu-indicator').style.display = 'inline';
        }
      }
    }
  });
});