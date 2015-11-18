/**
 *
 */
var jpm = $.jPanelMenu({
    openPosition: '80%',
    direction: 'right',
    duration: 300,
    easing: 'linear',
    beforeOn: function () {
        jpm.off();
    }
});


function toggleContent(selector, options) {
    if (!selector) {
        return false;
    }

    if (!options) {
        options = {
            'charLimit': 200,
            'ellipses': '...',
            'switchText': 'See more'
        }
    }
    var charLimit = options.charLimit || 200;
    var ellipses = options.ellipses || '...';
    var switchText = options.switchText || 'See more';

    var htmlContent = $(selector).html();
    var textContent = $(selector).text();
    var textLength = textContent.length;

    if (textLength > charLimit) {
        var showContent = '<span id="short-content" class="toggle-content">' + textContent.substr(0, charLimit) +  ellipses + '</span>';
        var hideContent = '<span id="full-content" class="toggle-content">' + htmlContent + '</span>';
        var switchContent = '<span>' + '<a href="#" class="toggle-link" onclick="return toggleLink.call(this,event)">' + switchText + '</a>' + '</span>';
        $(selector).html('').html(showContent + hideContent + switchContent);
        $('#full-content').hide();
    }
    return false;
}
function toggleLink(event){
    var text = $(this).text();
    var shortContent = $('#short-content');
    var fullContent = $('#full-content');

    if (text === "Hide") {
        $(this).text("See more");
        fullContent.hide();
        shortContent.show();
        shortContent.scrollView();
    } else {
        $(this).text("Hide");
        shortContent.hide();
        fullContent.show();
        fullContent.scrollView();
    }
    return false;
}

(function () {
    jpm.on();
    toggleContent('#toggle-description');

    $('a.show-mapsmallDevice').on('click',function(){
        window.location.href = $(this).attr('href');
    });
})();