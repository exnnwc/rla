
function changeDescription(id, description) {
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
    if ($("#achievement_name").html().trim() != new_name) {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "change_name", id: id, new_name: new_name}
        })
                .done(function (result) {
                    displayProfile(id);
                });
    }
}
function changePower(id, new_power, fromProfile) {
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_power", id: id, new_power: new_power}
    })
            .done(function (result) {
                if (fromProfile) {
                    displayProfile(id);
                } else {
                    listAchievements("default");
                }
            });

}


function changeQuality(id, new_quality){
	$.ajax({
		method:"POST",
		url:"/rla/php/ajax.php",
		data:{function_to_be_called:"change_quality", id:id, new_quality:new_quality}
	})
		.done(function(result){
			console.log(result);
			listAchievements("default");
		});
}

function changeRank(id, new_rank, fromProfile, parent) {
    if (new_rank > 0) {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "change_rank", id: id, new_rank: new_rank}
        })
                .done(function (result) {
                    console.log(result);
                    if (fromProfile) {
                        displayChildren(parent);
                    } else {
                        listAchievements("default");
                    }
                });
    }
}
function changeWorkStatus(id, status, parent) {
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_work_status", id: id, status: status}
    })
            .done(function (result) {
                if ($(document.body).attr('id') === "AchievementsList") {
                    countAchievements();
                    listAchievements("default");
                } else {
                    
                    displayProfile(id);
                }
            });
}
function countAchievements(){
	$.ajax({
		method:"POST",
		url:"/rla/php/ajax.php",
		data:{function_to_be_called:"count_achievements"}
	})
		.done (function (result){
			$("#achievement_count").html(result);
		});
}
function createAchievement(parent, name) {
    if (name.length > 255) {
        $("#error").html("This has too many characters.");
    } else if (name.trim() === "") {

    } else {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
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





function toggleWorkStatus(id, status, parent){
    if (status==5){
        status=0;
    } else {
        status++;
    }
    changeWorkStatus(id, status, parent);
    
}
