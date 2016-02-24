function createTag(id, name){

    $.ajax({
       method: "POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"create_tag", id:id, name:name}
    })
        .done(function (result){
            displayProfile(id);
        });   
}

function deleteTag(id, achievement_id){
    $.ajax({
        method: "POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"delete_tag", id:id}
    })
        .done(function(result){
            displayProfile(achievement_id);
        });
}
