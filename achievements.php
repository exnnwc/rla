<?php
include ("config.php");
//TODO: Keep track of all changes. 
$connection = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PWD);
switch (filter_input(INPUT_POST, 'function_to_be_called', FILTER_SANITIZE_STRING)) {
    case "change_description":
        change_description(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        break;
    case "change_documentation_status":
        change_documentation_status(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING));
        break;
    case "change_name":
        change_name(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'new_name', FILTER_SANITIZE_STRING));
        break;
    case "change_power":
        change_power(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'new_power', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "change_rank":
        change_rank(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'new_rank', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "create_achievement":
        create_achievement(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING), filter_input(INPUT_POST, 'parent', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "delete_achievement":
        delete_achievement(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "is_it_active":
        is_it_active(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "list_achievements":
        list_achievements(filter_input(INPUT_POST, 'sort_by', FILTER_SANITIZE_STRING));
        break;
    case "list_children":
        list_children(filter_input(INPUT_POST, 'parent', FILTER_SANITIZE_NUMBER_INT));
        break;
    case "change_work_status":
        change_work_status(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT), filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT));
        break;
}

function change_description($id, $description) {
    global $connection;
    $statement = $connection->prepare("update achievements set description=? where id=?");
    $statement->bindValue(1, $description, PDO::PARAM_STR);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_documentation_status($id, $status) {
    global $connection;
    $statement = $connection->prepare("update achievements set documented=? where id=?");
    $statement->bindValue(1, $status, PDO::PARAM_BOOL);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_name($id, $name) {
    global $connection;
    $statement = $connection->prepare("update achievements set name=? where id=?");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function change_power($id, $power) {
    global $connection;
    $statement = $connection->prepare("update achievements set power=? where id=?");
    $statement->bindValue(1, $power, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
    //Need to reorder the other ones.
}

function change_rank($id, $new_rank) {
    global $connection;
    $achievement = fetch_achievement($id);
    if ($new_rank > (fetch_rank($achievement->parent) + 1)) {
        $new_rank = fetch_rank($achievement->parent) + 1;
    }
    $statement = $connection->prepare("select count(*) from achievements where active=1 and parent=? and rank=? limit 1");
    $statement->bindValue(1, $achievement->parent, PDO::PARAM_INT);
    $statement->bindValue(2, $new_rank, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn()) {
        $statement = $connection->prepare("select * from achievements where active=1 and parent=? and rank=?");
        $statement->bindValue(1, $achievement->parent, PDO::PARAM_INT);
        $statement->bindValue(2, $new_rank, PDO::PARAM_INT);
        $statement->execute();
        $other_achievement = $statement->fetchObject();
        change_rank_with_another_achievement($id, $achievement, $other_achievement);
    } else {
        rank_table_in_order(0, $achievement->parent);
    }
    update_rank($id, $new_rank);
}

function change_rank_with_another_achievement($id, $achievement, $other_achievement) {
    global $connection;
    if (abs($achievement->rank - $other_achievement->rank) == 1) {
        update_rank($other_achievement->id, $achievement->rank);
    } else if (abs($achievement->rank - $other_achievement->rank) > 1) {
        if ($achievement->rank - $other_achievement->rank > 1) {
            $end = $achievement->rank;
            $begin = $other_achievement->rank;
            $query = "update achievements set rank=rank+1 where rank>=$begin and rank<=$end and parent=$achievement->parent and id != $id";
        } else if ($achievement->rank - $other_achievement->rank < -1) {
            $begin = $achievement->rank;
            $end = $other_achievement->rank;
            $query = "update achievements set rank=rank-1 where rank>=$begin and rank<=$end and parent=$achievement->parent  and id != $id";
        } else {
            //error handling
        }
        $connection->exec($query);
    } else {
        //error handling
    }
}

function change_work_status($id, $status) {
    global $connection;
    $statement = $connection->prepare("update achievements set work=? where id=?");
    $statement->bindValue(1, $status, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function check_achievements_for_name($name) {
    global $connection;
    $statement = $connection->prepare("select count(*) from achievements where active=1 and name=? limit 1");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    return $statement->fetchcolumn;
}

function create_achievement($name, $parent) {
    global $connection;
    if (!check_achievements_for_name($name)) {
        if ($parent == 0) {
            $query = "insert into achievements(name, parent, rank) values (?, ?, ?)";
        } else if ($parent > 0) {
            $query = "insert into achievements(name, parent, rank, documented) values (?, ?, ?, ?)";
        }
        $statement = $connection->prepare($query);
        $statement->bindValue(1, $name, PDO::PARAM_STR);
        $statement->bindValue(2, $parent, PDO::PARAM_INT);
        $statement->bindValue(3, fetch_rank($parent) + 1, PDO::PARAM_INT);
        if ($parent > 0) {
            $achievement = fetch_achievement($parent);
            $statement->bindValue(4, $achievement->parent, PDO::PARAM_INT);
        }
        $statement->execute();
    } else {
        echo "0 This achievement already exists."; //Maybe reference the specific achievements.
    }
}

function delete_achievement($id) {
    global $connection;
    $statement = $connection->prepare("update achievements set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $achievement = fetch_achievement($id);
    $connection->exec("update achievements set rank=rank-1 where active=1 and parent=$achievement->parent and rank>=$achievement->rank");
}

function display_achievement_listing_menu($achievement, $child) {
    //revisit
    $string = "<input type='button' value='X' onclick=\"deleteAchievement($achievement->id, $achievement->parent, true);\" />
            <input type='button' value='-' 
                onclick=\"changeRank($achievement->id, " . ($achievement->rank + 1) . ", true, $achievement->parent);\"/>                    
              <input type='text' style='width:32px;text-align:center;' value='$achievement->rank' 
                  onkeypress=\"if (event.keyCode==13){changeRank($achievement->id";
    $string = $string . ", this.value, true, $achievement->parent); }\"/>
              <input type='button' value='+' 
                onclick=\"changeRank($achievement->id, " . ($achievement->rank - 1) . ", true, $achievement->parent);\"/>";
    return $string;
}

function fetch_achievement($id) {
    global $connection;
    $statement = $connection->prepare("select * from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}

function fetch_rank($parent) {
    global $connection;
    $statement = $connection->prepare("select rank from achievements where active=1 and parent=? order by rank desc limit 1");
    $statement->bindValue(1, $parent, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}

function fetch_order_query($sort_by) {
    switch ($sort_by) {
        case "default":
            $order_by = " order by rank asc";
            break;
        case "power":
            $order_by = " order by power asc";
            break;
        case "powerrev":
            $order_by = " order by power desc, rank asc";
            break;
        case "rank":
            $order_by = " order by rank asc";
            break;
        case "rankrev":
            $order_by = " order by rank desc";
            break;
        case "created":
            $order_by = " order by created asc";
            break;
        case "createdrev":
            $order_by = " order by created desc";
            break;
        case "name":
            $order_by = " order by name asc";
            break;
        case "namerev":
            $order_by = " order by name desc";
            break;
    }
    return $order_by;
}

function is_it_active($id) {
    global $connection;
    $statement = $connection->prepare("select active from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    echo $statement->fetchColumn();
}

function list_achievements($sort_by) {

    echo "<table style='text-align:center;'>"
    . "<tr><td>X</td><td>Rank</td><td>Power</td><td>
            <a href='".SITE_ROOT."/work/' style='color:black;'>Work</a>
                </td><td>Achievement Name</td></tr>";
    global $connection;
    $query = "select * from achievements where active=1 and parent=0" . fetch_order_query($sort_by);
    $statement = $connection->query($query);
    while ($achievement = $statement->fetchObject()) {
        echo "<tr><td>
              <input type='button' value='X' onclick=\"deleteAchievement($achievement->id, $achievement->parent, false);\" />
                  </td><td>              <input type='button' value='-' 
                onclick=\"changeRank($achievement->id, " . ($achievement->rank + 1) . ", false);\"/>                    
              <input type='text' style='width:32px;text-align:center;' value='$achievement->rank' 
                  onkeypress=\"if (event.keyCode==13){changeRank($achievement->id, this.value, false); }\"/>
              <input type='button' value='+' 
                onclick=\"changeRank($achievement->id, " . ($achievement->rank - 1) . ", false);\"/>
                    </td><td>
                    $achievement->power
                    </td><td>";
        echo $achievement->work ?
                "<input type='button' value='Off' onclick=\"changeWorkStatus($achievement->id, 0, 0);\" />" :
                "<input type='button' value='On' onclick=\"changeWorkStatus($achievement->id, 1, 0);\" />";
        echo "</td><td style='text-align:left'>
              <a href='".SITE_ROOT."/?rla=$achievement->id' style='";
        if ($achievement->work) {
            echo "color:green;";
        } else {
            echo "color:red;";
        }
        echo "'> 
                $achievement->name 
                    </a>
                  </td></tr>
              ";
    }
    echo "</table>";
}


function list_children($id) {
    global $connection;
    $statement = $connection->prepare("select count(*) from achievements where active=1 and parent=? limit 1");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn() == 0) {
        echo "<div style=' font-style:italic;'>This achievement has no children.</div>";
    } else {
        $statement = $connection->prepare("select * from achievements where active=1 and parent=? order by rank");
        $statement->bindValue(1, $id, PDO::PARAM_INT);
        $statement->execute();
        while ($achievement = $statement->fetchObject()) {
            echo "<div>"
            . display_achievement_listing_menu($achievement, true)
            . " <a href='".SITE_ROOT."/?rla=$achievement->id'>$achievement->name </a>
              </div>";
        }
    }
}

function rank_table_in_order($query, $parent) {
    global $connection;
    $rank = 1;
    if ($query == 0) {
        $query = "select * from achievements where active=1 and parent=$parent order by rank";
    }
    $statement = $connection->query($query);
    while ($achievement = $statement->fetchObject()) {
        $connection->exec("update achievements set rank=$rank where id=$achievement->id");
        $rank++;
    }
}

function update_rank($id, $new_rank) {
    global $connection;
    $statement = $connection->prepare("update achievements set rank=? where id=?");
    $statement->bindValue(1, $new_rank, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}
