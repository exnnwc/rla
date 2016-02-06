<?php
require_once ("config.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);

function create_requirement($for, $by) {
    global $connection;
    $statement = $connection->prepare ("select count(*) from requirements where active=1 and required_for=? and required_by=?");
    $statement->bindValue(1,$for, PDO::PARAM_INT);
    $statement->bindValue(2,$by, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn()>0){
         //BAD This is already required.";
        return;
    } 
    $statement = $connection->prepare("insert into requirements (required_for, required_by) values (?, ?)");
    $statement->bindValue(1, $for, PDO::PARAM_INT);
    $statement->bindValue(2, $by, PDO::PARAM_INT);
    $statement->execute();    
}

function delete_requirement($id) {
    global $connection;    
    $statement = $connection->prepare("update requirements set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function count_requirements_with($id, $type){
    $statement = $connection->prepare("select count(*) from requirements where active=1 and required_$type=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}
