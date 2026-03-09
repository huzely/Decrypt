(function () {
    const popup = document.getElementById('popupOverlay');
    if (!popup) return;

    const popupImage = popup.dataset.image;
    const popupLinksRaw = popup.dataset.links || '';
    const links = popupLinksRaw
        .split(',')
        .map((s) => s.trim())
        .filter((s) => s.length > 0);

    if (!popupImage || links.length === 0) return;

    const viewed = localStorage.getItem('popupViewed') === 'true';
    if (viewed) return;

    const imgElement = popup.querySelector('img');
    if (imgElement) imgElement.src = popupImage;

    popup.style.display = 'flex';

    const redirect = () => {
        localStorage.setItem('popupViewed', 'true');
        const randomLink = links[Math.floor(Math.random() * links.length)];
        window.location.href = randomLink;
    };

    popup.addEventListener('click', redirect);
})();
