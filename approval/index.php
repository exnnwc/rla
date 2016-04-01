<?php
	require_once("../php/config.php");
	require_once("../php/display.php");
	require_once("../php/user.php");
    require_once("../php/votes.php");
    check_achievement_authorization_status();
    $user_id = fetch_current_user_id();
?>

<html>
<head>
<style>
</style>
        <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>

        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/css/rla.css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/css/approval.css">
        <script src="<?php echo SITE_ROOT; ?>/approval/index.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/ajax.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/achievements.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/display.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/global.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/user.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/votes.js"></script>
</head>

<body>
    <?php 
    include("../templates/navbar.php");
    include("../templates/login.php");
    ?>
    <h3 style='clear:both;'> 
       Approval Process
    </h3>
    <div id='approval-explanation' class='content-div'>
        <div>
            Once a user has posted their link to documentation and marked it as completed:
        </div>
        <div class='indent-1'>
             If it is the first of its kind, any user can vote on it.
        </div>
        <div class='indent-1'>
            If it is a previously published achievement, the original publisher and other users who have aleady completed it can vote on it. 
        </div>
        <div>
            Once all eligible voters have voted, the vote ends.
        </div>
        <div>
            If no one votes and time runs out, it succeeds. 
        </div>
    </div>
    <?php if ($user_id==false):?>
        <div>
                You must be logged in to view this page.
        </div>
    <?php elseif ($user_id!=false): ?>
        <h3>
            Pending Approval
        </h3>
        <div id="achievements_pending_approval" class='content-div'>
            <?php list_pending_achievements(); ?>
        </div>
        <h3>
            Accepted Achievements
        </h3>
        <div id='completed_achievements_requiring_authorization' class='content-div'>
            <?php list_achievements_with_votes(false); ?>
        </div>
        <h3>
            Rejected Achievements
        </h3>
    	<div id="owned_achievements_requiring_authorization" class='content-div'>
            <?php list_achievements_with_votes(true); ?>
    	</div>
    <?php endif; ?>
</body>
</html>
<?php

function display_vote_summary($achievement){

           $vote_summary = summarize_vote($achievement->id);
            if ($vote_summary["status"]=="tie"){
                $class_name="tie"; 
                    $vote_caption="Tie";
            } else if ($vote_summary["status"]=="for"){
                $class_name="win";
                if ($vote_summary["total"]==0){
                    $vote_caption="Win by default. No votes.";
                } else if ($vote_summary["total"]>0){
                    $vote_caption="Leading by " . ($vote_summary["yays"] - $vote_summary["nays"]);
                }

            } else if ($vote_summary["status"]=="against"){
                $class_name="lose"; 
                $vote_caption="Losing by " . ($vote_summary["nays"] - $vote_summary["yays"]);
            }
            $string = display_vote_timer($achievement->id)  
              . "<span style='font-style:italic;' class='$class_name'> $vote_caption</span>";
    return $string;
}

function list_achievements_with_votes($reject){
	$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievements_set=false;
	$user_id = fetch_current_user_id();
	if ($user_id==false){
        echo "None.";
		return;
	} 
    $query = !$reject 
      ? "select * from achievements where deleted=0 and abandoned=0 and completed!=0 and authorized!=0 and owner=?"
      : "select * from achievements where  deleted=0 and abandoned=0 and completed=0 and authorizing=0 and owner=?  and id in (select achievement_id from votes where active=1)";
    $statement = $connection->prepare($query);
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

function list_pending_achievements(){
	$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievements_set=false;
	$user_id = fetch_current_user_id();
	if ($user_id==false){
        echo "None.";
		return;
	} 
    $statement = $connection->prepare("select * from achievements where  deleted=0 and abandoned=0 and completed=0 and authorized=0 and authorizing!=0 and owner=?");
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

