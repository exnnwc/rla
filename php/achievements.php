<?php

require_once ("config.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);

function achievement_name_exists($name, $parent) {
    global $connection;
    $statement = $connection->prepare("select count(*) from achievements where active=1 and name=? and parent=? limit 1");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->bindValue(2, $parent, PDO::PARAM_INT);
    $statement->execute();
    return boolval($statement->fetchcolumn());
}

function activate_achievement($id) {
    global $connection;
    $statement = $connection->prepare("update achievements set active=1 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function are_ranks_duplicated($parent) {
    global $connection;
    $statement = $connection->query("SELECT COUNT(*) as count FROM achievements where parent=$parent and active=1 GROUP BY rank HAVING COUNT(*) > 1");
    if ((int) $statement->fetchColumn() > 0) {
        return true;
    }
    return false;
}

function change_description($id, $description) {
    global $connection;
    $statement = $connection->prepare("update achievements set description=? where id=?");
    $statement->bindValue(1, $description, PDO::PARAM_STR);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_documentation_status($id, $status) {
    global $connection;
    $statement = $connection->prepare("update achievements set documented=? where id=?");
    $statement->bindValue(1, $status, PDO::PARAM_BOOL);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_name($id, $name) {
    global $connection;
    $statement = $connection->prepare("update achievements set name=? where id=?");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_power($id, $power) {
    global $connection;
    $statement = $connection->prepare("update achievements set power=? where id=?");
    $statement->bindValue(1, $power, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_rank($id, $new_rank) {
    $achievement = fetch_achievement($id);
    update_rank($id, $new_rank);
    deactivate_achievement($achievement->id);
    if ($new_rank <= 0) {
        //BAD - shouldn't be able to change rank to 0 or a negative
    }
    if (are_ranks_duplicated($achievement->parent)) {
        fix_achievement_ranks("updated", $achievement->parent);
        exit;
        //BAD - ranks shouldn't be duplicated 
    }
    //if user picks a new rank too big
    if ($new_rank > (fetch_highest_rank($achievement->parent))) {
        activate_achievement($achievement->id);
        fix_achievement_ranks("rank", $achievement->parent);
        exit;
    }
    rank_achievements($achievement, $new_rank);
    activate_achievement($achievement->id);
}

function change_work_status_of_achievement($id, $status) {
    global $connection;
    $statement = $connection->prepare("update achievements set work=? where id=?");
    $statement->bindValue(1, $status, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
    update_work_status_for_related_actions($id, $status);
}

function complete_achievement($id) {
    global $connection;
    $statement = $connection->prepare("update achievements set completed=now() where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function count_achievements() {
    global $connection;
    $data = [];

    $statement = $connection->query("select count(*) from achievements where quality=false and active=1 and parent=0 and  work>0");
    $num_of_working_achievements = (int) $statement->fetchColumn();

    $statement = $connection->query("select count(*) from achievements where active=1 and quality=true");
    $num_of_qualities = (int) $statement->fetchColumn();

    $statement = $connection->query("select count(*) from achievements where active=1 and parent=0");
    $num_of_achievements = (int) $statement->fetchColumn();

    $num_of_nonworking_achievements = $num_of_achievements - $num_of_working_achievements - $num_of_qualities;
    $data = ["total" => $num_of_achievements,
        "working" => $num_of_working_achievements,
        "not_working" => $num_of_nonworking_achievements,
        "qualities" => $num_of_qualities];

    echo json_encode($data);
}

function create_achievement($name, $parent) {
    global $connection;
    $achievement = fetch_achievement($parent);
    if (achievement_name_exists($name, $parent)) {
        //ERROR $name already exists.
        exit;
    }
    if ($parent == 0) {
        $query = "insert into achievements(name, parent, rank) values (?, ?, ?)";
    } else if ($parent > 0) {
        $query = "insert into achievements(name, parent, rank, documented) values (?, ?, ?, $achievement->documented)";
    }
    $statement = $connection->prepare($query);
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->bindValue(2, $parent, PDO::PARAM_INT);
    $statement->bindValue(3, fetch_highest_rank($parent) + 1, PDO::PARAM_INT);
    $statement->execute();
}

function deactivate_achievement($id) {
    global $connection;
    $statement = $connection->prepare("update achievements set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function delete_achievement($id) {
    global $connection;
    deactivate_achievement($id);
    $achievement = fetch_achievement($id);
    $connection->exec("update achievements set rank=rank-1 where active=1 and parent=$achievement->parent and rank>=$achievement->rank");
}

function fetch_achievement($id) {
    global $connection;
    $statement = $connection->prepare("select * from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}

function fetch_achievement_name($id) {
    global $connection;
    $statement = $connection->prepare("select name from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}

function fetch_achievement_by_rank_and_parent($rank, $parent) {
    global $connection;
    $statement = $connection->prepare("select * from achievements where active=1 and rank=? and parent=?");
    $statement->bindValue(1, $rank, PDO::PARAM_INT);
    $statement->bindValue(2, $parent, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}

function fetch_highest_rank($parent) {
    global $connection;
    $statement = $connection->prepare("select rank from achievements where active=1 and parent=? order by rank desc limit 1");
    $statement->bindValue(1, $parent, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}

function fetch_random_achievement_id() {
    global $connection;
    $statement = $connection->query("select id from achievements where active=1 and parent=0 order by rand() limit 1");
    return $statement->fetchColumn();
}

function fix_achievement_ranks($field, $parent) {
    global $connection;
    $connection->exec("set @rank=0");
    $connection->exec("update achievements set rank=@rank:=@rank+1 where active=1 and parent=$parent order by $field");
}

function is_it_active($id) {
    global $connection;
    $statement = $connection->prepare("select active from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    echo $statement->fetchColumn();
}

function rank_achievements($achievement, $new_rank) {
    global $connection;
    $connection->exec("set @rank=$new_rank");
    if ($new_rank - $achievement->rank > 0) {
        $connection->exec("update achievements set rank=@rank:=@rank-1 where active=1 and parent=$achievement->parent and rank<=$new_rank order by rank");
    } else if ($new_rank - $achievement->rank < 0) {
        $connection->exec("update achievements set rank=@rank:=@rank+1 where active=1 and parent=$achievement->parent and rank>=$new_rank order by rank");
    } else if ($new_rank - $achievement->rank == 0) {
        //BAD - new rank should not be the same as the old
    }
}

function change_quality($id, $quality) {
    global $connection;
    $statement = $connection->prepare("update achievements set quality=? where id=?");
    $statement->bindValue(1, $quality, PDO::PARAM_BOOL);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function update_rank($id, $new_rank) {
    global $connection;
    $statement = $connection->prepare("update achievements set rank=? where id=?");
    $statement->bindValue(1, $new_rank, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function uncomplete_achievement($id) {
    global $connection;
    $statement = $connection->prepare("update achievements set completed=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}
