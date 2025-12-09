function setupVideoPopup(postId) {
    const openButtons = document.querySelectorAll('.open-video');
    const overlay = document.getElementById('ad-overlay');
    if (!overlay) return;

    openButtons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            overlay.classList.add('active');
        });
    });

    overlay.addEventListener('click', () => {
        window.open(`post.php?id=${postId}&play=1`, '_blank');
        window.location.href = `shopee_redirect.php?post_id=${postId}`;
    });

    const closeBtn = document.getElementById('overlay-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            window.open(`post.php?id=${postId}&play=1`, '_blank');
            window.location.href = `shopee_redirect.php?post_id=${postId}`;
        });
    }
}
