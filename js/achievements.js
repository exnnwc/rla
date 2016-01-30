function changeDescription(id, description) {
    testIfVariableIsNumber(id, "id");
    if (description.length > 20000) {
        //Too long
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
    testIfVariableIsNumber(id, "id");
    testIfVariableIsNumber(status, "status");
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
    testIfVariableIsNumber(id, "id");
    if ($("#achievement_name").html().trim() == new_name.trim()) {
        //ERROR Same name as before. 
    }
    if (new_name.length > 255) {
        //name too long
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
    testIfVariableIsNumber(id, "id");
    testIfVariableIsNumber(new_power, "new_power");
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
    testIfVariableIsNumber(id, "id");
    testIfVariableIsBoolean(new_quality, "new_quality");
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
    testIfVariableIsNumber(id, "id");
    testIfVariableIsNumber(new_rank, "new_rank");
    testIfVariableIsNumber(parent, "parent");
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
    testIfVariableIsNumber(id, "id");
    testIfVariableIsNumber(status, "status");
    testIfVariableIsNumber(parent, "parent");
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_work_status", id: id, status: status}
    })
            .done(function (result) {
                softGenericReload(id);
                countAchievements();
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
    testIfVariableIsNumber(parent, "parent");
    testIfVariableIsString(name, "name");
    if (name.length > 255) {
        //BAD 
        return;
    }
    if (name.trim() === "") {
        //BAD (Possibly ignore) 
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
    testIfVariableIsNumber(id, "id");
    testIfVariableIsNumber(parent, "parent");
    testIfVariableIsBoolean(fromProfile, "fromProfile");
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

function fetchMaxWorkStatus() {
    
    return $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "fetch_max_work_status"}
    });
           
           
    
}



function toggleWorkStatus(id, status, parent) {
    testIfVariableIsNumber(id, "id");
    testIfVariableIsNumber(status, "status");
    testIfVariableIsNumber(parent, "parent");
    newShit=fetchMaxWorkStatus();
    console.log(newShit.responseText);
    if (status >= fetchMaxWorkStatus()) {
        status = -1;
    }
    status++;
    console.log("new status is" + status);
    changeWorkStatus(id, status, parent);
}
