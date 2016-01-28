function createRelation(a, b) {
    console.log (a + " " + b);
    if (a === b){
        $("#relation_error").html("Cannot make a self-referencing relation.");
    } else {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "create_relation", a: a, b: b}
        })
                .done(function (result) {
                    console.log(result);
                    if (result.substr(0, 1) == "0") {
                        $("#relation_error").html(result.substr(2, result.length));
                    } else {
                        listRelations(a);
                    }
                });
    } 
}

function deleteRelation(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this as a relationship?")) {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "delete_relation", id: id}
        })
                .done(function (result) {
                    listRelations(achievement_id);
                });
    }
}



