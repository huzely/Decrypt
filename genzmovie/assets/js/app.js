$(function () {
    const $search = $('#movie-search');
    const $box = $('#autocomplete-box');

    $search.on('input', function () {
        const q = $(this).val().trim();
        if (q.length < 2) {
            $box.empty();
            return;
        }
        $.getJSON('/genzmovie/?route=autocomplete&q=' + encodeURIComponent(q), function (items) {
            $box.empty();
            items.forEach(item => {
                $box.append(`<a href="/genzmovie/?route=movie&slug=${item.slug}">${item.title}</a>`);
            });
        });
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#movie-search').length) {
            $box.empty();
        }
    });
});
