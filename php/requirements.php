<?php

include_once ("config.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
switch (filter_input(INPUT_POST,'function_to_be_called', FILTER_SANITIZE_STRING)) {
    
}

function create_requirement($for, $by) {
    global $connection;
    //echo "select count(*) from requirements where active=1 and required_for=$for and required_by=$by";
    $statement = $connection->prepare ("select count(*) from requirements where active=1 and required_for=? and required_by=?");
    $statement->bindValue(1,$for, PDO::PARAM_INT);
    $statement->bindValue(2,$by, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn()>0){
        echo "0 This is already required.";
    } else {
    $statement = $connection->prepare("insert into requirements (required_for, required_by) values (?, ?)");
    $statement->bindValue(1, $for, PDO::PARAM_INT);
    $statement->bindValue(2, $by, PDO::PARAM_INT);
    $statement->execute();
    }
}

function delete_requirement($id) {
    global $connection;
    echo "update requirements set active=0 where id=$id";
    $statement = $connection->prepare("update requirements set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function list_new_requirements($id) {
    global $connection;
    $statement = $connection->prepare("select * from achievements where active=1 and parent=0 order by name asc");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement = $statement->fetchObject()) {
        echo "<option value='$achievement->id'>$achievement->name</option>";
    }
}

function list_requirements($id, $type) {
    global $connection;
    switch ($type){
        case "for":
            $other_type="by";
            break;
        case "by":
            $other_type="for";
            break;
    }

    $query = "select count(*) from requirements where active=1 and required_$type=?";
    $statement = $connection->prepare($query);
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn() == 0) {
        echo "<div style=' font-style:italic;'>";
        if ($type=="for"){
            echo "No other achievements required to complete this achievement.";
        } else if ($type=="by"){
            echo "No other achievements require this achievement for completion.";
        }        
        echo "</div>";
    } else {
        $query="select achievements.id, achievements.name, requirements.id, requirements.required_$type from achievements inner join requirements on achievements.id=requirements.required_$other_type where requirements.active=1 and required_$type=? order by achievements.name";
       
        $statement = $connection->prepare($query);
        $statement->bindValue(1, $id, PDO::PARAM_INT);
        $statement->execute();
        while ($result=$statement->fetch(PDO::FETCH_NUM)){
            $achievement_id=$result[0];
            $achievement_name=$result[1];
            $requirement_id=$result[2];
            $requirement_required=$result[3];

           echo "<div><input type='button' value='X' 
                    onclick=\"deleteRequirement($requirement_id, $requirement_required);\" />
                  <a href='http://".$_SERVER['SERVER_NAME']."/rla/?rla=$achievement_id'>$achievement_name</a></div>";
 
        }        


    }
}
