function ChangeWork(id, work) {
    //console.log(id + " " + work);
    $.ajax({
        method: "POST",
        url: "work.php",
        data: {function_to_be_called: "change_work", id: id, work: work}
    })
            .done(function (result) {
                ListAllWork();
            })
}

function ListAllWork(){
    for (work=1;work<5;work++){
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
                }
            })
}

function CreateWork(achievement_id){
    $.ajax({
        method:"POST",
        url:"work.php",
        data:{function_to_be_called:"create_work", achievement_id:achievement_id}
    })
            .done (function (result){
                
    })
}
      
