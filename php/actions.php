<?php
function create_action($achievement_id, $action) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement=fetch_achievement($achievement_id);    
    $statement = $connection->prepare("select count (*) from actions where active=1 and name=?");
    $statement->bindValue(1, $action, PDO::PARAM_STR);
    $statement->execute();
    $num_of_records=(int)$statement->fetchColumn();
    if ($num_of_records>0){
        return false;
    }
    $statement = $connection->prepare("insert into actions(achievement_id, name,  work) values (?, ?, ?)");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(2, $action, PDO::PARAM_STR);
    $statement->bindValue(3, $achievement->work, PDO::PARAM_INT);
    $statement->execute();
}

function delete_action($id) {
    //If I need to extend this to include reference actions, I've already written that. Deleted 02/01/16
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
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
function fetch_action_by_name($name){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count (*) from actions where active=1 and name=?");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->execute();
    $num_of_records=(int)$statement->fetchColumn();
    if ($num_of_records==0){
        return false;
    } else if ($num_of_records>1){
        //BAD Should not be more than one active action with the same name.
        return false;
    }
    $statement = $connection -> prepare ("select * from actions where active=1 and name=?");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
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
