function activateAchievement(id, parent) {
    console.log("RUN");
    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }
    data = {function_to_be_called: "activate_achievement", id: id};

    AJAXThenReload(data, parent, function () {
    });
}

function changeAuthorizingStatus(id, status){
	data={function_to_be_called:"change_authorizing_status", id:id, status:status};
	AJAXThenReload(data, id, function (result){
		console.log(result);
	});
}
function changeDescription(id, description) {
    if (!testIfVariableIsNumber(id, "id")
            || !testStringForMaxLength(description, 20000, "description")) {
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
    if (!testIfVariableIsNumber(id, "id")
            || !testIfVariableIsNumber(status, "status")) {
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
function changeDueDate(id, month, day, year, time) {
    if (!month || !day) {
        console.log("ERROR");
        return;
    }
    timestamp = year + "-" + month + "-" + day + " " + time + ":00";
    data = {function_to_be_called: "change_due_date", id: id, due: timestamp};
    AJAXThenReload(data, id, function (result) {
    });
}

function changeName(id, new_name) {
    if (!testIfVariableIsNumber(id, "id")
            || !testStringForMaxLength(new_name, 255, "new_name")
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
    if (!testIfVariableIsNumber(id, "id")
            || !testIfVariableIsNumber(new_power, "new_power")) {
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
    if (!testIfVariableIsNumber(id, "id")
            || !testIfVariableIsBoolean(new_quality, "new_quality")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_quality", id: id, new_quality: new_quality}
    })
            .done(function (result) {
                listAchievements("default", "default");
                countAchievements();
            });
}

function changeRank(id, new_rank, parent) {
    if (!testIfVariableIsNumber(id, "id")
            || !testIfVariableIsNumber(new_rank, "new_rank")
            || !testIfVariableIsNumber(parent, "parent")) {
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
                console.log(result);
                softGenericReload(parent);
            });
}
function changeWorkStatus(id, status, parent) {
    if (!testIfVariableIsNumber(id, "id")
            || !testIfVariableIsNumber(status, "status")
            || !testIfVariableIsNumber(parent, "parent")) {
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

function clearDueDate(id) {
    data = {function_to_be_called: "clear_due_date", id: id};
    AJAXThenReload(data, id, function (results) {
    });
}
function completeAchievement(id) {
    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }
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
                if (!count.working) { //public
                    $("#private_achievement_total").html("");
                    $("#working_total").html("");
                    $("#nonworking_total").html("");
                    $("#private_filtered_total").html("");
                    $("#private_achievement_count").hide();
                    $("#public_achievement_count").show();
                    $("#public_achievement_total").html(count.total);
                    if (count.filtered) {
                        $("#public_filtered_total").html("(" + count.filtered + " filtered)");
                        $("#public_filtered_total").show();
                    }
                    if (!count.filtered) {
                        $("#public_filtered_total").hide();
                    }

                } else if (count.working) { //private
                    $("#private_achievement_count").show();
                    $("#public_achievement_count").hide();
                    $("#private_achievement_total").html(count.total);

                    $("#working_total").html(count.working);
                    $("#nonworking_total").html(count.not_working);

                    if (count.filtered) {
                        $("#private_filtered_total").html("(" + count.filtered + " filtered)");
                        $("#private_filtered_total").show();
                    }
                    if (!count.filtered) {
                        $("#private_filtered_total").hide();
                    }
                }
            });

}
function createAchievement(parent, name) {
    if (!testIfVariableIsNumber(parent, "parent")
            || !testStringForMaxLength(name, 255, "name")
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

function createDocumentation(id, documentation, explanation){
    data={function_to_be_called:"create_documentation", id:id, documentation:documentation, explanation:explanation};
    AJAXThenReload(data, id, function (result){
        console.log(result);
    });     
}
function deactivateAchievement(id, parent) {
    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }

    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "deactivate_achievement", id: id}
    })
            .done(function (result) {
                softGenericReload(parent);
            });
}
function deleteAchievement(id, parent, fromProfile) {
    if (!testIfVariableIsNumber(id, "id")
            || !testIfVariableIsNumber(parent, "parent")
            || !testIfVariableIsBoolean(fromProfile, "fromProfile")) {

        return;
    }
    if (window.confirm("Are you sure you want to delete this achievement?")) {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "remove_achievement", id: id}
        })
                .done(function (result) {
                    console.log(result);
                    if (fromProfile) {
                        if (parent === 0) {
                            displayProfile(id);
                        } else if (parent > 0) {
                            displayChildren(parent);
                        }
                    } else if (fromProfile === false) {
                        listAchievements("default", "default");
                    }
                });
    }
}

function getNumOfSecondsUntilAuthorize(id,callback){
    data={function_to_be_called:"get_num_of_seconds_until_authorized", id:id};
    AJAXOnly(data, function (result){
        callback(JSON.parse(result));
    });
}
function restoreAchievement(id, parent) {
    data = {function_to_be_called: "restore_achievement", id: id};
    AJAXThenReload(data, parent, function (result) {
        console.log(result);
    });
}
function toggleDocumentationStatus(id) {

    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "toggle_documentation_status", id: id}
    })
            .done(function (result) {
                displayProfile(id);
            });
}

function toggleQuality(id) {
    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "toggle_quality", id: id}
    })
            .done(function (result) {
                softGenericReload(id);
            });
}

function toggleActiveStatus(id) {
    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "toggle_active_status", id: id}
    })
            .done(function (result) {
                softGenericReload(id);
            });
}
function toggleLockedStatus(id) {
    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "toggle_locked_status", id: id}
    })
            .done(function (result) {
                console.log(result);
                softGenericReload(id);
            });
}
function uncompleteAchievement(id) {
    if (!testIfVariableIsNumber(id, "id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "uncomplete_achievement", id: id}
    })
            .done(function (result) {
                softGenericReload(id);
            });
}
