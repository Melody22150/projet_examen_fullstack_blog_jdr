/* MESSAGE DE BIENVENUE */

document.addEventListener("DOMContentLoaded", () => {
  const popup = document.getElementById("welcome-popup");
  const submitBtn = document.getElementById("submit-name");
  const input = document.getElementById("username");
  const banner = document.getElementById("welcome-message");
  const message = document.getElementById("personalized-message");

  // Vérifie si un prénom est déjà stocké
  const storedName = localStorage.getItem("userName");
 
    // Supprime les balises HTML ou scripts potentiels
    function sanitize(input) {
    return input.replace(/</g, "&lt;").replace(/>/g, "&gt;");
  }

  function showWelcome(name) {
    const safeName = sanitize(name);
    message.textContent = `Bienvenue dans les Chroniques, ${safeName} !`;
    banner.classList.remove("hidden");

    // Faire disparaître après 3 secondes
    setTimeout(() => {
      banner.classList.add("fade-out");
    }, 3000);

    // Cacher définitivement après l'animation
    banner.addEventListener(
      "animationend",
      () => {
        banner.classList.add("hidden");
      },
      { once: true }
    );
  }

  // Si aucun prénom stocké → on affiche le popup
  if (!storedName) {
    popup.classList.remove("hidden");
    console.log("Aucun prénom trouvé, affichage du popup.");
  } else {
    showWelcome(storedName);
    console.log("Nom trouvé dans le localStorage :", storedName);
  }

  submitBtn.addEventListener("click", () => {
    const rawName = input.value.trim();
    const name = sanitize(rawName);

    if (name !== "") {
      localStorage.setItem("userName", name);
      popup.classList.add("hidden");
      showWelcome(name);
      console.log("Nom saisi :", name);
    } else {
      alert("Merci d'entrer un prénom valide.");
      console.log("Prénom non valide saisi");
    }
  });
});


// JS MENU BURGER
const navToggle = document.querySelector('.nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
  navToggle.addEventListener('click', () => {
    const open = navToggle.getAttribute('aria-expanded') === 'true';
    navToggle.setAttribute('aria-expanded', String(!open));
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && navToggle.getAttribute('aria-expanded') === 'true') {
      navToggle.setAttribute('aria-expanded', 'false');
      navToggle.focus();
    }
  });
}

// Confirmation chargement
console.log("Script de la page d'accueil chargé.");

// Bouton retour en haut
const backToTopBtn = document.getElementById("backToTop");

// Affiche le bouton quand on scroll vers le bas
window.addEventListener("scroll", () => {
  if (window.scrollY > 300) { // apparaît après 300px de défilement
    backToTopBtn.style.display = "block";
  } else {
    backToTopBtn.style.display = "none";
  }
});

// Clique → remonte en haut de page
backToTopBtn.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth" // défilement doux
  });
});

// Déplacer la mini-bio sous les articles en mobile (≤ 420px)
(function(){
  const miniBio = document.querySelector('.mini-bio');
  const articlesWrapper = document.querySelector('.articles-wrapper');
  if (!miniBio || !articlesWrapper) return;

  const mqMobile = window.matchMedia('(max-width: 420px)');
  const originalParent = miniBio.parentElement;
  const originalNext = miniBio.nextElementSibling;

  const relocateMiniBio = () => {
    if (mqMobile.matches) {
      if (miniBio.parentElement !== articlesWrapper.parentElement) {
        articlesWrapper.insertAdjacentElement('afterend', miniBio);
      }
    } else {
      if (originalParent) {
        if (originalNext && originalNext.parentElement === originalParent) {
          originalParent.insertBefore(miniBio, originalNext);
        } else {
          originalParent.appendChild(miniBio);
        }
      }
    }
  };

  relocateMiniBio();
  mqMobile.addEventListener('change', relocateMiniBio);
})();

// Déplacer le menu du footer en mobile (≤ 420px)
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
  const articleId = article.classList[1];

  if (isValidArticleId(articleId) && localStorage.getItem(articleId) === 'lu') {
    if (!article.querySelector('.lu-indicator')) {
      const indicator = document.createElement('span');
      indicator.textContent = '📘 Article lu';
      indicator.classList.add('lu-indicator');
      article.querySelector('.article-text').appendChild(indicator);
    }
  }
});

// === Quand on clique sur "Lire la suite" ===
const lireBtns = document.querySelectorAll('.article-button');

lireBtns.forEach(btn => {
  btn.addEventListener('click', function () {
    const article = this.closest('.article');
    if (article) {
      const articleId = article.classList[1];
      if (isValidArticleId(articleId)) {
        localStorage.setItem(articleId, 'lu');

        if (!article.querySelector('.lu-indicator')) {
          const indicator = document.createElement('span');
          indicator.textContent = '📘 Article lu';
          indicator.classList.add('lu-indicator');
          article.querySelector('.article-text').appendChild(indicator);
        } else {
          article.querySelector('.lu-indicator').style.display = 'inline';
        }
      }
    }
  });
});