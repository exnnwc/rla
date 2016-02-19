function displayChildren(parent) {
    if (!testIfVariableIsNumber(parent, "parent")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "list_children", parent: parent}
    })
            .done(function (result) {
                $("#child_achievements_of_" + parent).html(result);
            });

}
function displayProfile(id) {
    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "is_it_active", id: id}
    })
            .done(function (result) {
                if (result === "1") {
                    $.ajax({
                        method: "POST",
                        url: "/rla/php/profile.php",
                        data: {id: id}
                    })
                            .done(function (result) {
                                $("#achievement_profile").html(result);
                                listActions(id);
                                listNewActions(id);
                                listRequirements(id, "for");
                                listRequirements(id, "by");
                                displayChildren(id);
                                listRelations(id);
                                listNewRelations(id);
                                listNotes(id);
                            });
                } else if (result == "0") {
                    $("#achievement_profile").html("This achievement has been deleted.");
                } else {
                    $("#achievement_profile").html("This profile does not exist. You should not being see this message. Delete this before production.");
                }
            });
}
function listActions(achievement_id) {
    if (!testIfVariableIsNumber(achievement_id, "achievement_id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "list_actions", achievement_id: achievement_id}
    })
            .done(function (result) {
                $("#actions" + achievement_id).html(result);
            });
}
function listNewActions(achievement_id) {
    if (!testIfVariableIsNumber(achievement_id, "achievement_id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "list_new_actions", achievement_id: achievement_id}
    })
            .done(function (result) {
                $("#list_of_current_actions" + achievement_id).html(result);
            });
}
function listAllActions(achievement_id) {
    listActions(achievement_id);
    listNewActions(achievement_id);
}
function listNewRelations(id) {
    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "list_new_relations"}
    })
            .done(function (result) {
                $("#list_of_new_relations" + id).html(result);
            });
}
function listNewRequirements(id) {
    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "list_new_requirements"}
    })
            .done(function (result) {
                $("#list_of_new_required_for" + id).html(result);
                $("#list_of_new_required_by" + id).html(result);
            });
}
function listNotes(achievement_id) {
    if (!testIfVariableIsNumber(achievement_id, "achievement_id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "list_notes", achievement_id: achievement_id}
    })
            .done(function (result) {
                $("#list_of_notes" + achievement_id).html(result);
            });

}

function listRelations(achievement_id) {
    if (!testIfVariableIsNumber(achievement_id, "achievement_id")) {
        return;
    }    
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "list_relations", achievement_id: achievement_id}
    })
            .done(function (result) {
                $("#list_of_relations" + achievement_id).html(result);

            });
}

function listRequirements(id, type) {
    if (!testIfVariableIsNumber(id, "id") || !testIfVariableIsString(type, "type")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "list_requirements", id: id, type: type}
    })
            .done(function (result) {
                $("#required_" + type + "_" + id).html(result);
            });
}
