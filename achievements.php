<?php
$connection = new PDO ("mysql:host=localhost;dbname=rla", "root", "");
switch ($_POST['function_to_be_called']){    
    case "change_documentation_status":
        change_documentation_status($_POST['id'], $_POST['status']);
        break;
    case "change_description":
        change_description($_POST['id'], $_POST['description']);
        break;
    case "change_rank":
        change_rank($_POST['id'], $_POST['new_rank']);
    case "create_quick":
        create_quick($_POST['name'], $_POST['parent']);
        break;
    case "delete":
        delete($_POST['id']);
        break;
    case "is_it_active":
        is_it_active($_POST['id']);
        break;
    case "list":
        list_all();
        break;
	case "list_children":
		list_children($_POST['parent']);
		break;
}

function change_description($id, $description){
    //In the future, create a new one instead of just changing it.
    global $connection;
    $statement=$connection->prepare("update achievements set description=? where id=?");
    $statement->bindValue(1, $description, PDO::PARAM_STR);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_documentation_status($id, $status){
    global $connection;
    $statement=$connection->prepare("update achievements set documented=? where id=?");
    $statement->bindValue(1, $status, PDO::PARAM_BOOL);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}
function change_rank($id, $rank){
    global $connection;
    $statement=$connection->prepare ("update achievements set rank=? where id=?");
    $statement->bindValue(1, $rank, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    //Need to reorder the other ones.
}
function create_quick($name, $parent){
    global $connection;
    $statement=$connection->prepare("insert into achievements(name, parent) values (?, ?)");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->bindValue(2, $parent, PDO::PARAM_INT);
    $statement->execute();
}

function delete($id){
    global $connection;
    $statement=$connection->prepare("update achievements set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();

}

function is_it_active($id){
    global $connection;
    $statement=$connection->prepare("select active from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    echo $statement->fetchColumn();
}
function list_all(){
    global $connection;
    $statement=$connection->query("select * from achievements where active=1 and parent=0");
    while ($achievement=$statement->fetchObject()){
        echo "<div title='ID #$achievement->id'>$achievement->rank)
              <input type='button' value='X' onclick=\"DeleteAchievement($achievement->id, false);\" />
              <input type='button' value='-' 
                onclick=\"ChangeRank($achievement->id, ". ($achievement->rank-1).", true);\"/>
              <input type='text' style='width:32px;text-align:center;' value='$achievement->repeated' />
              <input type='button' value='+' />
              <a href='http://".$_SERVER['SERVER_NAME']."/rla/?rla=$achievement->id'> $achievement->name </a>
              </div>";
    }
}

function list_children($id){
    global $connection;
    $statement=$connection->prepare("select * from achievements where active=1 and parent=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement=$statement->fetchObject()){
        echo "<div>
              <input type='button' value='X' onclick=\"DeleteAchievement($achievement->id, $achievement->parent, true);\" />
              <a href='http://".$_SERVER['SERVER_NAME']."/rla/?rla=$achievement->id'> $achievement->name </a>
              </div>";
    }

}



function rank_table_in_order($query, $parent) {
//Erase if not used by 01/15/15
$rank=1;
    if ($query==0){
        $query="select * from achievements where active=1 and parent=$parent";
    }
$statement=$connection->query($query);
while ($achievement=$statement->fetchObject()){
    $connection->exec("update achievements set rank=$rank where id=$achievement->id");
    $rank++;
}
}

