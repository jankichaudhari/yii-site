function iframeHeight(iframeId) {
    var contentH = $("#" + iframeId).contents().find("html").height();
    var windowH = $(window).height();
    var height = contentH;
    if (windowH > contentH) {
        height = windowH
    }
    $("#" + iframeId).height(height);
}
function popupPreview(selector, detailViewUrl) {
    var $el = $(selector);
    $el.attr('action', detailViewUrl);
    window.open('about:blank', "popupWindow", "status=1,scrollbars=1,menubar=1,resizable=1,width=1200,height=1100");

    $el.attr('target', "popupWindow");
    $el.submit();

    return false;
}

function stopPopupPreview(selector) {
    var $el = $(selector);

    $el.attr('action', "");
    $el.attr('target', "_self");
	$el.submit();
	return false;
}