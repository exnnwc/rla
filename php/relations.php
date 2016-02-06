<?php
require_once ("config.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);



function create_relation($a, $b) {
    global $connection;
    $statement=$connection->prepare("select count(*) from relations where a=? and b=?");
    $statement->bindValue(1, $a, PDO::PARAM_INT);
    $statement->bindValue(2, $b, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn() > 0) {
        //BAD 
        return;
    }
        $statement = $connection->prepare("insert into relations(a,b) values (?,?)");
        $statement->bindValue(1, $a, PDO::PARAM_INT);
        $statement->bindValue(2, $b, PDO::PARAM_INT);
        $statement->execute();
}

function delete_relation($id) {
    global $connection;
    $statement = $connection->prepare("update relations set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function fetch_number_of_relations_for($achievement_id){
    global $connection;
    $statement = $connection->prepare("select count(*) from relations where active=1 and ((a=:id and b!=:id) or (a!=:id and b=:id))");
    $statement->bindValue(":id", $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}

