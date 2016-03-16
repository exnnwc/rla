<?php
require_once ("config.php");

function create_requirement($for, $by) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare ("select count(*) from requirements where active=1 and required_for=? and required_by=?");
    $statement->bindValue(1,$for, PDO::PARAM_INT);
    $statement->bindValue(2,$by, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn()>0){
        error_log("Line #".__LINE__ . " " . __FUNCTION__ . "($for, $by): This is already required.");                
        return;
    } 
    $statement = $connection->prepare("insert into requirements (required_for, required_by) values (?, ?)");
    $statement->bindValue(1, $for, PDO::PARAM_INT);
    $statement->bindValue(2, $by, PDO::PARAM_INT);
    $statement->execute();    
}

function delete_requirement($id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);    
    $statement = $connection->prepare("update requirements set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function count_requirements_with($id, $type){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);    
    $statement = $connection->prepare("select count(*) from requirements where active=1 and required_$type=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}

function fetch_all_requirements($achievement_id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);    
    $statement = $connection->prepare("select required_by from requirements where active=1 and required_for=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($requirement_id = $statement->fetchColumn()){
        $requirement_ids[]=$requirement_id;
    }
    return $requirement_ids;
}
