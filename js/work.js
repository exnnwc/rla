
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





function createWork(action_id) {
    var data;
    if ($("#action_quantity_"+action_id).val()){
        data={quantity:$("#action_quantity_"+action_id).val()};
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data: {function_to_be_called: "create_work", action_id: action_id, data:data}
    })
            .done(function (result) {
                console.log(result);
                displayWork();
            });
}




