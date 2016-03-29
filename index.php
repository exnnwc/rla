
<?php 
require_once ("php/config.php"); 
require_once("php/tags.php");
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
        <style>
            a{
                color:black;
                text-decoration:none;
            }
            a:hover{
                text-decoration:underline;
            }
        </style>
    </head>
<body>
<?php
include("templates/navbar.php"); 
include("templates/login.php"); 
?>
<div style='clear:both;'>
<?php
($user_id==false)
  ? display_landing_page()
  : display_all_public_achievements();
?>
</div>

</body>
</html>


<?php 

function display_all_public_achievements(){
	$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("select * from achievements where deleted=0 and parent=0 and public=1");
    while ($achievement = $statement->fetchObject()){
        echo "  <div style='clear:both;'>
                    <div style='float:left;width:600px;padding:4px;";
        if ($achievement->published!=0){
            echo "background-color:green;";
        } else if ($achievement->completed!=0){
            echo "background-color:grey;";
        }
        echo "        '>
                    <a href='".SITE_ROOT."/summary/?id=".$achievement->id."'>$achievement->name</a> 
                    </div><div style='float:left;'>
                    <a style='float:right;margin-right:256px;' class='user-link' href='".SITE_ROOT."/user/?id=".$achievement->owner."'>".fetch_username($achievement->owner)."</a>
                    </div>
                </div>";
    }
}

function display_landing_page(){
    echo "That shit cray.";
}
