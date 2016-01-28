function displayWork() {
    displayQueue();
    displayHistory();
}
function displayHistory() {

    $.ajax({
        method: "POST",
        url: "/rla/php/history.php",
    })
            .done(function (result) {
                $("#work_history").html(result);
            });
}

function displayQueue() {
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "display_queue"}
    })
            .done(function (result) {
                //console.log(result);
                $("#work_content").html(result);
            });
}