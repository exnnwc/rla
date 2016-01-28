function associateAchievementWithAction(achievement_id, action_id) {
    $.ajax({
        method: "POST",
        url: "ajax.php",
        data: {function_to_be_called: "associate", achievement_id: achievement_id, action_id: action_id}
    })
            .done(function (result) {
                displayWork();
            });
}

function changeWorkStatusOfAction(id, work) {
    $.ajax({
        method: "POST",
        url: "ajax.php",
        data: {function_to_be_called: "change_work_status_of_action", id: id, work: work}
    })
            .done(function (result) {
                displayWork();
            });
}
function createAction(achievement_id, action) {
    //console.log(achievement_id + " " + action);
    if (!$('#list_of_current_actions' + achievement_id + ' option:selected').val()) {
        reference = 0;

    } else {
        reference = $('#list_of_current_actions' + achievement_id).val();
    }
    // console.log ($('#list_of_current_actions'+achievement_id).val() + " " + achievement_id + " " + action);
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "create_action", achievement_id: achievement_id, action: action, reference: reference}
    })
            .done(function (result) {
                //console.log (result);
                $.getScript("/rla/profile.js", function(){
                    listActions(achievement_id);
                });
                
            });
}
function createNewAction(action) {
    console.log(action);
    $.ajax({
        method: "POST",
        url: "ajax.php",
        data: {function_to_be_called: "create_new_action", action: action}
    })
            .done(function (result) {
                console.log(result);
                displayWork();
            });
}

function deleteAction(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this action?")) {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "delete_action", id: id}
        })
                .done(function (result) {
                    listActions(achievement_id);
                });
    }
}


