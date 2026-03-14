(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.avstube-upload-form');
    if (!form) {
      return;
    }

    form.addEventListener('submit', function (event) {
      var title = form.querySelector('#avstube_video_title');
      var file = form.querySelector('#avstube_video_file');
      var external = form.querySelector('#avstube_video_external_mp4');
      var embed = form.querySelector('#avstube_video_embed_url');

      if (!title.value.trim()) {
        event.preventDefault();
        alert('Please enter a video title.');
        return;
      }

      if (!file.value && !external.value.trim() && !embed.value.trim()) {
        event.preventDefault();
        alert('Please upload MP4, provide external MP4 URL, or embed URL.');
      }
    });
  });
})();
