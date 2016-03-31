
<?php 
require_once($_SERVER['DOCUMENT_ROOT'] ."/rla/php/achievements.php");
require_once ($_SERVER['DOCUMENT_ROOT'] ."/rla/php/config.php"); 
require_once($_SERVER['DOCUMENT_ROOT'] ."/rla/php/tags.php");
require_once($_SERVER['DOCUMENT_ROOT'] ."/rla/php/user.php");
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

include($_SERVER['DOCUMENT_ROOT'] ."/rla/templates/navbar.php"); 
include($_SERVER['DOCUMENT_ROOT'] ."/rla/templates/login.php"); 
if ($user_id!=false): ?>
<div style='clear:both;margin-bottom:16px;padding-top:16px;'>
    <span style='color:black;font-weight:bold;'> [ All ] </span>
    <span style='color:black;'> [ Work In Progress ] </span>
    <span style='color:grey;'>[ Completed ]</span>
    <span style='color:green;'>[ Published ]</span>
</div>
<?php endif; ?>



<div style='clear:both;'>
<?php
($user_id==false)
  ? display_landing_page()
  : display_all_public_achievements(0);
?>
</div>

</body>
</html>


<?php 

function display_all_public_achievements($filter){
    switch ($filter){  
        case 0:
            $where = "";
            break;
        case 1:
            $where = " and completed=0 and published=0"; 
            break;
        case 2:
            $where = " and completed!=0"; 
            break;
        case 3:
            $where = " and published!=0"; 
            break;
    }
	$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("select * from achievements where deleted=0 and parent=0 and public=1"); 
    while ($achievement = $statement->fetchObject()){
        echo "  <div style='clear:both;";

        echo    "'>
                    <div style='float:left;width:600px;padding:4px;'>
                        <a href='".SITE_ROOT."/summary/?id=".$achievement->id."' style='";
        if ($achievement->published!=0){
            echo "color:green;";
        } else if ($achievement->completed!=0){
            echo "color:grey;";
        } else{
            echo "color:black";
        }
        echo "          '>$achievement->name</a> 
                    </div><div style='float:left;padding:4px;'>";
                    if ($achievement->disowned==0){
                    echo "<a style='float:right;margin-right:256px;' class='user-link' href='".SITE_ROOT."/user/?id=".$achievement->owner."'>".fetch_username($achievement->owner)."</a>";
                    } else if ($achievement->disowned==1){
                        echo "<span class='user-link' style='font-weight:bold;'>Abandoned</span>";
                    }
        echo "
                    </div>
                </div>";
    }
}

function display_landing_page(){
    require_once("templates/landing.php");
}
