function AJAXThenReload(data, parent, callback){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:data
    })
        .done(function(result){
            callback(result);
            softGenericReload(parent);
        });
}
function AJAXThenList(data, callback){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:data
    })
        .done(function(result){
            callback(result);
            listAchievements("default", "default");
        });
}

function AJAXOnly(data, callback){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:data
    })
        .done(function(result){
            callback(result);
        });
}

