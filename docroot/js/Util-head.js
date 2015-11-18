/**
 *
 */
var closePopUpRedirect = function (url) {
    $.fancybox.close();
    window.location = url;
};

var openPopUp = function (selector, options) {

    if (!selector) {
        return false;
    }

    $(selector).fancybox({
        'cyclic': options.cyclic || false,
        'autoDimensions': options.autoDimensions || false,
        'width': options.width || '100%',
        'height': options.height || '100%',
        'type': options.type || '',
        'overlayColor': options.overlayColor || '#FFFFFF',
        'centerOnScroll': options.centerOnScroll || true,
        'autoScale': options.autoScale || true,
        'transitionIn': options.transitionIn || 'elastic',
        'transitionOut': options.transitionOut || 'elastic',
        'titleShow': options.titleShow || false,
        'titlePosition': options.titlePosition || 'over',
        'padding': options.padding || 10,
        'margin': options.margin || 20,
        'titleFormat': function (title) {
            return '<div class="fancybox-middle-title">' + (title.length ? ' &nbsp;&nbsp;' + title + '&nbsp;&nbsp;' : '') + '</div>';
        }
    });
};

var sliderGallery = function (selector, options) {
    if (!selector) {
        selector = '.slider-container';
    }

    if (!options) {
        options = {};
    }

    $(selector).slider({
        startAtSlide: options.startAtSlide || 1,
        autoSlide: options.autoSlide || false,
        autoSlideTimer: options.autoSlideTimer || 5000,
        autoSlideTransTimer: options.autoSlideTransTimer || 700,
        desktopClickDrag: options.desktopClickDrag || true,
        keyboardControls: options.keyboardControls || true,
        infiniteSlider: options.infiniteSlider || true,
        snapToChildren: options.snapToChildren || true,
        snapSlideCenter: options.snapSlideCenter || true,
        navPrevSelector: options.navPrevSelector || $('#slider-left-arrow'),
        navNextSelector: options.navNextSelector || $('#slider-right-arrow')
    });
};

var repeatSlides = function (selector, totalItems, itemWidth) {

    if (!totalItems || !itemWidth) {
        return false;
    }

    if (!selector) {
        selector = '.slider';
    }

    var eleWidth = (totalItems - 1) * itemWidth;
    var windowWidth = $(window).width();
    if (eleWidth <= windowWidth) {
        var diff = windowWidth - eleWidth;
        var reqItem = Math.round(diff / itemWidth);
        if (reqItem > totalItems) {
            var itemCount = 0;
            while (itemCount != reqItem) {
                appendSlides(totalItems, selector,itemCount);
                itemCount++;
            }
        } else if (reqItem == totalItems) {
            appendSlides(totalItems, selector, 0);
        } else {
            appendSlides(reqItem, selector, 0);
        }
    }
};

var appendSlides = function (limit, selector, totalRepeats) {
    if (!limit || (limit > totalItems)) {
        limit = totalItems;
    }
    totalRepeats = totalRepeats ? totalRepeats : 0;
    for (var i = 1; i <= limit; i++) {
        var item = $('.item:nth-child(' + i + ')').clone();
        var newId = (item.attr('id')) + '-' + (i + totalRepeats);
        var newImgId = (item.children('img').attr('id')) + '-' + (i + totalRepeats);
        item.attr('id',newId);
        item.children('img').attr('id',newImgId);
        $(selector).append(item);
        $(selector).slider('update');
    }
};

var updatePhotoSizes = function (selector) {
    if (!selector) {
        selector = '.item';
    }
    var windowW = $(window).width();
    var windowH = $(window).height();
    $(selector).each(function (i) {
        var id = $(this).children('img').attr('id');
        var imgClass = $(this).children('img').attr('class');
        var img = $('#' + id);
        var orgW = img.attr('width');
        var orgH = img.attr('height');
        var newW = orgW;
        var newH = orgH;

        switch (imgClass) {
            case 'horizontal' :
                if (windowW < orgW) {
                    newH = (orgH * windowW) / orgW;
                    newW = windowW;
                    if (windowH < newH) {
                        newW = (newW * windowH) / newH;
                        newH = windowH;
                    }
                }
                break;
            case 'vertical' :
                if (windowH < orgH) {
                    newW = (orgW * windowH) / orgH;
                    newH = windowH;
                    if (windowW < newW) {
                        newH = (newH * windowW) / newW;
                        newW = windowW;
                    }
                }
                break;
            case 'square' :
                if ((windowH > windowW) && (windowW < orgW)) {
                    newW = newH = windowW;
                } else if (windowH < orgH) {
                    newW = newH = windowH;
                }
                break;
        }

        img.width(newW);
        img.height(newH);

    });
};


