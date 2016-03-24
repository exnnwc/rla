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
        <!--<script src="index.js"></script>-->
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
    <div>
        <?php list_all_published_achievements(); ?>
    </div>
    </body> 

</html>


<?php 
function list_all_published_achievements(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $user_id = fetch_current_user_id();
    $statement = $connection -> query ("select * from achievements where published!=0");
    while($achievement = $statement->fetchObject()){
        echo "<div>";
        echo !$user_id
          ? $achievement->points 
          : "<input type=\"number\" value=\"$achievement->points\" style='width:40px;text-align:center;'/>";
        echo "<a href=".SITE_ROOT."/summary/?id=$achievement->id'>$achievement->name</a>";
        if (!empty($achievement->description)){
            echo "
                <span class='hand text-button'>[ + ]</span>
            ";
        }
        echo "
            </div>";
        if (!empty($achievement->description)){
            echo "
                <div>
                    $achievement->description
                </div>";
        }

    }
}
