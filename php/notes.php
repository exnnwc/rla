<?php
require_once ("config.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);


function create_note($note, $achievement_id){
    global $connection;
    $statement=$connection->prepare("insert into notes (body, achievement) values (?, ?)");
    $statement->bindValue(1, $note, PDO::PARAM_STR);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
}

function delete_note($id){
    global $connection;
    $statement=$connection->prepare("update notes set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}


