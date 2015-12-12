<?php
$pref_date_format="F j, Y g:i:s";

//There could be an issue where users spoof this to see other people's achievements.
//Be sure to check user's session data and page reference before commencing.


$connection=new PDO("mysql:host=localhost;dbname=rla", "root", "");
$statement=$connection->prepare ("select * from achievements where id=? and active=1");
$statement->bindValue(1, $_POST['id'], PDO::PARAM_INT);
$statement->execute();    
$achievement=$statement->fetchObject();
?>
<div>
<a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/rla/">Back</a>
</div>
<h1> 

<?php
echo $achievement->name;
?> 

</h1>
<div>
<input type='button' value='Delete' onclick="DeleteAchievement( 
<?php echo $achievement->id; ?> , 
<?php echo $achievement->parent; ?>
, true)" />
</div>
<div>
Created:

<?php
echo date($pref_date_format, strtotime($achievement->created))
?>

</div>
<div>

<?php
echo ($achievement->completed!=0) ? "Completed:".date($pref_date_format, strtotime($achievement->completed)) : "";
?>

</div>
<div>

<?php
echo $achievement->documented 
    ? "Documented (Requires proof of completion)".display_documentation_menu($achievement->id, 0) 
    : "Undocumented (No proof of completion required)". display_documentation_menu($achievement->id, 1);
?>

</div>
<div>
<h3>
Category <input type="button" value="Edit" />
</h3>
<div style="display:none;" />
<div><input type='radio' />None</div>
<div style='margin-left:30px;'>N/A</div>
<?php
display_categories($achievement->category);
?>
</div>


<div>
<?php
echo $achievement->category ? $achievement->category : "None selected.";

?>
</div>
<h3>
Description
<input type='button' value='Edit' onclick="$('#current_description').hide(); $('#new_description_input').show()"/>
</h3>
<span id="current_description">
<?php
echo $achievement->description ? str_replace("\n", "<BR>", $achievement->description) : "There is no description.";
?>
</span>
<span id="new_description_input" style="display:none">
<textarea id="new_description" style="width:600px;height:150px;">
<?php
echo $achievement->description ? $achievement->description : "";    
?>
</textarea>
<input type='button' value='Submit' onclick="ChangeDescription(<?php echo $achievement->id;?>,$('#new_description').val())" />
</span>
</div>


<div>
<h3>
Sub-Achievements
</h3>
<input id="new_achievement<?php echo $achievement->id; ?>" type='text' onkeypress="if (event.keyCode==13){CreateAchievement(<?php echo $achievement->id; ?>, this.value);this.value='';}"/>
<input type="button" value="Quick Create" onclick="CreateAchievement(<?php echo $achievement->id; ?>, $('#new_achievement<?php echo $achievement->id; ?>')"/>
<div id='child_achievements_of_<?php echo $achievement->id; ?>'></div>
</div>
<?php

function display_documentation_menu($id, $status){
    $menu = "<input type='button' value='Switch to ";
    if ($status){
        $menu=$menu . "documented";
    } else{
        $menu=$menu . "undocumented";
    }
    $menu=$menu."' onclick=\"ChangeDocumentationStatus($id, $status)\" />";
    return $menu;
}

function display_categories($active_category){
    
}
?>
