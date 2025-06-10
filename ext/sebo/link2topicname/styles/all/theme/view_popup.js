document.addEventListener('DOMContentLoaded', () => {
  const popup = document.createElement('div');
  popup.className = 'l2t-popup-global';

  const bodyBg = getComputedStyle(document.body).backgroundColor;
  popup.style.backgroundColor = bodyBg;

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

      let left = linkRect.left;
      if (left + popupRect.width > viewportWidth) {
        left = viewportWidth - popupRect.width - 10;
      }
      if (left < 10) left = 10;

      let top;
      popup.classList.remove('arrow-up', 'arrow-down');
      if (linkRect.top > popupRect.height + 10) {
        top = window.scrollY + linkRect.top - popupRect.height - 8;
        popup.classList.add('arrow-down');
      } else {
        top = window.scrollY + linkRect.bottom + 8;
        popup.classList.add('arrow-up');
      }

      const linkWidth = linkRect.width;

      if (linkWidth > 450) {
        const marginLeft = (linkWidth - 450) / 2;
        popup.style.marginLeft = `${marginLeft}px`;
      } else {
        popup.style.marginLeft = '0px';
      }

      const popupWidth = popupRect.width;
      let arrowLeft = linkRect.left + linkWidth * 0.6 - left;

      const arrowMargin = 30;
      if (arrowLeft > popupWidth - arrowMargin) arrowLeft = popupWidth - arrowMargin;
      if (arrowLeft < arrowMargin) arrowLeft = arrowMargin;

      popup.style.setProperty('--arrow-left', `${arrowLeft}px`);
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
