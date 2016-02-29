function displayFilterMenu(){
    listFilterTags();
}

function filterListings(){    
    var filter_tags=[];
    $("input[name='filtered_tags']:checked").each(function (){
       filter_tags.push(Number(this.value));
    });
    filter = {filter_tags:filter_tags};
    listAchievements(filter, "default");
}
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
