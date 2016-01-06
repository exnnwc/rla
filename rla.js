

function CreateAction(achievement_id, action) {
    //console.log(achievement_id + " " + action);
    if (!$('#list_of_current_actions' + achievement_id + ' option:selected').val()) {
        reference = 0;

    } else {
        reference = $('#list_of_current_actions' + achievement_id).val();
    }
    // console.log ($('#list_of_current_actions'+achievement_id).val() + " " + achievement_id + " " + action);
    $.ajax({
        method: "POST",
        url: "work/work.php",
        data: {function_to_be_called: "create_action", achievement_id: achievement_id, action: action, reference: reference}
    })
            .done(function (result) {
                //console.log (result);
                listActions(achievement_id);
            });
}
function CreateNote(note, achievement_id, edit) {
    // console.log(edit + " " + achievement_id + " " + note);
    if (note.trim() == "") {


    } else {
        $.ajax({
            method: "POST",
            url: "notes.php",
            data: {function_to_be_called: "create", note: note.trim(), achievement_id: achievement_id, edit: edit}
        })
                .done(function (result) {
                    listNotes(achievement_id);
                    //    console.log(result);
                });
    }
}

function CreateRelation(a, b) {
    console.log (a + " " + b);
    if (a === b){
        $("#relation_error").html("Cannot make a self-referencing relation.");
    } else {
        $.ajax({
            method: "POST",
            url: "relations.php",
            data: {function_to_be_called: "create", a: a, b: b}
        })
                .done(function (result) {
                    if (result.substr(0, 1) == "0") {
                        $("#relation_error").html(result.substr(2, result.length));
                    } else {
                        listRelations(a);
                    }
                });
    } 
}
function CreateRequirement(required_for, required_by, type) {
    if (required_for != required_by) {
        $.ajax({
            method: "POST",
            url: "requirements.php",
            data: {function_to_be_called: "create", required_for: required_for, required_by: required_by}
        })
                .done(function (result) {
                    if (result.substr(0, 1) == "0") {
                        if (type == "for") {
                            $("#requirements_error" + required_for).html(result.substr(1, result.length));
                        } else if (type == "by") {
                            $("#requirements_error" + required_by).html(result.substr(1, result.length));
                        }
                    } else {
                        if (type == "for") {
                            listRequirements(required_for, type);
                        } else if (type == "by") {
                            listRequirements(required_by, type);
                        }
                    }
                });
    } else {
        if (type == "for") {
            $("#requirements_error" + required_for).html("Achievement cannot be a requirement of itself.");
        } else if (type == "by") {
            $("#requirements_error" + required_by).html("Achievement cannot be a requirement of itself.");
        }

    }
}


function DeleteAction(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this action?")) {
        $.ajax({
            method: "POST",
            url: "work/work.php",
            data: {function_to_be_called: "delete_action", id: id}
        })
                .done(function (result) {
                    listActions(achievement_id);
                });
    }
}
function DeleteNote(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this as a note?")) {
        $.ajax({
            method: "POST",
            url: "notes.php",
            data: {function_to_be_called: "delete", id: id}
        })
                .done(function (result) {
                    listNotes(achievement_id);
                });
    }
}
function DeleteRelation(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this as a relationship?")) {
        $.ajax({
            method: "POST",
            url: "relations.php",
            data: {function_to_be_called: "delete", id: id}
        })
                .done(function (result) {
                    listRelations(achievement_id);
                });
    }
}
function DeleteRequirement(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this as a requirement?")) {
        $.ajax({
            method: "POST",
            url: "requirements.php",
            data: {function_to_be_called: "delete", id: id}
        })
                .done(function (result) {
                    listRequirements(achievement_id, "for");
                    listRequirements(achievement_id, "by");
                });
    }
}


function listActions(achievement_id) {
    $.ajax({
        method: "POST",
        url: "work/work.php",
        data: {function_to_be_called: "list_actions", achievement_id: achievement_id}
    })
            .done(function (result) {
                $("#actions" + achievement_id).html(result);
            });
}
function listCurrentActions(achievement_id) {
    console.log(achievement_id);
    $.ajax({
        method: "POST",
        url: "work/work.php",
        data: {function_to_be_called: "list_current_actions", achievement_id: achievement_id}
    })
            .done(function (result) {
                console.log(result);
                $("#list_of_current_actions" + achievement_id).html(result);
            });
}
function listNewRelations(id) {
    $.ajax({
        method: "POST",
        url: "relations.php",
        data: {function_to_be_called: "list_new"}
    })
            .done(function (result) {
                $("#list_of_new_relations" + id).html(result);
            });
}
function listNotes(achievement_id) {
    //console.log("LISTING NOTES" + achievement_id);
    $.ajax({
        method: "POST",
        url: "notes.php",
        data: {function_to_be_called: "list", achievement_id: achievement_id}
    })
            .done(function (result) {
                // console.log(result);
                $("#list_of_notes" + achievement_id).html(result);
            });

}
function listRelations(achievement_id) {
    $.ajax({
        method: "POST",
        url: "relations.php",
        data: {function_to_be_called: "list", achievement_id: achievement_id}
    })
            .done(function (result) {
                $("#list_of_relations" + achievement_id).html(result);

            });
}
function ListNewRequirements(id) {
    $.ajax({
        method: "POST",
        url: "requirements.php",
        data: {function_to_be_called: "list_new"}
    })
            .done(function (result) {
                $("#list_of_new_required_for" + id).html(result);
                $("#list_of_new_required_by" + id).html(result);
            });
}
function listRequirements(id, type) {
    $.ajax({
        method: "POST",
        url: "requirements.php",
        data: {function_to_be_called: "list_requirements", id: id, type: type}
    })
            .done(function (result) {
                $("#required_" + type + "_" + id).html(result);
            });
}

