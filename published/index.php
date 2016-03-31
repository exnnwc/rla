<?php
    require_once ("../php/config.php"); 
    require_once ("../php/user.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/rla.css">
        <!--Replace this with a web link when the site goes live.-->
        <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>
        <script src="index.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/achievements.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/actions.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/ajax.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/display.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/error.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/filter.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/global.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/listings.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/profile.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/requirements.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/notes.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/tags.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/todo.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/user.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/work.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/relations.js"></script>        
        <title><?PHP echo SITE_NAME ?></title>
    </head>
    <body>
        <?php 
        include("../templates/navbar.php"); 
        include("../templates/login.php"); 
        ?>
    <h2 style="clear:both;">
        Published Achievements
    </h2>
<!--
    <div>
        Tags: <span id='publishing_tags'></span>
    </div>
-->
    <div>
        <?php list_all_published_achievements(); ?>
    </div>
    </body> 

</html>


<?php 
function list_all_published_achievements(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement_set=false;
    $user_id = fetch_current_user_id();
    $statement = $connection -> query ("select * from achievements where deleted=0 and parent=0 and published!=0 order by points desc");
    $margin_top = ($user_id===false)
      ? "0"
      : "16";
    while($achievement = $statement->fetchObject()){
        $achievement_set=true;
        echo "<div style='clear:both;padding-top:16px;'>
            <div id='".$achievement->id."points_error' style='color:red;'></div>
            <div style='float:left;width:50px;text-align:center;'>";
        echo !$user_id
          ? $achievement->points 
          : "<div id='upvote$achievement->id' class='hand upvote' style='text-align:center;font-weight:bold;color:grey;' class='hand'>&uarr;</div>
             <div style='text-align:center;font-size:12px;color:grey;'>($achievement->points)</div>
             <div id='downvote$achievement->id' class='hand downvote' style='text-align:center;font-weight:bold;color:grey;' class='hand'>&darr;</div>";
        echo "</div> 
            
                <div style='float:left;padding-top:".$margin_top."px;padding-left:16px;'>
                    <a href='".SITE_ROOT."/summary/?id=$achievement->id'>$achievement->name</a>
                    <span class='text-button' style='font-style:italic;'>";
        if  ($user_id===$achievement->owner){
            if ($achievement->disowned==0){
                echo "Self-published";
            } else if ($achievement->disowned==1){
                echo "You abandoned this achievement.";
            }           
        } else if  ($user_id!==$achievement->owner){
            if ($achievement->disowned==0){
                echo "Published by <a href='".SITE_ROOT . "/user/?id=". $achievement->owner . "' class='user-link'>" . fetch_username($achievement->owner) . "</a>";
            } else if ($achievement->disowned==1){
                echo "Abandoned by publisher.";
            }

        }
        echo "</span>";
        if (!empty($achievement->description)){
            echo "
                <span class='hand text-button'>[ + ]</span>
            ";
        }

        //16px
        echo "
            </div></div>";
        if (!empty($achievement->description)){
            echo "
                <div>
                    $achievement->description
                </div>";
        }

    }
    if (!$achievement_set){
        echo "No achievements have been published.";
    }
}
