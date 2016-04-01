<?php
require_once("../php/achievements.php");
require_once("../php/config.php");
require_once("../php/display.php");
require_once("../php/user.php");
require_once("../php/votes.php");
?>
<html>
<head>

        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/css/rla.css">
        <link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT; ?>/css/votes.css">
        <script src="<?php echo SITE_ROOT; ?>/js/jquery-2.1.4.min.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/achievements.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/ajax.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/display.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/global.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/votes/index.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/user.js"></script>
        <script src="<?php echo SITE_ROOT; ?>/js/votes.js"></script>
</head>
<body>
    <?php 
    include("../templates/navbar.php");
    include("../templates/login.php"); 
    $id = isset($_GET['id']) 
        ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) 
        : 0;
    if ($id == 0):?>
        <h3 style='clear:both;'>
            Achievements Requiring Your Vote
        </h3>
        <div>
            <?php display_achievements_requiring_vote(); ?>
        </div>
        <h3>
            Your Past Votes
        </h3>
        <div>
            <?php display_votes(); ?>
        </div>

    <?php elseif ($id>0): ?>
        <h2 style='text-align:center;clear:both;'>
            <?php 
                $achievement = fetch_achievement($id);
                $old_round=1;
                echo "<a href='".SITE_ROOT."/summary/?id=$achievement->id'>$achievement->name</a>";
            ?>
        </h2>
        <h3 style='text-align:center;'>
           <?php
                $summary = summarize_vote($achievement->id);
                if (($achievement->rejected!=0 || $achievement->authorized!=0)){
                    if ($summary["total"]===0 || ($summary["total"]>0 && $summary["status"]=="for") ){
                        echo "<span style='color:green;'>Approved!</span>";
                    } else if ($summary["total"]>0 && $summary["status"]=="against"){
                        echo "<span style='color:red;'>Rejected!</span>";
                    } else if ($summary["total"]>0 && $summary["status"]=="tie"){
                        echo "<span style='color:grey;'>Deadlocked</span>";
                    }   
                } else if ($achievement->authorizing!=0 && $achievement->rejected==0 && $achievement->authorized==0){
                    echo "<span style='color:grey;'>Voting still active</span>";
                }
            ?> 
        </h3>
        <div>
            <?php display_vote_summary_for_achievement($achievement->id); ?>
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
        echo "You must be logged in to view this.";
		return;
	} 
    $query="select * from achievements where completed=0 and authorizing!=0 and owner!=?";
	$statement = $connection->prepare($query);
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
	$statement->execute();
	while ($achievement = $statement->fetchObject()){
        if ($achievement->original==0 
          || ($achievement->original!=0 && in_array($user_id, fetch_all_voters_for_this_achievement($achievement->id)) )){
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
                  . "   </div>
                        <div style='padding-left:16px;padding-top:8px;'>Round #$achievement->round</div>
                        <div style='padding-left:16px;'>"
                  .         $achievement->name
                  . "       - 
                            <a class='user-link' href='" . SITE_ROOT . "/user/?id=$achievement->owner'>"
                  . fetch_username($achievement->owner)
                  . "       </a>";
                        
                        $string = $string . "                        
                    </div>";
            if (!empty($achievement->description)){
                $string = $string . "<div style='padding-left:32px;font-style:italic;'> $achievement->description</div>";
            }
            $string = $string .
                   "<div style='padding-left:16px;'>
                            Documentation: <a href='$achievement->documentation'>$achievement->documentation </a>";
            if ($achievement->documentation_explanation){
                $string = $string . " - <span style='font-style:italic;'>$achievement->documentation_explanation</span>";
            }
    
            $string = $string . "
                    </div>
                </div>";
        }
	}
    if (!$achievements_set){
        $string = "None.";
    }
    echo $string;
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

function display_vote_summary_for_achievement($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    $last_round=0;
    $old_round=0;
    $old_date=0;
    //$vote_set=false;
    $statement = $connection->prepare ("select * from votes where active=1 and achievement_id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($vote = $statement->fetchObject()){
  //      $vote_set=true;
        $date = date("m/d/y", strtotime($vote->created)) ;
        $time = date("h:i:s A", strtotime($vote->created));

        if($vote->round!=$old_round){
            echo "<h3>Round #$vote->round</h3>";
            $old_round = $vote->round;
        }
        if ($date != $old_date){
            echo "<div style='margin-bottom:8px;'>$date</div>"; 
        }
        echo "  <div>$time - <a href='".SITE_ROOT."/user/?id=".$user_id."' class='user-link'>" . fetch_username($vote->user_id) 
          . "</a> voted <span style='color:";         
        echo $vote->vote 
          ? "green;'>for"
          : "red;'>against";
        echo "</span> </div>
            <div style='padding-left:8px;color:grey;font-style:italic;'>";
        echo !empty($vote->explanation)
          ? $vote->explanation
          : "-No explanation given- ";
        echo "</div>"; 
        $last_round=$vote->round;
    }
    if($last_round!= $achievement->round){
        echo "<h3>Round #$achievement->round</h3>
        <div>No votes during this round. Passed by default.</div>";
    }
/*
    if (!$vote_set){
        echo "No one has voted yet.";
    }
*/
}
function display_votes(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $vote_set=false;
	$user_id = fetch_current_user_id();
	if ($user_id==false){
        echo "You must be logged in to view this.";
		return;
	} 
    $statement = $connection->prepare("select * from votes where user_id=? order by created desc");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->execute();
    $old_date=0;
    $old_time=0;

    while ($vote=$statement->fetchObject()){
        $vote_set=true;
        $achievement = fetch_achievement($vote->achievement_id);
        if ($achievement->completed!=0 && $achievement->round == $vote->round){
            $vote_summary= summarize_vote($achievement->id);
            $date = date("m/d/y", strtotime($vote->created)); 
            $time = date("g:iA", strtotime($vote->created));

            if ($date!=$old_date){
                echo "<div class='date-header'>$date</div>";
                $old_date = $date;
            }
            echo "<div style='margin-left:16px;'>";
            if ($time!=$old_time){
                echo "<div class='time-header'>$time</div>";
            }
            echo $vote->vote
              ? "<div class='for' style='margin-left:12px;color:green;'>Voted For"
              : "<div class='against' style='margin-left:12px;color:red'>Voted Against";
            if (!empty($vote->explanation)){
               echo " - $vote->explanation"; 
            }
            echo "</div><div style='margin-left:12px'>
                        Round #$vote->round - \"$achievement->name\" - 
                        <a href='" . SITE_ROOT . "/user/?id=" . $achievement->owner . "' class='user-link'>"
                      . fetch_username($achievement->owner) . "</a> <span style='font-style:italic;'>(<span style='text-decoration:underline;'>";
            if ($vote_summary["status"]=="for" && $vote_summary["total"]==0){
                echo "Passed</span> by default.)</span></div>";
            } else if ($vote_summary["total"]>0){
                echo $vote_summary["caption"] . "</span> by " . $vote_summary['difference'];
                echo $vote_summary['difference']>1
                  ? " votes."
                  : " vote.";
                echo ")</span></div>";
            }
            echo "</div>";
        }
    }
    if (!$vote_set){
        echo "None.";
    }
}
