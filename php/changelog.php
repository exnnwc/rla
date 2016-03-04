<?php
function create_history($id, $message){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection -> prepare("insert into history(achievement_id, message) values (?, ?)");
    $statement-> bindValue(1, $id, PDO::PARAM_INT);
    $statement->bindVAlue(2, $message,PDO::PARAM_STR);
    $statement->execute();
}
