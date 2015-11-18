function popupWindow(url, width, height) {
    new Popup(url, width, height).open();
    return false;
}

/*Notes*/
function showNotesBlocksByType(noteTypeId, noteType) {
    $.getJSON('/admin4/note/showNotesBlocksByType/noteTypeId/' + noteTypeId + '/noteType/' + noteType, function (data) {
        if (!data) {
            return false;
        }
        $('#' + data.blockType).html(data.html);
        clearNoteBox(data.blockType);
    });
}
function showNotesBlocksById(noteId) {
    $.getJSON('/admin4/note/showNotesBlocksById/noteId/' + noteId, function (data) {
        if (!data) {
            return false;
        }
        $('#' + data.blockType).html(data.html);
        clearNoteBox(data.blockType);
    });
}
function deleteNote(noteId) {
    if (noteId.length == 0) {
        return false;
    }
    $.post('/admin4/note/deleteNote/', {'id': noteId }, function () {
        showNotesBlocksById(noteId);
    });
}
function clearNoteBox(noteType) {
    $('#' + noteType + '_not_blurb').val('');
    $('#' + noteType + '_not_id').val('');
}
function saveNoteBlurb(noteType, noteTypeId) {
    var noteBlurb = $('#' + noteType + '_not_blurb').val();
    var noteId = $('#' + noteType + '_not_id').val();
    if (noteBlurb.length == 0) {
        alert("Please enter your note..");
        return false;
    }
    $.post('/admin4/note/saveNoteBlurb/', { 'noteId': noteId, 'noteType': noteType, 'noteTypeId': noteTypeId, 'noteBlurb': noteBlurb}, function (not_id) {
        clearNoteBox(noteType);
        if (noteId == 0 || noteId.length == 0) {
            showNotesBlocksById(not_id);
        } else {
            showNotesBlocksById(noteId);
        }
    });
}
/*Notes*/