function generatePreviewLink(id) {
    $.post('/admin4/instruction/previewLink/id/' + id, function (data) {
        prompt("Preview Link :", data);
    });
    return false;
}

function disablePreviewLink(id) {
    $.post('/admin4/instruction/disablePreviewLink/id/' + id, function (data) {
        alert(" Link has been disabled");
    });
    return false;
}

function popupWindow(url, width, height) {
    new Popup(url, width, height).open();
    return false;
}

function showInstructionOffers(instructionId) {
    $.get('/admin4/offer/listOffers/instructionId/' + instructionId, function (data) {
        $('#offers').html(data);
    });
}