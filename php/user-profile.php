<?php require_once("config.php");
require_once("user.php");

$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
$user_profile_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$user_id = fetch_current_user_id();
if ($user_profile_id == 0 && $user_id!=false){
    $user_profile_id = $user_id;
} else if ($user_profile_id == 0 && $user_id==false){
    return;    
}
$sign_up_date = fetch_sign_up_date($user_profile_id);
$username =  fetch_username($user_profile_id);
$count = count_achievements_for_user_profile($user_profile_id);
?>
<h1>
    <?php echo $username; ?>
</h1>
<div>
    <?php echo date("F j, Y", strtotime($sign_up_date)); ?>
</div>
<div>
    <?php echo $count["total"]; ?> Achievements 
    (<?php echo $count['public'];?> Public / <?php echo $count['completed']; ?> Completed / <?php echo $count['published']; ?> Published)
</div>
<h3 style='margin-top:32px;margin-bottom:0px;'>
    Achievements
</h3>
<div>
    <?php display_achievements_for_user_profile($user_profile_id, $count); ?>
</div>


<?php

function display_achievements_for_user_profile($user_id, $count){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement_set=false;
    $statement=$connection->prepare("select * from achievements where deleted=0 and parent=0 and public=1 and owner=? order by name"); 
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement = $statement->fetchObject()){
        $achievement_set=true;
        echo "<div style='padding:8px;"; 
        if ($achievement->completed!=0){
            echo "background-color:lightgrey;";
        } else if ($achievement->published!=0){
            echo "background-color:green;";
        }
        echo "'><a href='".SITE_ROOT."/summary/?id=$achievement->id'>$achievement->name</a>";
        if ($achievement->completed!=0){
           echo " <span style='font-style:italic;'>Completed " . date("m/d/y", strtotime($achievement->created)) . "</span>"; 
        } else if ($achievement->published!=0){
            echo "<span style='font-style:italic;'>Published " . date("m/d/y", strtotime($achievement->created)) . "</span>";
        }

        echo "</div>";
    }
    if (!$achievement_set){
        echo "<div style='padding:8px;'>"; 
        echo $count["total"]!=0
          ? "No public achievements."
          : "None.";
        echo "</div>";
    }
}
