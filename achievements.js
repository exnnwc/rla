function changeDescription(id, description) {
    //An issue with comparing the old and new description is that I add <BR> to \newlines so I'd have to reverse those additions.
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "change_description", id: id, description: description}
    })
            .done(function (result) {
                displayAchievement(id);
            });
}
function changeDocumentationStatus(id, status) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "change_documentation_status", id: id, status: status}
    })
            .done(function (result) {
                displayAchievement(id);
            });
}
function changeName(id, new_name) {
    if ($("#achievement_name").html().trim() != new_name) {
        $.ajax({
            method: "POST",
            url: "achievements.php",
            data: {function_to_be_called: "change_name", id: id, new_name: new_name}
        })
                .done(function (result) {
                    displayAchievement(id);
                });
    }
}
function changePower(id, new_power, fromProfile) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "change_power", id: id, new_power: new_power}
    })
            .done(function (result) {
                if (fromProfile) {
                    displayAchievement(id);
                } else {
                    listAchievements("default");
                }
            });

}
function changeRank(id, new_rank, fromProfile, parent) {
    if (new_rank > 0) {
        $.ajax({
            method: "POST",
            url: "achievements.php",
            data: {function_to_be_called: "change_rank", id: id, new_rank: new_rank}
        })
                .done(function (result) {
                    if (fromProfile) {
                        displayChildren(parent);
                    } else {
                        listAchievements("default");
                    }
                });
    }
}

function createAchievement(parent, name) {
    if (name.length > 255) {
        $("#error").html("This has too many characters.");
    } else if (name.trim() === "") {

    } else {
        $.ajax({
            method: "POST",
            url: "achievements.php",
            data: {function_to_be_called: "create_achievement", parent: parent, name: name}
        })
                .done(function (result) {
                    if (result.substr(0, 1) === "0") {
                        $("#error").html(result.substr(2, result.length - 2));
                    } else {
                        if (parent === 0) {
                            listAchievements("default");
                        } else if (parent > 0) {
                            displayChildren(parent);
                        }
                    }
                });
    }
}

function deleteAchievement(id, parent, fromProfile) {
    if (window.confirm("Are you sure you want to delete this achievement?")) {
        $.ajax({
            method: "POST",
            url: "achievements.php",
            data: {function_to_be_called: "delete_achievement", id: id}
        })
                .done(function (result) {
                    if (fromProfile) {
                        if (parent === 0) {
                            displayAchievement(id);
                        } else if (parent > 0) {
                            displayChildren(parent);
                        }
                    } else if (fromProfile === false) {
                        listAchievements("default");
                    }
                });
    }
}

function displayAchievement(id) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "is_it_active", id: id}
    })
            .done(function (result) {
                if (result === "1") {
                    $.ajax({
                        method: "POST",
                        url: "display.php",
                        data: {id: id}
                    })
                            .done(function (result) {
                                $("#achievement_profile").html(result);
                                listActions(id);
                                listCurrentActions(id);
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
                    $("#achievement_profile").html("This profile does not exist.");
                }
            });
}

function displayChildren(parent) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "list_children", parent: parent}
    })
            .done(function (result) {
                $("#child_achievements_of_" + parent).html(result);
            });

}

function listAchievements(sort_by) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "list_achievements", sort_by: sort_by}
    })
            .done(function (result) {
                $("#list_of_achievements").html(result);
            });
}

function changeWorkStatus(id, status, parent) {
    $.ajax({
        method: "POST",
        url: "achievements.php",
        data: {function_to_be_called: "change_work_status", id: id, status: status}
    })
            .done(function (result) {
                if ($(document.body).attr('id') === "AchievementsList") {
                    listAchievements("default");
                } else {
                    displayAchievement(id);
                }
            });
}

function toggleWorkStatus(id, status, parent){
    if (status==4){
        status=0;
    } else {
        status++;
    }
    changeWorkStatus(id, status, parent);
    
}