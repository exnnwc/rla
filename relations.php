<?php

$connection = new PDO("mysql:host=localhost;dbname=rla", "root", "");

switch (filter_input(INPUT_POST,'function_to_be_called', FILTER_SANITIZE_STRING)) {
    case "create":
        create(filter_input(INPUT_POST,'a', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST,'b', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete":
        delete(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list":
        list_relations(filter_input(INPUT_POST,'achievement_id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_new":
        list_new();
        break;
}

function create($a, $b) {
    global $connection;
    $statement=$connection->prepare("select count(*) from relations where a=? and b=?");
    $statement->bindValue(1, $a, PDO::PARAM_INT);
    $statement->bindValue(2, $b, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn() == 0) {

        $statement = $connection->prepare("insert into relations(a,b) values (?,?)");
        $statement->bindValue(1, $a, PDO::PARAM_INT);
        $statement->bindValue(2, $b, PDO::PARAM_INT);
        $statement->execute();
    } else {
        echo "0 Achievement already exists.";
    }
}

function delete($id) {
    echo "$id being removed";
    global $connection;
    $statement = $connection->prepare("update relations set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function list_new() {
    global $connection;
    $statement = $connection->query("select * from achievements where active=1 order by name");
    echo "<option>Please indicate which achievement you'd like to create a relation for.</option>";
    while ($achievement = $statement->fetchObject()) {
        echo "<option value='$achievement->id'>$achievement->name</option>";
    }
}

function list_relations($achievement_id) {
    global $connection;
    $statement = $connection->prepare("select count(*) from relations where active=1 and a=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn() == 0) {
        echo "<div style=' font-style:italic;'>No other achievements are related.</div>";
    } else {
        $query = "select achievements.id, achievements.name, relations.id from achievements inner join relations on achievements.id=relations.b where relations.active=1 and relations.a=?";
        $statement = $connection->prepare($query);
        $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
        $statement->execute();
        while ($result = $statement->fetch(PDO::FETCH_NUM)) {
            $achievement_id_from_result = $result[0];
            $achievement_name = $result[1];
            $relation_id = $result[2];
            //echo "<input type='button' onclick=\"document.write('asdfa')\"";
            echo "<div title='$relation_id'><input type='button' value='X' onclick=\"DeleteRelation($relation_id, $achievement_id)\" />
            <a href='http://" . $_SERVER['SERVER_NAME'] . "/rla/?rla=$achievement_id_from_result'>$achievement_name</a></div>";
        }
    }
}
