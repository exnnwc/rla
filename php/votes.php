<?php
require_once("config.php");

function create_vote($user_id, $achievement_id, $vote, $explanation){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("insert into votes (user_id, achievement_id, vote, explanation) values (?, ?, ?, ?)");
    $statement->bindValue(1, $user_id, PDO::PARAM_INT);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(3, $vote, PDO::PARAM_BOOL);
    $statement->bindValue(4, $explanation, PDO::PARAM_STR);
    $statement->execute();
}
