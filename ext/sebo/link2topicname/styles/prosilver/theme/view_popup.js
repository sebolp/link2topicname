document.addEventListener('DOMContentLoaded', () => {
  const popup = document.createElement('div');
  popup.className = 'l2t-popup-global';
  document.body.appendChild(popup);

  const links = document.querySelectorAll('a.l2t-link');

  links.forEach(link => {
    link.addEventListener('mouseenter', () => {
      const popupContent = link.getAttribute('data-popup');
      if (!popupContent) return;

      popup.innerHTML = popupContent;
      popup.style.display = 'block';

      const linkRect = link.getBoundingClientRect();
      const popupRect = popup.getBoundingClientRect();
      const viewportHeight = window.innerHeight;
      const viewportWidth = window.innerWidth;

      // Posizione orizzontale popup: non uscire dai bordi della finestra
      let left = linkRect.left;
      if (left + popupRect.width > viewportWidth) {
        left = viewportWidth - popupRect.width - 10; // padding 10px
      }
      if (left < 10) left = 10;

      // Decidi se sopra o sotto (popup sopra per default)
      let top;
      popup.classList.remove('arrow-up', 'arrow-down');
      if (linkRect.top > popupRect.height + 10) {
        // c'è spazio sopra
        top = window.scrollY + linkRect.top - popupRect.height - 8;
        popup.classList.add('arrow-down'); // freccia verso il basso, popup sopra
      } else {
        // altrimenti sotto
        top = window.scrollY + linkRect.bottom + 8;
        popup.classList.add('arrow-up'); // freccia verso l'alto, popup sotto
      }

      // Calcolo posizione freccia (variabile CSS --arrow-left)
      // La freccia deve essere all'interno del popup, mai fuori dai bordi
      const linkWidth = linkRect.width;
      const popupWidth = popupRect.width;

      // Posizione freccia teorica: quasi alla fine del link (es. 90% della larghezza del link)
      let arrowLeft = linkRect.left + linkWidth * 0.9 - left;

      // Limita arrowLeft a rientrare nel popup
      const arrowMargin = 16; // margine da sinistra/destra per non far uscire la freccia
      if (arrowLeft > popupWidth - arrowMargin) arrowLeft = popupWidth - arrowMargin;
      if (arrowLeft < arrowMargin) arrowLeft = arrowMargin;

      // Imposta la variabile CSS
      popup.style.setProperty('--arrow-left', `${arrowLeft}px`);

      // Posiziona il popup
      popup.style.left = `${left + window.scrollX}px`;
      popup.style.top = `${top}px`;
    });

    link.addEventListener('mouseleave', () => {
      popup.style.display = 'none';
      popup.innerHTML = '';
      popup.classList.remove('arrow-up', 'arrow-down');
    });
  });
});
