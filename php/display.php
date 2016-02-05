<?php
require_once("work.php");
function fetch_action_listing($action) {
    $string = "<span style='margin-left:20px;cursor:pointer;";
    $string = has_action_been_worked_on($action->id) 
            ? $string . "color:grey;text-decoration:line-through;' 
                        title='Cancel work'  
                        onmouseover=\"$(this).css('text-decoration', 'none');\"  
                        onmouseleave=\"$(this).css('text-decoration', 'line-through');\"
                        onclick=\"cancelWork($action->id);\"" 
            : $string . "' title='Click to indicate action worked.' 
                        onmouseover=\"$(this).css('text-decoration', 'line-through');\" 
                        onmouseleave=\"$(this).css('text-decoration', 'none');\" 
                        onclick=\"createWork($action->id);\"";
    $string = $string . "> $action->name [" . convert_work_num_to_caption($action->work) . "]</span>";
    if ($action->type==1){
        $string = $string . "<input id='action_quantity_$action->id' type='number' value='$action->default_quantity'/>";
    }
    return $string;
}



function fetch_child_menu($achievement) {
    return "<input class='delete_button' type='button' value='X' />        
            <input type='button' value='-' 
                onclick=\"changeRank($achievement->id, " . ($achievement->rank + 1) . ", true, $achievement->parent);\"/>                    
              <input type='text' style='width:32px;text-align:center;' value='$achievement->rank' 
                  onkeypress=\"if (event.keyCode==13){changeRank($achievement->id, this.value, true, $achievement->parent); }\"/>
              <input type='button' value='+' 
                onclick=\"changeRank($achievement->id, " . ($achievement->rank - 1) . ", true, $achievement->parent);\"/>";
}

function display_queue() {
    global $connection;
    $statement = $connection->query("select * from achievements where active=1 and quality=false and work>0 order by work asc");
    $statement->execute();
    while ($achievement = $statement->fetchObject()) {
        if (!has_achievement_been_worked_on($achievement->id) && is_it_the_appropriate_day($achievement->work)) {
            echo "  <div>
                        <div style='font-weight:bold;'>
                            <a href='" . SITE_ROOT . "/?rla=$achievement->id'>
                                $achievement->name [" . convert_work_num_to_caption($achievement->work) . "]
                            </a>
                        </div>";
            $action_statement = $connection->query("select * from actions where active=1 and achievement_id=$achievement->id");
            $action_statement->execute();
            while ($action = $action_statement->fetchObject()) {
                echo "  <div style='margin-left:16px;'>" . fetch_action_listing($action) . "</div>";
            }
            echo "      </div>";
        }
    }
}

function list_actions($achievement_id) {
    global $connection;
    $statement = $connection->prepare("select * from actions where active=1 and achievement_id=? order by name");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($action = $statement->fetchObject()) {
        echo "  <div>
                    <input type='button' value='X' 
                        onclick=\"deleteAction($action->id, $action->achievement_id)\" />
                    $action->name
                </div>";
    }
}

function list_children($id) {
    global $connection;
    $statement = $connection->prepare("select count(*) from achievements where active=1 and parent=? limit 1");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn() == 0) {
        echo "<div style=' font-style:italic;'>This achievement has no children.</div>";
        exit;
    }
    $statement = $connection->prepare("select * from achievements where active=1 and parent=? order by rank");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement = $statement->fetchObject()) {
        echo "  <div>" . fetch_child_menu($achievement)
        . "         <a href='" . SITE_ROOT . "/?rla=$achievement->id'>$achievement->name </a>
                </div>";
    }
}

function list_new_actions() {
    global $connection;
    $statement = $connection->query("select * from actions where reference=0 and active=1");
    $statement->execute();
    echo "<option></option>";
    while ($action = $statement->fetchObject()) {
        echo "<option value='$action->id'>$action->name</option>";
    }
}
