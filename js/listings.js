function listAchievements(sort_by) {
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "list_achievements", sort_by: sort_by}
    })
            .done(function (result) {
                $("#list_of_achievements").html(result);
            });
}