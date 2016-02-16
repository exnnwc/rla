function createNote(note, achievement_id) {
    if (!testIfVariableIsNumber(achievement_id, "achievement_id") || !testIfVariableIsString(note, "note") || note.trim() === "") {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "create_note", note: note.trim(), achievement_id: achievement_id}
    })
            .done(function (result) {
                listNotes(achievement_id);
            });
}

function deleteNote(id, achievement_id) {
    if (!testIfVariableIsNumber(achievement_id, "achievement_id") || !testIfVariableIsNumber(id, "id") ){
        return;
    }
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
