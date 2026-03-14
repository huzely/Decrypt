(function () {
  'use strict';

  function postJSON(url) {
    return fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      }
    }).then(function (response) {
      return response.json();
    });
  }

  document.addEventListener('click', function (event) {
    var button = event.target.closest('.avstube-like-button');
    if (!button || !window.avstubeVM) {
      return;
    }

    var videoId = button.getAttribute('data-video-id');
    if (!videoId) {
      return;
    }

    postJSON(window.avstubeVM.restUrl + 'video/like/' + videoId)
      .then(function (data) {
        if (typeof data.likes !== 'undefined') {
          var count = button.querySelector('.avstube-like-count');
          if (count) {
            count.textContent = data.likes;
          }
        }
      })
      .catch(function () {});
  });

  document.addEventListener('play', function (event) {
    if (!window.avstubeVM || !event.target.classList.contains('avstube-player')) {
      return;
    }

    var wrap = event.target.closest('.avstube-player-wrap');
    if (!wrap || wrap.dataset.viewTracked === '1') {
      return;
    }

    var videoId = wrap.getAttribute('data-video-id');
    if (!videoId) {
      return;
    }

    wrap.dataset.viewTracked = '1';

    postJSON(window.avstubeVM.restUrl + 'video/view/' + videoId).catch(function () {
      wrap.dataset.viewTracked = '0';
    });
  }, true);
})();
