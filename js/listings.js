
function listAchievements(filter, sort_by) {
    if (!testIfVariableIsString(sort_by, "sort_by")){        
        return;
    }
    $("input[name='filtered_tags']:checked").each(function (){
        tag=this.id.substr(10, this.id.length-(10+9));
        $("#filter_by_" + tag + "_text_button").addClass("active-filter");
        $("#filter_by_" + tag + "_text_button").addClass("active-tag");
    });
    $("input[name='filtered_tags']:not(:checked)").each(function (){
        tag=this.id.substr(10, this.id.length-(10+9));
        $("#filter_by_" + tag + "_text_button").removeClass("active-filter");
        $("#filter_by_" + tag + "_text_button").removeClass("active-tag");
    });
    $.ajax({
        method: "POST",
        url: "/rla/php/listings.php",
        data: {filter:filter, sort_by:sort_by}
    })
            .done(function (result) {
                $("#list_of_achievements").html(result);
                fetchRequiredFilterStatus(function (required_filter_status) {
                    if (required_filter_status==="true"){
                        $("#hide_required_filter").prop("checked", true);
                        $("#required_filter_caption").addClass("active-filter");
                    } else if (required_filter_status==="false"){
                        $("#hide_required_filter").prop("checked", false);
                        $("#required_filter_caption").removeClass("active-filter");
                    }
                });
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
