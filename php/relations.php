<?php
include_once ("config.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);



function create_relation($a, $b) {
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

function delete_relation($id) {
    echo "$id being removed";
    global $connection;
    $statement = $connection->prepare("update relations set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function list_new_relations() {
    global $connection;
    $statement = $connection->query("select * from achievements where active=1 order by name");
    echo "<option>Please indicate which achievement you'd like to create a relation for.</option>";
    while ($achievement = $statement->fetchObject()) {
        echo "<option value='$achievement->id'>$achievement->name</option>";
    }
}

function list_relations($achievement_id) {
    global $connection;
    $statement = $connection->prepare("select count(*) from relations where active=1 and ((a=? and b!=?) or (a!=? and b=?))");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(3, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(4, $achievement_id, PDO::PARAM_INT);    
    $statement->execute();
    if ($statement->fetchColumn() == 0) {
        echo "<div style=' font-style:italic;'>No other achievements are related.</div>";
    } else {
        $query = "select achievements.id, achievements.name, relations.id from achievements 
            inner join relations on achievements.id=relations.a or achievements.id=relations.b
                where relations.active=1 and ((relations.a=? and relations.b!=?) or (relations.a!=? and relations.b=?)) and achievements.id!=?";
        
        $statement = $connection->prepare($query);
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(3, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(4, $achievement_id, PDO::PARAM_INT);         
   $statement->bindValue(5, $achievement_id, PDO::PARAM_INT);         
        $statement->execute();
        while ($result = $statement->fetch(PDO::FETCH_NUM)) {
            $achievement_id_from_result = $result[0];
            $achievement_name = $result[1];
            $relation_id = $result[2];
            //echo "<input type='button' onclick=\"document.write('asdfa')\"";
            echo "<div title='$relation_id'><input type='button' value='X' onclick=\"deleteRelation($relation_id, $achievement_id)\" />
            <a href='http://" . $_SERVER['SERVER_NAME'] . "/rla/?rla=$achievement_id_from_result'>$achievement_name</a></div>";
        }
    }
}
