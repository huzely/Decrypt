document.addEventListener('DOMContentLoaded', () => {
    const burger = document.querySelector('.hamburger');
    const mobileMenu = document.querySelector('.mobile-menu');
    if (burger && mobileMenu) {
        burger.addEventListener('click', () => {
            burger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });
        mobileMenu.addEventListener('click', () => {
            burger.classList.remove('active');
            mobileMenu.classList.remove('active');
        });
        mobileMenu.querySelector('.panel').addEventListener('click', (e) => e.stopPropagation());
    }
});
