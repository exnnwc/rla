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
        url: "/rla/php/queue.php",
    })
            .done(function (result) {
                $("#work_content").html(result);
            });
}