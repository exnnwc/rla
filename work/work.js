function associateAchievementWithAction (achievement_id, action_id){
    $.ajax({
        method: "POST",
        url: "work.php",
        data: {function_to_be_called: "associate", achievement_id: achievement_id, action_id: action_id}
    })
            .done(function (result) {
                ListAllWork();
            });}

function ChangeWork(id, work) {
    //console.log(id + " " + work);
    $.ajax({
        method: "POST",
        url: "work.php",
        data: {function_to_be_called: "change_work", id: id, work: work}
    })
            .done(function (result) {
                ListAllWork();
            });
}
function changeWorkStatusOfAction(id, work) {
    $.ajax({
        method: "POST",
        url: "work.php",
        data: {function_to_be_called: "change_work_status_of_action", id: id, work: work}
    })
            .done(function (result) {
                ListAllWork();
            });
}
function cancelWork(action_id) {
    console.log(action_id);
        $.ajax({
        method: "POST",
        url: "work.php",
        data: {function_to_be_called: "cancel_work", action_id:action_id}
    })
            .done(function (result) {
                console.log(result);
                ListAllWork();
            });
}

function createNewAction (action){
    console.log(action);
        $.ajax({
        method: "POST",
        url: "work.php",
        data: {function_to_be_called: "create_new_action", action:action}
    })
            .done(function (result) {
                console.log(result);
                ListAllWork();
            });        
}
function DeleteAction(id, top) {
    if (window.confirm("Are you sure you want to delete this action?")) {
        if (top){
            function_name="delete_top_action";
        } else {
            function_name="delete_action";            
        }
        $.ajax({
            method: "POST",
            url: "work.php",
            data: {function_to_be_called: function_name, id: id}
        })
                .done(function (result) {
                    console.log(result);
                    ListAllWork();
                        });
    }
}
function DisplayWorkHistory() {
    
        $.ajax({
        method: "POST",
        url: "work.php",
        data: {function_to_be_called: "display_work_history"}
    })
            .done(function (result) {
                $("#work_history").html(result);
            });
}

function listAchievementsNeedingWork(){
    $.ajax({
      method:"POST",
      url:"work.php",
      data:{function_to_be_called:"list_achievements_needing_work"}
    })
            .done(function(result){
                console.log(result);
                $("#work_content").html(result);
            });
}
function ListAllWork(){
    
    for (work=0;work<5;work++){
        ListWork(work);
    }
    
}
function ListWork(work) {
    //console.log(work);
    $.ajax({
        method: "POST",
        url: "work.php",
        data: {function_to_be_called: "list_work", work:work}
    })
            .done(function (result) {
                //console.log(result);
                if (work==1){
                    $("#unassigned").html(result);
                } else if (work==2){
                    $("#daily").html(result);
                } else if (work==3){
                    $("#weekly").html(result);
                }else if (work==4){
                    $("#monthly").html(result);
                } else if (work==0){
                    $("#inactive").html(result);
                }
            });
}

function createWork(action_id){
    $.ajax({
        method:"POST",
        url:"work.php",
        data:{function_to_be_called:"create_work", action_id:action_id}
    })
            .done (function (result){
                ListAllWork();
            });
}
      
