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

// === Boutons MASQUER / AFFICHER ===
const btnMasquer = document.getElementById('masquer-lus');
const btnAfficher = document.getElementById('afficher-tous');

btnMasquer.addEventListener('click', () => {
  document.querySelectorAll('.article').forEach(article => {
    const articleId = article.classList[1];
    if (isValidArticleId(articleId) && localStorage.getItem(articleId) === 'lu') {
      article.style.display = 'none';
    }
  });
});

btnAfficher.addEventListener('click', () => {
  document.querySelectorAll('.article').forEach(article => {
    article.style.display = 'flex';
  });
});

// Liste déroulante
document.addEventListener("DOMContentLoaded", () => {
  const selectCategorie = document.getElementById('categorie-select');
  const articles = document.querySelectorAll('.article');

  selectCategorie.addEventListener('change', () => {
    const selected = selectCategorie.value;

    articles.forEach(article => {
      if (selected === 'toutes' || article.classList.contains(selected)) {
        article.style.display = 'flex';
      } else {
        article.style.display = 'none';
      }
    });
  });

  // TEST

  console.log("Script de la page articles chargé.");

  // Articles déjà lus
  articles.forEach(article => {
    const articleId = article.classList[1];
    if (isValidArticleId(articleId) && localStorage.getItem(articleId) === 'lu') {
      console.log(`Article ${articleId} est marqué comme lu`);
    }
  });

  // FIN DU TEST

  // Clic sur Lire la suite
  lireBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      const article = this.closest('.article');
      if (article) {
        const articleId = article.classList[1];
        if (isValidArticleId(articleId)) {
          console.log(`Article ${articleId} a été lu`);
        }
      }
    });
  });

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

  // Bouton Masquer
  btnMasquer.addEventListener('click', () => {
    console.log("Bouton 'Masquer les articles lus' cliqué.");
  });

  // Bouton Afficher
  btnAfficher.addEventListener('click', () => {
    console.log("Bouton 'Afficher tous les articles' cliqué.");
  });

  // Sélection d'une catégorie
  selectCategorie.addEventListener('change', () => {
    console.log("Catégorie sélectionnée :", selectCategorie.value);
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

