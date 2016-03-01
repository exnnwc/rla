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
function cancelToDo(achievement_id, id){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"cancel_todo", id:id}
    })
        .done(function(result){
            console.log(result);
            listToDo(achievement_id);
        });
}
function changeToDoName(achievement_id, id, name){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"change_todo_name", id:id, name:name}
    })
        .done(function(result){
            listToDo(achievement_id);    
        });
}
function completeToDo(achievement_id, id){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"complete_todo", id:id}
    })
        .done(function(result){
            console.log(result);
            listToDo(achievement_id);
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
function deleteToDo(achievement_id, id){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"delete_todo", id:id}
    })
        .done(function(result){
            console.log(result);
            listToDo(achievement_id);
        });
}
