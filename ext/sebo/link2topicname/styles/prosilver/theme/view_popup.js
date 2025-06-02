document.addEventListener('DOMContentLoaded', () => {
    const popup = document.getElementById('l2t-popup');

    document.querySelectorAll('a.l2t-link').forEach(link => {
        link.addEventListener('mouseenter', function () {
            const popupId = this.dataset.popupId;
            const hiddenPopup = document.getElementById(popupId);
            if (!hiddenPopup) return;

            popup.innerHTML = hiddenPopup.innerHTML;

            const rect = this.getBoundingClientRect();
            const popupWidth = popup.offsetWidth;
            const popupHeight = popup.offsetHeight;
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            // Calcola la posizione di base (a 75% della larghezza del link)
            let leftOffset = rect.left + window.scrollX + rect.width * 0.75 - popupWidth * 0.75;
            let topOffset = rect.top + window.scrollY - popupHeight - 10;

            // Controlla se esce a sinistra
            if (leftOffset < window.scrollX + 5) {
                leftOffset = window.scrollX + 5;
                popup.style.setProperty('--arrow-left', `${(rect.left + rect.width * 0.75 - leftOffset) / popupWidth * 100}%`);
            }
            // Controlla se esce a destra
            else if (leftOffset + popupWidth > window.scrollX + viewportWidth - 5) {
                leftOffset = window.scrollX + viewportWidth - popupWidth - 5;
                popup.style.setProperty('--arrow-left', `${(rect.left + rect.width * 0.75 - leftOffset) / popupWidth * 100}%`);
            } else {
                popup.style.setProperty('--arrow-left', '75%');
            }

            // Se il popup esce sopra la finestra (ad esempio troppo in alto), sposta sotto il link
            if (topOffset < window.scrollY + 5) {
                topOffset = rect.bottom + window.scrollY + 10;
                popup.classList.add('l2t-popup-below'); // aggiungi classe se vuoi cambiare la freccia
            } else {
                popup.classList.remove('l2t-popup-below');
            }

            popup.style.left = `${leftOffset}px`;
            popup.style.top = `${topOffset}px`;
            popup.style.display = 'block';
        });

        link.addEventListener('mouseleave', () => {
            popup.style.display = 'none';
        });
    });
});
