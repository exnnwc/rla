<html>
<head>
<style>

</style>

</head>

<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script>
function ChangeDescription(id, description){
    $.ajax({
        method:"POST",
        url:"achievements.php",
        data:{function_to_be_called:"change_description", id:id, description:description}
    })
        .done (function (result){
            DisplayAchievement(id);
        });              
    
    
}
function ChangeDocumentationStatus(id, status){
    $.ajax({
        method:"POST",
        url:"achievements.php",
        data:{function_to_be_called:"change_documentation_status", id:id, status:status}
    })
        .done (function (result){
            DisplayAchievement(id);
        });              
    
}
function CreateAchievement(parent, name){

    $.ajax({
        method:"POST",
        url:"achievements.php",
        data:{function_to_be_called:"create_quick", parent:parent, name:name}
    })
        .done (function (result){
            if (parent==0){
                ListAchievements();

            } else if (parent>0){
                DisplayAchievement(parent);

            } else {
                document.write("2");
            }            
        });              

}
function DeleteAchievement(id, parent, fromProfile){
if (window.confirm("Are you sure you want to delete this achievement?")){
    $.ajax({
        method:"POST",
        url:"achievements.php",
        data:{function_to_be_called:"delete", id:id}
    })
        .done (function (result){
                if (fromProfile==true)
					//Need to include code to make a distinction between the parent and child.
                if (parent==0){
                    DisplayAchievement(id);
                } else if (parent>0){
                    DisplayAchievement(parent);
                }
                else if (fromProfile==false){
                    ListAchievements();
                }

        });   
}
}
function DisplayAchievement(id){

   $.ajax({
        method:"POST",
        url:"achievements.php",
        data:{function_to_be_called:"is_it_active", id:id}
    })
        .done (function (result){
            if (result=="1"){
                    $.ajax({
                        method:"POST",
                        url:"display.php",
                        data:{id:id}
                    })
                        .done (function (result){
                            $("#achievement_profile").html(result);
										    $.ajax({
												method:"POST",
												url:"achievements.php",
												data:{function_to_be_called:"list_children", parent:id}
											})
												.done (function (result){
													$("#child_achievements_of_"+id).html(result);
													
												}); 
                        });
            } else if (result=="0"){
                $("#achievement_profile").html("This achievement has been deleted.");
            } else {
                $("#achievement_profile").html("This profile does not exist.");
            }
        }); 

}

function IsItActive(id){

   $.ajax({
        method:"POST",
        url:"achievements.php",
        data:{function_to_be_called:"is_it_active", id:id}
    })
        .done (function (result){
                $("#achievement_profile").html(typeof result);
        });
}
function ListAchievements(){
    $.ajax({
        method:"POST",
        url:"achievements.php",
        data:{function_to_be_called:"list"}
    })
        .done (function (result){
            $("#list_of_achievements").html(result);
        });      

}

</script>
<?php
if (!isset($_GET['rla'])){
    $_GET['rla']=0;
} else {
    $_GET['rla']=(int)$_GET['rla'];
}
if ($_GET['rla']==0):?>
<body onload="ListAchievements();">
<input id="new_achievement" type='text' onkeypress="if (event.keyCode==13){CreateAchievement(0, this.value);this.value='';}"/>
<input type="button" value="Quick Create" onclick="CreateAchievement(0, $('#new_achievement')"/>
<div id="error"></div>
<div id="list_of_achievements"></div>
<?php elseif ($_GET['rla']>0):?>
<body onload="DisplayAchievement(<?php echo $_GET['rla'];?>);ListChildrenOf(<?php echo $_GET['rla'];?>)">
<div id="error"></div>
<div id="achievement_profile"></div>
<?php endif;?>
</body>
</html>
