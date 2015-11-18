/**
 *
 */
var smallDevSize = 768;
var windowWidth = $(window).width();
var totalItems = $('.slider .item').length;
var itemWidth = $('.slider .item').width();
repeatSlides('.slider', totalItems, itemWidth);

sliderGallery('.slider-container', {
//    autoSlide: true
});
sliderGallery('.park-gallery', {
    navPrevSelector: $('#park-left-arrow'),
    navNextSelector: $('#park-right-arrow')
});

$('.item a, .descImage').hover(
    function () {
        $(this).find('.zoom-symbol').show();
    }, function () {
        $(this).find('.zoom-symbol').hide();
    }
);

$(".gallery").each(function () {
    var thisRel = $(".gallery").attr('rel');

    if (thisRel == 'park-gallery') {
        openPopUp(this, {
            cyclic: true,
            titleShow: true,
            titlePosition: 'inside'
        });
    } else {
        openPopUp(this, {
            cyclic: true
        });
    }
});

$('a.show-map').each(function () {
    openPopUp(this, {
        type: 'iframe',
        titleShow: true
    });
});

$('a.play-video').each(function () {
    openPopUp(this, {
        type: 'iframe',
        width: 1008,
        height: 567
    });
});

$.fn.scrollView = function () {
    return this.each(function () {
        $('html, body').animate({
            scrollTop: $(this).offset().top
        }, 1000);
    });
};

$(document).scroll(function (event) {
    event.preventDefault();
    if ($(this).scrollTop() > 500) {
        $('.back-to-top').fadeIn();
    } else {
        $('.back-to-top').fadeOut();
    }
});

$('.back-to-top').on('click', function (event) {
    $.scrollTo('#header', 500);
});

/* Social Media */
Share = {
    facebook: function (purl, ptitle, pimg, text) {
        url = 'http://www.facebook.com/sharer.php?s=100';
        url += '&p[title]=' + encodeURIComponent(ptitle);
        url += '&p[summary]=' + encodeURIComponent(text);
        url += '&p[url]=' + encodeURIComponent(purl);
        url += '&p[images][0]=' + encodeURIComponent(pimg);
        Share.popup(url);
    },
    twitter: function (purl, ptitle) {
        url = 'http://twitter.com/share?';
        url += 'text=' + encodeURIComponent(ptitle);
        url += '&url=' + encodeURIComponent(purl);
        url += '&counturl=' + encodeURIComponent(purl);
        Share.popup(url);
    },
    popup: function (url) {
        window.open(url, '', 'toolbar=0,status=0,width=626, height=436');
    }
};
!function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (!d.getElementById(id)) {
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);
    }
}(document, "script", "twitter-wjs");
(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


