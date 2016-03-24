<?php
	require_once("../php/config.php");
	require_once("../php/display.php");
	require_once("../php/user.php");
    require_once("../php/votes.php");
    check_achievement_authorization_status();
?>

<html>
<head>
<style>
    .indent-1{
        margin-left:24px;
    }
</style>
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/rla.css">
        <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/achievements.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/ajax.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/display.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/global.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/approval/index.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/user.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/votes.js"></script>
</head>

<body>
    <?php include("../templates/navbar.php");?>
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
    <div>
    <?php
    	$user_id = fetch_current_user_id();
    	if ($user_id==false){
            echo "You must be logged in to view this page.";
    	}
    ?>
    </div>
    <h3> 
       Approval Process
    </h3>
    <div style='margin-left:16px;font-size:12px;'>
        <div>
            Once a user has posted their link to documentation and marked it as completed:
        </div>
        <div class='indent-1'>
             If it is the first of its kind, any user can vote on it.
        </div>
        <div class='indent-1'>
            If it is a previously published achievement, the original publisher and other users who have aleady completed it can vote on it. (The original publisher receives 2 votes instead of 1.) 
        </div>
        <div>
            Once all eligible voters have voted, the vote ends.
        </div>
        <!--
        PENDING 
        <div>
            Each swing vote extends the vote until the next day if there are voters remaining. 
        </div>
        -->
        <div>
            If no one votes and time runs out, it succeeds. 
        </div>
<!--
        <div>
            Voters must anonymously explain why they voted against the achievement so that the user can make a correction. All those who voted against have 24 hours to submit an explanation on why they voted negatively. If no one submits an explanation, the vote succeeds.
        
        </div>
        <div>
            If the user makes the stated correction and voters are  unwilling to change their vote during the next submission, mods will get involved.
        
        </div>
-->
    </div>
    <h3>
        Pending Approval
    </h3>
    <div id="achievements_pending_approval">
        <?php list_all_achievements_pending_authorization(); ?>
    </div>
    <h3>
        Accepted Achievements
    </h3>
    <div id='completed_achievements_requiring_athuroziation'>
        <?php list_all_completed_authorized_achievements(); ?>
    </div>
    <h3>
        Rejected Achievements
    </h3>
	<div id="owned_achievements_requiring_authorization">
        <?php list_all_rejected_achievements(); ?>
	</div>
</body>
</html>
<?php

function display_vote_summary($achievement){

           $vote_summary = summarize_vote($achievement->id);
            $string = display_vote_timer($achievement->id)  
              . "<span style='font-style:italic;' class='";
            if ($vote_summary["status"]=="tie"){
                if ($vote_summary["total"]!=0){
                    $string = $string . "tie'> Tie";
                } else if ($vote_summary["total"]==0){
                    $string = $string . "tie'> Approved if no one votes against.";
                }
            } else if ($vote_summary["status"]=="for"){
                $string = $string . "win'> Leading by ". ($vote_summary["yays"] - $vote_summary["nays"]);
            } else if ($vote_summary["status"]=="against"){
                $string = $string . "lose'> Losing by " . ($vote_summary["nays"] - $vote_summary["yays"]);
            }
            $string = $string . "</span>";
    return $string;
}

function list_all_completed_authorized_achievements(){
	$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievements_set=false;
	$user_id = fetch_current_user_id();
	if ($user_id==false){
        echo "None.";
		return;
	} 
    $statement = $connection->prepare("select * from achievements where completed!=0 and authorized!=0 and owner=?");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement = $statement->fetchObject()){
        $achievements_set=true;
        echo "  <div>
                    <a href='" . SITE_ROOT ."/summary/?id=$achievement->id'>$achievement->name</a> 
                    <a href='" . SITE_ROOT ."/votes/?id=$achievement->id' class='hand text-button'>[ Vote Summary ]</a> 
                </div>";
    }
    if (!$achievements_set){
        echo "None.";
    }
}

function list_all_achievements_pending_authorization(){
	$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievements_set=false;
	$user_id = fetch_current_user_id();
	if ($user_id==false){
        echo "None.";
		return;
	} 
    $statement = $connection->prepare("select * from achievements where completed=0 and authorized=0 and authorizing!=0 and owner=?");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement = $statement->fetchObject()){
        $achievements_set=true;
        echo "<div>Round #$achievement->round - <a href='" . SITE_ROOT ."/summary/?id=$achievement->id'>$achievement->name</a> - "
          . display_vote_summary($achievement) 
          . "</div>";
    }
    if (!$achievements_set){
        echo "None.";
    }
}

function list_all_rejected_achievements(){
	$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievements_set=false;
	$user_id = fetch_current_user_id();
	if ($user_id==false){
        echo "None.";
		return;
	} 
    $statement = $connection->prepare("select * from achievements where completed=0 and authorizing=0 and owner=?  and id in (select achievement_id from votes where active=1)");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement = $statement->fetchObject()){
        $achievements_set=true;
        echo "  <div>
                    <a href='" . SITE_ROOT ."/summary/?id=$achievement->id'>$achievement->name</a> 
                    <a href='" . SITE_ROOT ."/votes/?id=$achievement->id' class='hand text-button'>[ Vote Summary ]</a> 
                </div>";
    }
    if (!$achievements_set){
        echo "None.";
    }
}
