<html>
<head>
<style>

</style>

</head>

<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script>
function CreateAchievement(name){
    $.ajax({
        method:"POST",
        url:"achievements.php",
        data:{function_to_be_called:"create_quick", name:name}
    })
        .done (function (result){
            ListAchievements();
//            $("#error").html(result);
        });              

}
function DisplayAchievement(id){
    $.ajax({
        method:"POST",
        url:"display.php",
        data:{id:id}
    })
        .done (function (result){
            document.write(result);
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
<input id="new_achievement" type='text' onkeypress="if (event.keyCode==13){CreateAchievement(this.value);this.value='';}"/>
<input type="button" value="Quick Create" onclick="CreateAchievement($('#new_achievement')"/>
<div id="error"></div>
<div id="list_of_achievements"></div>
<?php elseif ($_GET['rla']>0):?>
<body onload="DisplayAchievement(<?php echo $_GET['rla'] ?>)">

<?php endif;?>
</body>
</html>
