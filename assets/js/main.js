document.addEventListener('DOMContentLoaded', function () {
    var popup = document.getElementById('popupAd');
    if (popup && !sessionStorage.getItem('popupSeen')) {
        popup.hidden = false;
        sessionStorage.setItem('popupSeen', '1');
    }

    document.querySelectorAll('[data-close-popup]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (popup) {
                popup.hidden = true;
            }
        });
    });

    if (popup) {
        popup.addEventListener('click', function (event) {
            if (event.target === popup) {
                popup.hidden = true;
            }
        });
    }
});
