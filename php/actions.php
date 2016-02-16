<?php
function create_action($achievement_id, $action) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement=fetch_achievement($achievement_id);    
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

function update_work_status_for_related_actions($achievement_id, $new_work){    
    //I may eventually allow actions to have separate work schedules than their attached achievements.
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);    
    $statement = $connection->prepare('update actions set work=? where achievement_id=?');
    $statement->bindValue(1, $new_work, PDO::PARAM_INT);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
}