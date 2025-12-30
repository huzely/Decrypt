document.addEventListener('DOMContentLoaded', () => {
  const navLinks = document.querySelectorAll('.admin-nav a');
  const path = window.location.pathname.split('/').pop();
  navLinks.forEach(link => {
    if (link.getAttribute('href').includes(path)) {
      link.classList.add('active');
    }
  });
});
