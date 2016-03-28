<?php

require_once("changelog.php");
require_once ("config.php");
require_once ("filter.php");
require_once ("requirements.php");
require_once ("user.php");

function abandon_achievement($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set abandoned=1 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function abandon_published($id){
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $num_of_users_working_on_this = how_many_users_are_working_on_this_published($id);
    if ($num_of_users_working_on_this==0){
        delete_children($id);
        delete_achievement($id);
    } else if ($num_of_users_working_on_this>0){
        disown_published($id);
    }
}
function achievement_name_exists($name, $parent) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from achievements where deleted=0 and name=? and parent=? limit 1");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->bindValue(2, $parent, PDO::PARAM_INT);
    $statement->execute();
    return boolval($statement->fetchcolumn());
}

function activate_achievement($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set active=1 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function are_all_requirements_documented($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement_ids = fetch_all_requirements($id);
    if (!$achievement_ids){
        return true;
    }
    foreach ($achievement_ids as $achievement_id){
        $achievement = fetch_achievement($achievement_id); 
        if (!$achievement->documented){
            return false;
        }
    }
    return true;
}
function are_ranks_duplicated($achievement) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("SELECT COUNT(*) as count FROM achievements "
        . DEFAULT_WHERE . " and parent=$achievement->parent and owner=$achievement->owner GROUP BY rank HAVING COUNT(*) > 1");
    if ((int) $statement->fetchColumn() > 0) {
        return true;
    }
    return false;
}

function change_authorizing_status($id, $status){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $authorizing = $status
        ? "now()"
        : "0";
	$statement = $connection -> prepare ("update achievements set round=round+1, authorizing=$authorizing where id=?");
	$statement->bindValue(1, $id, PDO::PARAM_INT);
	$statement->execute();
	
}

function change_description($id, $description) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set description=? where id=?");
    $statement->bindValue(1, $description, PDO::PARAM_STR);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_documentation_status($id, $status) {
    if ($id==0){
        error_log(__FUNCTION__ . " ($id, $status) - id should not be 0");
        return;
    }
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $achievement = fetch_achievement($id);
    if ($achievement->documented == $status){
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set documented=? where id=?");
    $statement->bindValue(1, $status, PDO::PARAM_BOOL);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
    $message = !$status 
        ? "Achievement is now undocumented." 
        : "Achievement is now documented.";
    create_history($id, $message);
    $statement = $connection->prepare("select id from achievements where completed=0 and parent=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($child_id = $statement->fetchColumn()){
        change_documentation_status($child_id, $status);
    }
}

function change_due_date($id, $date) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set due=? where id=?");
    $statement->bindValue(1, $date, PDO::PARAM_STR);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}
function change_locked_status($id, $status){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
	$statement = $connection -> prepare ("update achievements set locked=? where id=?");
	$statement->bindValue(1, $status, PDO::PARAM_BOOL);
	$statement->bindValue(2, $id, PDO::PARAM_INT);
	$statement->execute();
}
function change_name($id, $name) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    $statement = $connection->prepare("update achievements set name=? where id=?");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
    $message = "Achievement name changed from \"$achievement->name\" to \"$name\".";
    create_history($id, $message);
}

function change_power($id, $power) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set power=? where id=?");
    $statement->bindValue(1, $power, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_points($id, $up){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    $user_id = fetch_current_user_id();
    if ($user_id===false){
        return "You need to be logged in to do this.";
    }
    $user_points = fetch_user_points($user_id);
    $cost = $up 
      ? $achievement->points*2
      : $achievement->points;
    if ($cost===0){
        $cost=1;
    } 
    $cost=abs($cost);
    if ($cost>$user_points){
        $string = "You don't have enough points to vote this achievement ";
        $string = $up
          ? $string . "up."
          : $string . "down.";
        return $string;
    }
    $operator = $up
      ? "+"
      : "-";
    $statement = $connection->query("update achievements set points=points".$operator."1 where id=$achievement->id");
    $statement = $connection->query("update users set points=points-$cost where id=$user_id"); 
    return true;
}
function change_quality($id, $quality) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set quality=? where id=?");
    $statement->bindValue(1, $quality, PDO::PARAM_BOOL);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_rank($id, $new_rank) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $achievement = fetch_achievement($id);
    $highest_rank = fetch_highest_rank($achievement->parent);
    if (fetch_num_of_achievements($achievement) != $highest_rank) {
        error_log("Line #" . __LINE__ . " " . __FUNCTION__ . "($id, $new_rank): Holes in rank (" .
            fetch_num_of_achievements($achievement) . " != $highest_rank");
        fix_achievement_ranks("updated", $achievement);
        return;
    }
    update_rank($id, $new_rank);
    delete_achievement($achievement->id);
    abandon_achievement($achievement->id);
    if ($new_rank <= 0) {
        error_log("Line #" . __LINE__ . " " . __FUNCTION__ . "($id, $new_rank): Shouldn't be able to change rank to 0 or negative");
        return;
    }
    if (are_ranks_duplicated($achievement)) {
        error_log("Line #" . __LINE__ . " " . __FUNCTION__ . "($id, $new_rank): Ranks duplicated.");
        fix_achievement_ranks("updated", $achievement->parent);
        return;
    }
    if ($new_rank > $highest_rank) {
        undelete_achievement($achievement->id);
        fix_achievement_ranks("rank", $achievement->parent);
        return;
    }
    rank_achievements($achievement, $new_rank);
    undelete_achievement($achievement->id);
    unabandon_achievement($achievement->id);
}

function check_achievement_authorization_status(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("select * from achievements where authorizing!=0 and completed=0 and deleted=0 and abandoned=0");
    while ($achievement=$statement->fetchObject()){
        $num_of_seconds=get_num_of_seconds_until_authorized($achievement->id);        
        $vote_summary=summarize_vote($achievement->id);
        if ($num_of_seconds<=0){
            if ($vote_summary["total"]==0 || ($vote_summary["total"]>0 &&  $vote_summary["status"]=="for")){
                $connection->exec("update achievements set authorized=now(), completed=now() where id=$achievement->id");
            } else if ($vote_summary["total"]>0 && $vote_summary["status"]=="tie"){
                extend_vote($achievement->id, 24); 
            } else if ($vote_summary["total"]>0 && $vote_summary["status"]=="against"){
               reject_achievement($id); 
                
            }
        } 
    }
}

function clear_due_date($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set due=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function complete_achievement($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set completed=now() where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function count_achievements() {
    //INTEGRATE
    //This is intended for working achievements.
    $data = [];
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);

    $filter_is_active = return_if_filter_active();
    $user_id = fetch_current_user_id();
    if ($user_id == false) {
        return ["total"=>0];
        /*
        $default_query = "select count(*) from achievements " . DEFAULT_LISTING . " and public=1";
        $query = $filter_is_active ? "select count(*) from achievements " . process_filter_to_query($_SESSION['filter']) : $default_query;
        $statement = $connection->query($query);
        $data['total'] = (int) $statement->fetchColumn();
        if ($filter_is_active) {
            $statement = $connection->query($default_query);
            $num_of_unfiltered = (int) $statement->fetchColumn();
            $num_of_filtered = $num_of_unfiltered - $total;
            $data["filtered"] = $num_of_filtered;
        }
*/
    } else if (!$user_id == false) {
        $default_query = "select count(*) from achievements " . DEFAULT_LISTING . " and owner=$user_id";
        $query = $filter_is_active ? "select count(*) from achievements " . process_filter_to_query($_SESSION['filter']) : $default_query;
        $statement = $connection->query($query . " and active=1");
        $num_of_working_achievements = (int) $statement->fetchColumn();
        $statement = $connection->query($query);
        $total = (int) $statement->fetchColumn();

        $statement = $connection->query($query . " and active=0");
        $num_of_nonworking_achievements = (int) $statement->fetchColumn();

        $data = ["total" => $total,
            "working" => $num_of_working_achievements,
            "not_working" => $num_of_nonworking_achievements];
        if ($filter_is_active) {
            $statement = $connection->query($default_query);
            $num_of_unfiltered = (int) $statement->fetchColumn();
            $num_of_filtered = $num_of_unfiltered - $total;
            $data["filtered"] = $num_of_filtered;
        }
    }
    return $data;
}
function copy_children($id, $owner, $parent){
    $children=fetch_children($id);
    foreach ($children as $child){
        $new_id=copy_achievement($child, $owner, $parent);
        if (count(fetch_children($id))>0){
            copy_children($child, $owner, $new_id);
        }
    }
}

function copy_achievement($id, $owner, $parent){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    $query="insert into achievements (locked, points, documentation, documentation_explanation, completed, owner, parent, name, description, documented, published, original) values (now(), 1, '$achievement->documentation', '$achievement->documentation_explanation', '$achievement->completed', $owner, $parent, '$achievement->name', '$achievement->description', 1, $achievement->id, 0)";
    $statement = $connection->exec($query);
    
    $statement = $connection->query("select id from achievements where deleted=0 and published=$id and owner=$owner");
    return (int)$statement->fetchColumn();
}




function create_achievement($name, $parent) {
    $user_id = fetch_current_user_id();
    if ($user_id==false) {
        //BAD Need to be logged in.
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($parent);
    if (achievement_name_exists($name, $parent)) {
        error_log("Line #" . __LINE__ . " " . __FUNCTION__ . "($name, $parent): Achievement already exists by that name.");
        return;
    }
    if ($parent == 0) {
        $query = "insert into achievements(owner, name, parent, rank) values (?, ?, ?, ?)";
    } else if ($parent > 0) {
        $query = "insert into achievements(owner, name, parent, rank, documented) values (?, ?, ?, ?, $achievement->documented)";
    }
    $statement = $connection->prepare($query);
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->bindValue(2, $name, PDO::PARAM_STR);
    $statement->bindValue(3, $parent, PDO::PARAM_INT);
    $statement->bindValue(4, fetch_highest_rank($parent) + 1, PDO::PARAM_INT);
    $statement->execute();
}

function create_documentation($id, $documentation, $explanation){
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    if ($explanation == "Explain here. (optional)"){
        $explanation="";
    }  
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection -> prepare ("update achievements set documentation=?, documentation_explanation=? where id=?");
    $statement->bindValue(1, $documentation, PDO::PARAM_STR);
    $statement->bindValue(2, $explanation, PDO::PARAM_STR);
    $statement->bindValue(3, $id, PDO::PARAM_INT);
    $statement->execute();

}
function deactivate_achievement($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function delete_achievement($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set deleted=1 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function delete_children($id){
    $children=fetch_children($id);
    foreach ($children as $child){
        if (count(fetch_children($id))>0){
            delete_children($child);
        }
        delete_achievement($child);
    }

}

function disown_achievement($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement=$connection->prepare("update achievements set disowned=1 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}
function disown_children($id){
    $children=fetch_children($id);
    foreach ($children as $child){
        if (count(fetch_children($id))>0){
            disown_children($child);
        }
        disown_achievement($child);
    }

}
function disown_published($id){
    disown_achievement($id);
    disown_children($id);
}

function extend_vote($id, $hours){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set hours_added=hours_added+? where id=?");
    $statement->bindValue(1, $hours, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}
function fetch_achievement($id) {
/*
    if (!user_owns_achievement($id)) {
        //BAD
        $error_msg = "Line #" . __LINE__ . " - " . __FUNCTION__ . "($id) - User ";
        $user_id = fetch_current_user_id();
        $error_msg = $user_id == false
            ?  $error_msg . "(Anonymous)"
            : $error_msg . "(#$user_id)";
        $error_msg = $error_msg . "does not own achievement.";
        error_log($error_msg);
        return;
    }
*/
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select * from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}

function fetch_achievement_name($id) {
/*    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }*/
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select name from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}

function fetch_achievement_by_rank_and_parent($rank, $parent) {
    //INTEGRATE   
    /*
      if (!user_owns_achievement($id)) {
      //BAD
      return;
      } */
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select * from achievements where deleted=0 and rank=? and parent=?");
    $statement->bindValue(1, $rank, PDO::PARAM_INT);
    $statement->bindValue(2, $parent, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}

function fetch_children($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $child_ids = [];
    $statement = $connection->prepare("select id from achievements where deleted=0 and abandoned=0 and parent=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($child_id = (int)$statement->fetchColumn()){
       $child_ids[] = $child_id; 
    }
    return $child_ids;
}
function fetch_due_message($num_of_days_til_due) {
    if ($num_of_days_til_due == -1) {
        return "(due yesterday)";
    } else if ($num_of_days_til_due < 0) {
        return " (due " . abs($num_of_days_til_due) . " days ago)";
    } else if ($num_of_days_til_due == 0) {
        return "(due today)";
    } else if ($num_of_days_til_due == 1) {
        return "(due tomorrow)";
    } else if ($num_of_days_til_due > 0) {
        return "(due $num_of_days_til_due days from now)";
    }
}

function fetch_highest_rank($parent) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select rank from achievements " . DEFAULT_WHERE . " and parent=$parent order by rank desc limit 1");
    $statement->bindValue(1, $parent, PDO::PARAM_INT);
    $statement->execute();
    return (int) $statement->fetchColumn();
}

function fetch_num_of_achievements($achievement) {
    if (!user_owns_achievement($achievement->id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("select count(*) from achievements " . DEFAULT_LISTING);
    return (int) $statement->fetchColumn();
}

function fetch_random_achievement_id($user_id) {
    if ($user_id != 0 && fetch_current_user_id() == $user_id) {
        $query = "select id from achievements where deleted=0 and owner=? order by rand() limit 1";
    } else if ($user_id == 0) {
        $query = "select id from achievements where deleted=0 and abandoned=0 and public=1 order by rand() limit 1";
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare($query);
    if ($user_id != 0) {
        $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    }
    $statement->execute();
    return $statement->fetchColumn();
}

function fix_achievement_ranks($field, $achievement) {
    if (!user_owns_achievement($achievement->id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $connection->exec("set @rank=0");
    $connection->exec("update achievements set rank=@rank:=@rank+1 "
        . DEFAULT_WHERE . " and parent=$achievement->parent and owner=$achievement->owner order by $field ");
}

function get_num_of_seconds_until_authorized($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection -> prepare ("select time_to_sec(timediff(now(), authorizing)) from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $num_of_seconds=$statement->fetchColumn();
    $num_of_seconds=86400-$num_of_seconds;
    //return $num_of_seconds;
    return 0;
}

function has_this_achievement_already_been_published($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from achievements where deleted=0 and published=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return ((int)$statement->fetchColumn()>0);        
}

function does_user_already_own_published_achievement($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $user_id = fetch_current_user_id();
    if ($user_id===false){
        return;
    }
    $statement = $connection->query("select count(*) from achievements where deleted=0 and original=$id and owner=$user_id");
    $num_of_achievements = (int)$statement->fetchColumn();
    if ($num_of_achievements==0){
        return false;
    } else if ($num_of_achievements>0){
        if ($num_of_achievements>1){
            error_log(__FILE__ . " #" . __LINE__ . " " . __FUNCTION__ . "($id) user owns more than one achievement of this type.");
        }
        return true;
    }    
    
}
function how_many_days_until_due($id) {
/*
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
*/
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select datediff(due, curdate()) from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $val = $statement->fetchColumn();
    return $val == NULL ? false : $val;
}

function how_many_users_are_working_on_this_published($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from achievements where deleted=0 and abandoned=0 and original=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return (int) $statement->fetchColumn();
}
function is_everything_else_completed($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare ("select count(*) from achievements where completed=0 and deleted=0 and abandoned=0 and parent=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    if ((int)$statement->fetchColumn()>0){
        return false;   
    } 
    $required_ids = fetch_all_requirements($id);
    if (!$required_ids){
        return true;
    }
    foreach($required_ids as $required_id){
        $achievement = fetch_achievement($required_id);
        if ($achievement->completed==0){
            return false;
        }
    }
    return true;
}

function is_it_active($id) {
    //This should be is is_it_deleted
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select deleted from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    echo json_encode(!(boolean) $statement->fetchColumn());
}

function own_published($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $user_id = fetch_current_user_id();
    if ($user_id===false){
        error_log (__FILE__ . " #" . __LINE__ . " " . __FUNCTION__ . "($id) tried to create a published achievement without being logged in.");
        return "You need to be logged in to do this.";
    }
    $new_parent=take_over_achievement($id, $user_id, 0);
    take_over_children($id, $user_id, $new_parent);
    return $new_parent;
}

function publish_achievement($id){
    $user_id=fetch_current_user_id();
    if ($user_id===false){
        return "You must be logged in to do this.";
    }
    if (!user_owns_achievement($id)) {
        //BAD
        return "User does not own this achievement.";
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    if ($achievement->parent!=0){
        //Send a confirmation to user that they will lose the parent achievement in this hierarchy.
        return "Achievement has parent.";
    } else if (has_this_achievement_already_been_published($id)){
        return "Achievement already published.";
    }  else if (fetch_user_points($user_id)<1){
        return "You do not have enough points to do this."; 
    }
    $statement = $connection->exec("update users set points=points-1 where id=$user_id");
    $new_id=copy_achievement($id, $user_id, 0);
    if (count(fetch_children($id))>0){
        copy_children($id, $user_id, $new_id);
    }
}

function rank_achievements($achievement, $new_rank) {
    if (!user_owns_achievement($achievement->id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $connection->exec("set @rank=$new_rank");
    if ($new_rank - $achievement->rank > 0) {
        $connection->exec("update achievements set rank=@rank:=@rank-1 "
            . DEFAULT_WHERE . " and parent=$achievement->parent and rank<=$new_rank and owner=$achievement->owner order by rank desc");
    } else if ($new_rank - $achievement->rank < 0) {

        $connection->exec("update achievements set rank=@rank:=@rank+1 "
            . DEFAULT_WHERE . " and parent=$achievement->parent and rank>=$new_rank and owner=$achievement->owner order by rank");
    } else if ($new_rank - $achievement->rank == 0) {
        error_log("Line #" . __LINE__ . " " . __FUNCTION__ . "($achievement->id, $new_rank): New rank should not be the same as the old.");
    }
}

function remove_achievement($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    if ($achievement->deleted) {
        error_log("Line #" . __LINE__ . ":" . __FUNCTION__ . "($id) Achievement already deleted.");
        return;
    }
    $achievement->abandoned ? delete_achievement($id) : abandon_achievement($id);
    if (!$achievement->locked) {
        toggle_locked_status($id);
    }
    $connection->exec("update achievements set rank=rank-1 where deleted=0 and parent=$achievement->parent and rank>=$achievement->rank");
    //This is a quick fix. May require a deleted tag so that tags can still stay active when an achievement is deleted.
    $connection->exec("update tags set active=0 where achievement_id=$id");
}

function reject_achievement($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $connection->exec("update achievements set rejected=now(), authorizing=0, hours_added=0, documentation='', documentation_explanation='' where id=$id");   
    create_history($id, "Achievement rejected in the approval process. <a href='" . SITE_ROOT ."/votes/?id=$id' class='hand text-button'>[ Vote Summary ]</a>");
}
function restore_achievement($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $achievement = fetch_achievement($id);
    if (!$achievement->abandoned && !$achievement->deleted) {
        error_log("Line #" . __LINE__ . ":" . __FUNCTION__ . "($id) Achievement doesn't need to be undeleted.");
        return;
    }
    if ($achievement->deleted) {
        undelete_achievement($id);
    }
    if ($achievement->abandoned) {
        unabandon_achievement($id);
    }
    update_rank($id, fetch_highest_rank($achievement->parent) + 1);
}

function toggle_documentation_status($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $achievement = fetch_achievement($id);
    change_documentation_status($id, !$achievement->documented);
}

function toggle_quality($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    $statement = $connection->prepare("update achievements set quality=? where id=?");
    $statement->bindValue(1, !$achievement->quality, PDO::PARAM_BOOL);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function toggle_active_status($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    $statement = $connection->prepare("update achievements set active=? where id=?");
    $statement->bindValue(1, !$achievement->active, PDO::PARAM_BOOL);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function toggle_locked_status($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    $status = $achievement->locked == 0 ? "now()" : "0";
    $statement = $connection->prepare("update achievements set locked=$status where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $message = $achievement->locked ? "Achievement is now unlocked." : "Achievement is now locked.";
    create_history($id, $message);
}

function update_rank($id, $new_rank) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set rank=? where id=?");
    $statement->bindValue(1, $new_rank, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function uncomplete_achievement($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set completed=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function unabandon_achievement($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set abandoned=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function undelete_achievement($id) {
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update achievements set deleted=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}
function user_owns_achievement($id) {
    $user_id = fetch_current_user_id();
    if ($user_id==false) {
        return false;
    }
    error_log(__FUNCTION__ . "($id)");
    $achievement = fetch_achievement($id);
    if ($achievement->owner == $user_id) {
        return true;
    }
    return false;
}
function take_over_achievement($id, $owner, $parent){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    $query="insert into achievements (locked, points,  owner, parent, name, description, documented,  original) values (now(), 1,  $owner, $parent, '$achievement->name', '$achievement->description', 1, $achievement->id)";
    $statement = $connection->exec($query);

    $query = "select id from achievements where deleted=0 and original=$id and owner=$owner and parent=$parent";
    $statement = $connection->query($query);
    return (int) $statement->fetchColumn();
}

function take_over_children($id, $owner, $parent){
    $children=fetch_children($id);
    foreach ($children as $child){
        $new_parent = take_over_achievement($child, $owner, $parent);
        if (count(fetch_children($id))>0){
            take_over_children($child, $owner, $new_parent);
        }
    }
}
