function createToDo(achievement_id){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"create_todo", achievement_id:achievement_id}
    })
        .done(function(result){
            console.log(result);
        });
}

function listToDo(achievement_id){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"list_todo", achievement_id:achievement_id}
    })
        .done(function(result){
            $("#todo_list").html(result);
        });
}
