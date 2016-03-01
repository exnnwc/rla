<?php

function create_todo($achievement_id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from todo where active=1 and name is null and achievement_id=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    if ((int)$statement->fetchColumn()>0){
        return;
    }
    $statement = $connection->prepare("insert into todo(achievement_id) values(?)");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
}


function change_todo_name($id, $name){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update todo set name=? where id=?");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function complete_todo($id){
    echo $id;
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update todo set completed=now() where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function cancel_todo($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update todo set completed=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function delete_todo($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update todo set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}
