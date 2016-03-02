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
