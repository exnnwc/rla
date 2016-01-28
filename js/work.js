
function cancelWork(action_id) {
    console.log(action_id);
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "cancel_work", action_id: action_id}
    })
            .done(function (result) {
                console.log(result);
                displayWork();
            });
}
function changeWork(id, work) {
    //console.log(id + " " + work);
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "change_work", id: id, work: work}
    })
            .done(function (result) {
                displayWork();
            });
}




function createWork(action_id) {
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "create_work", action_id: action_id}
    })
            .done(function (result) {
                console.log(result);
                displayWork();
            });
}




