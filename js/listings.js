
function listAchievements(filter, sort_by) {
    if (!testIfVariableIsString(sort_by, "sort_by")){        
        return;
    }
    $.ajax({
        method: "POST",
        url: "/rla/php/listings.php",
        data: {filter:filter, sort_by:sort_by}
    })
            .done(function (result) {
                $("#list_of_achievements").html(result);
                countAchievements();
                isFilterActive(function(filterIsActive){
                    if (filterIsActive){
                        $("#show_filter").hide();
                        $("#hide_filter").show();
                        $("#filter_menu").show();
                    } else if (!filterIsActive){
                        $("#show_filter").show();
                        $("#hide_filter").hide();
                        $("#filter_menu").hide();
                    } 
                }); 
            });
}

function listFilterTags(){
    $.ajax({
        method: "POST",
        url: "/rla/php/ajax.php",
        data:{function_to_be_called:"list_filter_tags"}
    })
            .done(function (result) {
                $("#list_of_filter_tags").html(result);
            });
    
}
