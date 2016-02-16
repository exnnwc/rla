
function cancelWork(action_id) {
    if (!testIfVariableIsNumber(action_id, "action_id")) {
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "cancel_work", action_id: action_id}
    })
            .done(function (result) {
                displayWork();
            });
}

function createWork(action_id) {
    if (!testIfVariableIsNumber(action_id, "action_id")) {
        return;
    }    
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "create_work", action_id: action_id}
    })
            .done(function (result) {
                displayWork();
            });
}




