function createNote(note, achievement_id, edit) {
    // console.log(edit + " " + achievement_id + " " + note);
    if (note.trim() == "") {


    } else {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "create_note", note: note.trim(), achievement_id: achievement_id, edit: edit}
        })
                .done(function (result) {
                    listNotes(achievement_id);
                    //    console.log(result);
                });
    }
}

function deleteNote(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this as a note?")) {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "delete_note", id: id}
        })
                .done(function (result) {
                    listNotes(achievement_id);
                });
    }
}
