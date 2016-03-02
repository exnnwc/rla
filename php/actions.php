<?php
function action_already_associated($achievement_id, $action_id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $action=fetch_action($action_id);
    $statement = $connection->prepare ("select count(*) from actions where achievement_id=? and name=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(2, $action->name, PDO::PARAM_STR);
    $num_of_actions=(int)$statement->fetchColumn();
    if ($num_of_actions==1){
        //WARN User trying to associate an action that's already been associated.
        return true;
    } else if ($num_of_actions>1){
        error_log("Line #".__LINE__ . " " . __FUNCTION__ . "($achievement_id, $action_id): More than one action registered for this achievement.");
        return true;
    }

}
function action_already_exists($achievement_id, $action){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from actions where active=1 and name=? and achievement_id=?");
    $statement->bindValue(1, $action, PDO::PARAM_STR);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    $num_of_records=(int)$statement->fetchColumn();
    if ($num_of_records==1){
        //WARN Action already exists.
        return true;
    } else if ($num_of_records>1){
        error_log("Line #".__LINE__ . " " . __FUNCTION__ . "($achievement_id, $action): More than one action registered for this achievement.");
        return true;
    }

}
function top_action_already_exists($achievement_id, $action){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from actions where active=1 and name=? and achievement_id=0");
    $statement->bindValue(1, $action, PDO::PARAM_STR);
    $statement->execute();
    $num_of_records=(int)$statement->fetchColumn();
    if ($num_of_records==1){
        $statement= $connection->prepare("select id from actions where active=1 and achievement_id=0 and name=?");
        $statement->bindValue(1, $action, PDO::PARAM_STR);
        $statement->execute();
        $action_id=(int)$statement->fetchColumn();
        associate_action($achievement_id, $action_id); 
        return true; 
    } else if ($num_of_records>1){
        error_log("Line #".__LINE__ . " " . __FUNCTION__ . "($achievement_id, $action_id): More than one action registered for this achievement.");
        return true;
    }
}
function associate_action($achievement_id, $action_id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $action=fetch_action($action_id);
    if (action_already_exists($achievement_id, $action->name)){
        return;
    }
    if(action_already_associated($achievement_id, $action_id)){
        return;
    }
    $statement=$connection->prepare ("insert into actions (achievement_id, name) values (?, ?)");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(2, $action->name, PDO::PARAM_STR);
    $statement->execute();

}
function create_action($achievement_id, $action) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    if (action_already_exists($achievement_id, $action)){
        return;
    }
    if (top_action_already_exists($achievement_id, $action)){
        return;
    }
    $statement = $connection->prepare("insert into actions(achievement_id, name) values (?, ?)");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(2, $action, PDO::PARAM_STR);
    $statement->execute();
    $statement = $connection -> prepare ("insert into actions(achievement_id, name) values (0, ?)");
    $statement->bindValue(1, $action, PDO::PARAM_STR);
    $statement->execute();

}

function delete_action($id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $action=fetch_action($id);
    $statement=$connection->prepare("select count(*) from actions where active=1 and achievement_id!=0 and name=?");
    $statement->bindValue(1, $action->name, PDO::PARAM_STR);
    $statement->execute();
    if ((int)$statement->fetchColumn()==1){
        $statement=$connection->prepare("update actions set active=0 where achievement_id=0 and name=?");
        $statement=$connection->bindValue(1, $action->name, PDO::PARAM_STR);
        $statement->execute();
    }
    $statement=$connection->prepare("update actions set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();    
}

function fetch_action($id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select * from actions where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}


function update_work_status_for_related_actions($achievement_id, $new_work){    
    //I may eventually allow actions to have separate work schedules than their attached achievements.
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);    
    $statement = $connection->prepare('update actions set work=? where achievement_id=?');
    $statement->bindValue(1, $new_work, PDO::PARAM_INT);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
}
