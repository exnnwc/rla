<?php
require_once("../php/achievements.php");
require_once("../php/config.php");
require_once("../php/display.php");
require_once("../php/user.php");
require_once("../php/votes.php");
?>
<html>
<head>

        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/rla.css">
        <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/achievements.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/ajax.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/display.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/global.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/user.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/votes.js"></script>
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
            Active
        </h3>
        <div>
            <?php display_achievements_requiring_vote(); ?>
        </div>
        <h3>
            Past Votes
        </h3>
        <div>
            <?php display_votes(); ?>
        </div>
    <?php endif; ?>
</body>
</html>

<?php

function display_achievements_requiring_vote(){
	$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $string = "";
    $achievements_set=false;
	$user_id = fetch_current_user_id();
	if ($user_id==false){
		return;
	} 
    $query="select * from achievements where completed=0 and authorizing!=0 and owner!=?";
	$statement = $connection->prepare($query);
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
	$statement->execute();
	while ($achievement = $statement->fetchObject()){
        $achievements_set=true;    
        $vote = how_did_user_vote($user_id, $achievement->id);
		$string = $string . "
                <div style='margin-top:16px;' >                
                <div style=''>"
            . display_vote_summary($achievement)
            . "</div>
                <div>
                <span id='yay$achievement->id' class='";
                        if ($vote==false){
                            $string = $string . "vote_button hand text-button'>[ Yay ]";
                        } else if ($vote=="nay"){
                            $string = $string . "inactive-vote'>Yay";
                        } else if (substr($vote, 0, 3)=="yay"){
                            $string = $string . "active-vote'>Yay";
                        }
                    $string = $string . "</span>
                    /
                    <span id='nay$achievement->id' class='";
                        if ($vote==false){
                            $string = $string . "vote_button hand text-button'>[ Nay ]";
                        } else if ($vote=="nay"){
                            $string = $string . "active-vote'>Nay";
                        } else if (substr($vote, 0, 3)=="yay"){
                            $string = $string . "inactive-vote'>Nay";
                        }
                    $string = $string . "
                    </span>";
                    if ($vote==false){
                        $string = $string 
                          . "<input type='text' id='explanation_input$achievement->id' class='explanation_input' 
                            value='Please explain why if nay.' style='color:grey;'/>";
                    } else if ($vote!=false && strlen(substr($vote, 3))>0){
                        $string = $string . " - " . substr($vote, 3);
                    }
            $string = $string 
            . "</div><div style='padding-left:16px;padding-top:8px;'>"
            . $achievement->name
                     . " - "
        . fetch_username($achievement->owner);
                    
                    $string = $string . "                        
                </div>";
        if (!empty($achievement->description)){
            $string = $string . "<div style='padding-left:16px;'> $achievement->description</div>";
        }
        $string = $string .
               "<div style='padding-top:4px;padding-left:16px;'>
                        Documentation: <a href='$achievement->documentation'>$achievement->documentation </a>";
        if ($achievement->documentation_explanation){
            $string = $string . " - $achievement->documentation_explanation";
        }

        $string = $string . "
                </div>
            </div>";
	}
    if (!$achievements_set){
        $string = "None.";
    }
    echo $string;
}
function display_submitted_achievements(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement_set = false;
   	$user_id = fetch_current_user_id();
	if ($user_id==false){
        echo "You must be logged in to view this.";
		return;
	} 
    $statement = $connection->prepare("select * from achievements where owner=? and id in (select achievement_id from votes) order by authorizing desc");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement = $statement->fetchObject()){
        $achievement_set = true;
        $vote_summary = summarize_vote($achievement->id);
        echo "  <div>
                    <a href='".SITE_ROOT."/summary/?id=$achievement->id'>$achievement->name</a> - 
                    <a href='".SITE_ROOT."/votes/?id=$achievement->id' style='text-decoration:none;'>";
        if ($vote_summary['total']==0){
            echo "<span style='color:green;' class='underline-hover'>Passed due to no conflict.</span>";
        } else if ($vote_summary['total']>0 && $vote_summary["status"]=="for"){
            echo "<span style='color:green;' class='underline-hover'> Passed by " . ($vote_summary['yays'] - $vote_summary['nays']. "</span>");
        } else if ($vote_summary["status"]=="against"){
            echo "<span style='color:red;' class='underline-hover'>Failed to pass by " . ($vote_summary['nays'] - $vote_summary['yays'] . "</span>");
        } else if ($vote_summary["status"]=="tie"){
            echo "<span style='color:grey;' class='underline-hover'>Stalemate</span>";
        }
        echo "</a> ";
        if ($achievement->completed==0){
            echo " <a href='".SITE_ROOT. "/publish/' style='' class='text-button'>[ Voting Ends" 
              . display_vote_timer($achievement->id)."]</a>";
;
        }
        echo "</div>";
         
    }
    if (!$achievement_set){
        echo "None.";
    }
}
function display_votes(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $vote_set=false;
	$user_id = fetch_current_user_id();
	if ($user_id==false){
        echo "You must be logged in to view this.";
		return;
	} 
    $statement = $connection->prepare("select * from votes where user_id=?");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->execute();
    $old_date=0;
    $old_time=0;
    while ($vote=$statement->fetchObject()){
        $vote_set=true;
        $achievement = fetch_achievement($vote->achievement_id);
        $vote_summary= summarize_vote($achievement->id);
        $date = date("m/d/y", strtotime($vote->created)); 
        $time = date("g:iA", strtotime($vote->created));
        if ($date!=$old_date){
            echo "<div style='font-weight:bold;text-decoration:underline;text-align:center;margin-top:16px;'>$date</div>";
            $old_date = $date;
        }
        echo "<div>";
        if ($time!=$old_time){
            echo "<div style=''>$time</div>";
        }
        echo $vote->vote
          ? "<div style='color:green;'>Voted For"
          : "<div style='color:red'>Voted Against";
        if (!empty($vote->explanation)){
           echo " - $vote->explanation"; 
        }
        echo "</div> \"$achievement->name\" - " . fetch_username($achievement->owner) . " <span style='font-style:italic;'>(<span style='text-decoration:underline;'>" 
          . $vote_summary["caption"] . "</span> by " . $vote_summary['difference'];
        echo $vote_summary['difference']>1
          ? " votes."
          : " vote.";
        echo ")</span></div>";
    }
    if (!$vote_set){
        echo "None.";
    }
}
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
