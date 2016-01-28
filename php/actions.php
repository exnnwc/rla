<?php

function associate($achievement_id, $action_id) {
    global $connection;
    $action = fetch_action($action_id);
    if ($action->achievement_id == 0) {
        $statement = $connection->prepare("insert into actions (name, work, achievement_id, reference) values('" . $action->name . "', $action->work, ?, 0)");
        $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
        $statement->execute();
        $statement = $connection->prepare("update actions set active=0 where id=?");
        $statement->bindValue(1, $action_id, PDO::PARAM_INT);
        $statement->execute();
    } else {
        $statement = $connection->prepare("insert into actions (name, work, achievement_id, reference) values('" . $action->name . "', $action->work, ?, ?)");
        $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
        $statement->bindValue(2, $action_id, PDO::PARAM_INT);
        $statement->execute();
    }
}

function change_work_status_of_action($id, $work) {
    global $connection;
    $statement = $connection->prepare("update actions set work=? where active=1 and (id=? or reference=?)");
    $statement->bindvalue(1, $work, PDO::PARAM_INT);
    $statement->bindvalue(2, $id, PDO::PARAM_INT);
    $statement->bindvalue(3, $id, PDO::PARAM_INT);
    $statement->execute();
}

function create_action($achievement_id, $action, $reference) {
    // echo $achievement_id + " " +  $action;
    global $connection;
    if ($reference != 0) {
        $action = fetch_action($reference)->name;
    }
    $statement = $connection->prepare("select work from achievements where id=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    $work = $statement->fetchColumn();
    $statement = $connection->prepare("insert into actions(achievement_id, name, reference, work) values (?, ?, ?, $work)");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(2, $action, PDO::PARAM_STR);
    $statement->bindValue(3, $reference, PDO::PARAM_INT);
    $statement->execute();
}

function create_new_action($action) {
    global $connection;
    $statement = $connection->prepare("insert into actions (name, achievement_id) values (?, 0)");
    $statement->bindValue(1, $action, PDO::PARAM_STR);
    $statement->execute();
}

function delete_action($id) {
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

function delete_top_action($id) {
    global $connection;
    $statement = $connection->prepare("update actions set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function fetch_action($id) {
    global $connection;
    $statement = $connection->prepare("select * from actions where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}
