<?php

$pref_date_format="F j, Y g:i:s";
//There could be an issue where users spoof this to see other people's achievements.

$connection=new PDO("mysql:host=localhost;dbname=rla", "root", "");

//Be sure to check user's session data and page reference before commencing.
$statement=$connection->prepare ("select * from achievements where id=?");
$statement->bindValue(1, $_POST['id'], PDO::PARAM_INT);
$statement->execute();    
$achievement=$statement->fetchObject();
?>
<h1> 
<?php
echo $achievement->name 
?> 
</h1>
<div>
<input type='button' value='Delete' onclick="DeleteAchievement(<?php echo $_POST['id'];?>)" />
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
<?php
echo $achievement->description ? $achievement->description : "There is no description.";
?>
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
?>
