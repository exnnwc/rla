<?php

$connection = new PDO("mysql:host=localhost;dbname=rla", "root", "");

switch ($_POST["function_to_be_called"]) {
    case "list_work":
        list_work($_POST['work']);
        break;
    case "change_work":
        change_work($_POST['id'], $_POST['work']);
        break;
    case "create_work":
        create_work($_POST['achievement_id']);
        break;
}

function change_work($id, $work) {
    echo "$id $work";
    global $connection;
    $statement = $connection->prepare("update achievements set work=? where id=?");
    $statement->bindvalue(1, $work, PDO::PARAM_INT);
    $statement->bindvalue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function create_work($achievement_id){
    global $connection;
    $statement=$connection->prepare("insert into work (achievement_id) values (?)");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
}
function list_work($work) {
    global $connection;
    $statement = $connection->prepare("select count(*) from achievements where active=1 and work=?");
    $statement->bindValue(1, $work, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn() == 0) {
        echo "<div>No work has been assigned to this time period.</div>";
    } else {
        $statement = $connection->prepare("select * from achievements where active=1 and work=?");
        $statement->bindValue(1, $work, PDO::PARAM_INT);
        $statement->execute();

        while ($achievement = $statement->fetchObject()) {



            echo "<div>";
            if ($achievement->work!=1){
                if(has_it_been_worked_on($achievement->id, $achievement->work)){
                    echo "<input type='button' value='XCancelX' onclick=\"\" />";
                } else {
                    echo "<input type='button' value='Done' onclick=\"CreateWork($achievement->id)\" />";
                }
            }
              echo $achievement->name;
            
                  echo "</div>";

            echo "<div style='margin-left:12px;'>";
            if ($achievement->work != 2) {
                echo "<input type='button' value='Daily' onclick=\"ChangeWork($achievement->id, 2)\"/>";
            }
            if ($achievement->work != 3) {
                echo "<input type='button' value='Weekly' onclick=\"ChangeWork($achievement->id, 3)\" />";
            }
            if ($achievement->work != 4) {
                echo "<input type='button' value='Monthly' onclick=\"ChangeWork($achievement->id, 4)\"  />";
            }
            echo "</div>";
        }
    }
}

function has_it_been_worked_on ($achievement_id, $achievement_work){
    global $connection;
    //This is only for today
    $statement=$connection->prepare("select DATE(work.created)=DATE(NOW()) from achievements inner join work on achievements.id=work.achievement_id where work.active=1 and achievements.id=?");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    $work_created=$statement->fetchColumn();
    return $work_created;
            
}