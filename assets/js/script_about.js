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
