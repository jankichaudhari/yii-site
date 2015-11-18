$(window).resize(function () {
    $('iframe').each(function () {
        var id = $(this).attr('id');
        iframeHeight(id);
    });
});

setTimeout(function () {
    $('iframe').each(function () {
        var id = $(this).attr('id');
        iframeHeight(id);
    });
}, 1000);