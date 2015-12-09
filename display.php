<?php
//There could be an issue where users spoof this to see other people's achievements.

$connection=new PDO("mysql:host=localhost;dbname=rla", "root", "");

//Be sure to check user's session data and page reference before commencing.

    $statement=$connection->prepare ("select * from achievements where id=?");
    $statement->bindValue(1, $_POST['id'], PDO::PARAM_INT);
    $statement->execute();    
    $achievement=$statement->fetchObject();
?>
<div>
<?php echo $achievement->name ?>
</div>
<div>
<input type='button' value="Delete" onclick="DeleteAchievement(<?php echo $_POST['id']?>)" />
</div>
<div>
<?php 
if (!$achievement->description){
echo "SUP";
} else 
var_dump($achievement);

?></div>
