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
    var show_only=[];
    $("input[name='show_only_filter']:checked").each(function (){
        show_only.push(this.value);
    });
    console.log(show_only.length);
    if (show_only.indexOf("locked")>-1 && show_only.indexOf("unlocked")>-1){
        show_only.splice(show_only.indexOf("locked"), 1);
        show_only.splice(show_only.indexOf("unlocked"), 1);    
    }
    console.log(show_only.length);
    $("input[name='filtered_tags']:checked").each(function (){
       filter_tags.push(Number(this.value));
    });
    required=Boolean($("#required_filter_checkbox:checked").length);
    filter = {filter_tags:filter_tags, required:required , show_only:show_only};
    isFilterMenuClear(filter) 
        ? listAchievements("clear", "default") 
        : listAchievements(filter, "default") ;
    
}

function isFilterActive(cb){
    $.ajax({
        method:"POST",
        url:"/rla/php/ajax.php",
        data:{function_to_be_called:"echo_if_filter_active"}
    })
        .done (function (result){
            cb(JSON.parse(result));
        });
}
function isFilterMenuClear(filter){
    if (filter['required'] || filter['show_only'].length>0 || filter['filter_tags'].length!=0){
        return false;
    }
    return true;
}
