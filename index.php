
<?php 
require_once($_SERVER['DOCUMENT_ROOT'] ."/rla/php/achievements.php");
require_once ($_SERVER['DOCUMENT_ROOT'] ."/rla/php/config.php"); 
require_once($_SERVER['DOCUMENT_ROOT'] ."/rla/php/user.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/css/rla.css">
        <!--Replace this with a web link when the site goes live.-->
        <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/ajax.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/error.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/global.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/user.js"></script>
        <title><?PHP echo SITE_NAME ?></title>
    </head>
<body>
<?php

include($_SERVER['DOCUMENT_ROOT'] ."/rla/templates/navbar.php"); 
include($_SERVER['DOCUMENT_ROOT'] ."/rla/templates/login.php"); 
if ($user_id!=false): ?>
<h2 style='clear:both;padding-top:16px;margin-bottom:4px;'>
All Public Achievements
</h2>
<div style='margin-bottom:16px;'>
    <span class='hand'> [ All ] </span>
    <span class='hand'> [ Work In Progress ] </span>
    <span class='hand completed'>[ Completed ]</span>
    <span class='hand published'>[ Published ]</span>
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
    $statement = $connection->query("select * from achievements where deleted=0 and parent=0 and public=1 $where"); 
    while ($achievement = $statement->fetchObject()){
        echo "  <div class='achievement-row'>
                    <div class='achievement-name'>
                        <a href='".SITE_ROOT."/summary/?id=".$achievement->id."' class='";
        if ($achievement->published!=0){
            echo " published";
        } else if ($achievement->completed!=0){
            echo " completed";
        } 
        echo "          '>
                            $achievement->name
                        </a> 
                    </div>
                    <div class='achievement-owner'>";
                    if ($achievement->disowned==0){
                        echo "
                        <a class='user-link' href='".SITE_ROOT."/user/?id=".$achievement->owner."'>"
                        . fetch_username($achievement->owner)
                        ."</a>";
                    } else if ($achievement->disowned==1){
                        echo "<span class='user-link' style='font-weight:bold;'>Abandoned</span>";
                    }
        echo "      </div>
                </div>";
    }
}

function display_landing_page(){
    require_once("templates/landing.php");
}
