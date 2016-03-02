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
    filtered=true;
    $("input[name='filtered_tags']:checked").each(function (){
       filter_tags.push(Number(this.value));
    });
    required=$("#hide_required_filter").is(":checked");
   
    filter = {filter_tags:filter_tags, required:required};
    
    filtered ? listAchievements(filter, "default") : listAchievements("clear", "default");
    
}