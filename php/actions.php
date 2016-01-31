<?php

function change_work_status_of_action($id, $work) {
    global $connection;
    $statement = $connection->prepare("update actions set work=? where active=1 and (id=? or reference=?)");
    $statement->bindvalue(1, $work, PDO::PARAM_INT);
    $statement->bindvalue(2, $id, PDO::PARAM_INT);
    $statement->bindvalue(3, $id, PDO::PARAM_INT);
    $statement->execute();
}

function create_action($achievement_id, $action) {
    global $connection;
    $achievement=fetch_achievement($achievement_id);    
    $statement = $connection->prepare("insert into actions(achievement_id, name,  work) values (?, ?, ?)");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(2, $action, PDO::PARAM_STR);
    $statement->bindValue(3, $achievement->work, PDO::PARAM_INT);
    $statement->execute();
}

function delete_action($id) {
    //go through this.  This can't be right.
    global $connection;
    $action = fetch_action($id);
    $statement = $connection->query("select count(*) from actions where active=1 and (id=$action->id or reference=$action->id)");
    $statement->execute();
    $num_of_achievements = $statement->fetchColumn();
    if ($num_of_achievements == 1) {
        $connection->exec("insert into actions (name, achievement_id, reference) values ('$action->name', 0, 0)");
        $connection->exec("update actions set active=0 where id=$id");
    } else if ($num_of_achievements > 1) {
        if ($action->reference == 0) {
            $statement = $connection->query("select id from actions where active=1 and reference=$action->id limit 1");
            $next_action_id = $statement->fetchColumn();
            $connection->exec("update actions set active=0 where id=$id");
            $connection->exec("update actions set reference=0 where id=$next_action_id");
            $connection->exec("update actions set reference=$next_action_id where reference=$id");
        } else {
            $statement = $connection->prepare("update actions set active=0 where id=?");
            $statement->bindValue(1, $id, PDO::PARAM_INT);
            $statement->execute();
        }
    } else {
        //ERROR
    }
}

function fetch_action($id) {
    global $connection;
    $statement = $connection->prepare("select * from actions where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}

function update_work_status_for_related_actions($achievement_id, $new_work){
    //I may eventually allow actions to have separate work schedules than their attached achievements.
    global $connection;
    $statement = $connection->prepare('update actions set work=? where achievement_id=?');
    $statement->bindValue(1, $new_work, PDO::PARAM_INT);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    
}