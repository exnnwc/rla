function changeDescription(id, description) {
    if (testIfVariableIsNumber(id, "id")
            || testStringForMaxLength(description, 20000, "description")) {
        return;
    }

    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_description", id: id, description: description}
    })
            .done(function (result) {
                displayProfile(id);
            });
}

function changeDocumentationStatus(id, status) {
    if (testIfVariableIsNumber(id, "id")
            || testIfVariableIsNumber(status, "status")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_documentation_status", id: id, status: status}
    })
            .done(function (result) {
                displayProfile(id);
            });
}

function changeName(id, new_name) {
    if (testIfVariableIsNumber(id, "id")
            || testStringForMaxLength(new_name, 255, "new_name")
            || $("#achievement_name").html().trim() == new_name.trim()) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_name", id: id, new_name: new_name}
    })
            .done(function (result) {
                displayProfile(id);
            });
}

function changePower(id, new_power) {
    if (testIfVariableIsNumber(id, "id")
            || testIfVariableIsNumber(new_power, "new_power")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_power", id: id, new_power: new_power}
    })
            .done(function (result) {
                softGenericReload(id);
            });
}

function changeQuality(id, new_quality) {
    if (testIfVariableIsNumber(id, "id")
            || testIfVariableIsBoolean(new_quality, "new_quality")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_quality", id: id, new_quality: new_quality}
    })
            .done(function (result) {
                listAchievements("default");
                countAchievements();
            });
}

function changeRank(id, new_rank, parent) {
    if (testIfVariableIsNumber(id, "id")
            || testIfVariableIsNumber(new_rank, "new_rank")
            || testIfVariableIsNumber(parent, "parent")) {
        return;
    }
    if (new_rank == 0) {
        //ERROR new_rank should never be 0
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_rank", id: id, new_rank: new_rank}
    })
            .done(function (result) {
                softGenericReload(parent);
            });
}
function changeWorkStatus(id, status, parent) {
    if (testIfVariableIsNumber(id, "id")
            || testIfVariableIsNumber(status, "status")
            || testIfVariableIsNumber(parent, "parent")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_work_status_of_achievement", id: id, status: status}
    })
            .done(function (result) {
                softGenericReload(id);
                countAchievements();
            });
}

function completeAchievement(id) {
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "complete_achievement", id: id}
    })
            .done(function (result) {
                softGenericReload(id);
            });
}

function countAchievements() {
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "count_achievements"}
    })
            .done(function (result) {
                count = JSON.parse(result);
                $("#working_total").html(count.working);
                $("#quality_total").html(count.qualities);
                $("#nonworking_total").html(count.not_working);
                $("#achievement_total").html(count.total);
            });

}
function createAchievement(parent, name) {
    if (testIfVariableIsNumber(parent, "parent")
            || testStringForMaxLength(name, 255, "name")
            || name.trim() === "") {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "create_achievement", parent: parent, name: name}
    })
            .done(function (result) {
                softGenericReload(parent);
            });
}

function deleteAchievement(id, parent, fromProfile) {
    if (testIfVariableIsNumber(id, "id")
            || testIfVariableIsNumber(parent, "parent")
            || testIfVariableIsBoolean(fromProfile, "fromProfile")) {
        return;
    }
    if (window.confirm("Are you sure you want to delete this achievement?")) {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "delete_achievement", id: id}
        })
                .done(function (result) {
                    if (fromProfile) {
                        if (parent === 0) {
                            displayProfile(id);
                        } else if (parent > 0) {
                            displayChildren(parent);
                        }
                    } else if (fromProfile === false) {
                        listAchievements("default");
                    }
                });
    }
}

function toggleWorkStatus(id, status, parent) {
    //FIX I'd like to implement this AJAX call as a separate function 
    //but this is a quick fix that I don't imagine will have negative repercussions.    
    if (testIfVariableIsNumber(id, "id")
            || testIfVariableIsNumber(status, "status")
            || testIfVariableIsNumber(parent, "parent")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "fetch_max_work_status"}
    })
            .done(function (result) {
                if (status >= JSON.parse(result)) {
                    status = -1;
                }
                status++;
                console.log("new status is" + status);
                changeWorkStatus(id, status, parent);
            });
}

function uncompleteAchievement(id) {
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "uncomplete_achievement", id: id}
    })
            .done(function (result) {
                softGenericReload(id);
            });
}