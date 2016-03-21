<?php
require_once("config.php");
require_once("user.php");
function create_vote( $achievement_id, $vote, $explanation){
    $user_id = fetch_current_user_id();
    if ($user_id==false){
        error_log(__FILE__ . " #" . __LINE__  . ":" . __FUNCTION__ . "($achievement_id, $vote, $explanation) vote attempted to be created without being logged in.");
        return;
    }

    if (how_did_user_vote($user_id, $achievement_id)!=false){
        error_log(__FILE__ . " #" . __LINE__  . ":" . __FUNCTION__ . "($achievement_id, $vote, $explanation) user #$user_id attempted to vote more than once.");
        return;
    }


    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("insert into votes (user_id, achievement_id, vote, explanation) values (?, ?, ?, ?)");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(3, $vote, PDO::PARAM_BOOL);
    $statement->bindValue(4, $explanation, PDO::PARAM_STR);
    $statement->execute();
}
function summarize_vote ($achievement_id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection -> prepare("select count(*) from votes where active=1 and achievement_id=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    $num_of_votes = (int)$statement->fetchColumn();
    $statement = $connection -> prepare("select count(*) from votes where active=1 and vote=1 and achievement_id=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    $num_of_yays = (int)$statement->fetchColumn();
    $statement = $connection -> prepare("select count(*) from votes where active=1 and vote=0 and achievement_id=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    $num_of_nays = (int)$statement->fetchColumn();
    if ($num_of_votes != ($num_of_nays+$num_of_yays)){
        error_log(__FILE__ . " #" . __LINE__ . " " . __FUNCTION__ . "($achievement_id) - number of votes does not equal combined number of yays and nays.");
        return;
    }
    if ($num_of_nays > $num_of_yays){
        $status = "against";
    } else if ($num_of_yays > $num_of_nays){
        $status = "for";
    } else if ($num_of_yays == $num_of_nays){
        $status = "tie";
    }
    return ["status"=>$status, "total"=>$num_of_votes, "nays"=>$num_of_nays, "yays"=>$num_of_yays];
}

function how_did_user_vote($user_id, $achievement_id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from votes where active=1 and user_id=? and achievement_id=?");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    if ((int)$statement->fetchColumn()==0){
        return false; 
    }
    $statement = $connection->prepare("select vote, explanation from votes where active=1 and user_id=? and achievement_id=?");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    $vote = $statement->fetchObject();
    return $vote->vote
      ? "yay"
      : "nay$vote->explanation";
}

