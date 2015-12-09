<?php
$connection = new PDO ("mysql:host=localhost;dbname=rla", "root", "");

switch ($_POST['function_to_be_called']){
    case "create_quick":
        create_quick($_POST['name']);
        break;
    case "list":

        list_all();
        break;
}

function create_quick($name){
    global $connection;
    $statement=$connection->prepare("insert into achievements(name) values (?)");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->execute();
}

function list_all(){
    global $connection;
    $statement=$connection->query("select * from achievements where active=1");
    while ($achievement=$statement->fetchObject()){
        echo "<div><a href='http://".$_SERVER['SERVER_NAME']."/rla/?rla=$achievement->id'> $achievement->name </a></div>";
    }
}
