<?php
require_once("../php/achievements.php");
require_once("../php/config.php");
require_once("../php/user.php");
?>
<html>
<head>

        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/rla.css">
        <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/global.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/user.js"></script>
</head>
<body>

	<div style='float:right;font-size:12px;text-align:right;'>
		<?php if (!isset($_SESSION['user'])): ?>
		Not logged in.
		<a href='signup/' class='text-button' style='margin-left:2px;font-size:12px;float:right;'>[ Sign Up ]</a> 
		<span id='show_login' class='hand text-button' 
			style='margin-left:4px;font-size:12px;float:right;'>[ Login ]</span>
		<div id="login_form" style='margin-top:16px;display:none;'>
			<?php require ("../login/login.htm"); ?> 
		</div>
		<?php elseif (fetch_current_user_id()!=false): ?>
			Logged in as <?php echo fetch_username(fetch_current_user_id()) . ". (" . fetch_user_points(fetch_current_user_id()) . ")"; ?> 
			<span id='logout' class='hand text-button'> [ Logout ] </span>
		<?php endif; ?>
	</div>
<?php

    $id = isset($_GET['id']) 
        ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) 
        : 0;
    if ($id == 0):?>
        <h3>
            Past Votes
        </h3>
        <div>
            <?php display_votes(); ?>
        </div>
        <h3>
            Submitted Achievements
        </h3>
        <div>
           
        </div>
    <?php endif; ?>
</body>
</html>

<?php
function display_votes(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
	$user_id = fetch_current_user_id();
	if ($user_id==false){
        echo "You must be logged in to view this.";
		return;
	} 
    $statement = $connection->prepare("select * from votes where user_id=?");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->execute();
    $old_date=0;
    while ($vote=$statement->fetchObject()){
        $achievement = fetch_achievement($vote->achievement_id);
        $date = date("m/d/y", strtotime($vote->created)); 
        if ($date!=$old_date){
            echo "<div style='margin-top:16px;'>$date</div>";
            $old_date = $date;
        }
        echo "<div>".
        date("g:iA", strtotime($vote->created)) . " - $achievement->name - " . fetch_username($achievement->owner) ;
        echo $vote->vote
          ? " - For"
          : " - Against";
        echo "</div>";
    }
}
