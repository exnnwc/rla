function fetchRequiredFilterStatus(cb){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"fetch_required_filter_status"}
    })
        .done (function (result){
            cb(JSON.parse(result));
        });
}

function displayFilterMenu(){
    listFilterTags();
}

function filterListings(){    
    var filter_tags=[];
    $("input[name='filtered_tags']:checked").each(function (){
       filter_tags.push(Number(this.value));
    });
    required=Boolean($("#hide_required_filter:checked").length);
    filter = {filter_tags:filter_tags, required:required};
    isFilterMenuClear(filter) 
        ? listAchievements("clear", "default") 
        : listAchievements(filter, "default") ;
    
}

function isFilterActive(cb){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"is_filter_active"}
    })
        .done (function (result){
            cb(JSON.parse(result));
        });
}
function isFilterMenuClear(filter){
    if (required || filter['filter_tags'].length!=0){
        return false;
    }
    return true;
}
