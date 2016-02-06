<?php

require_once("work.php");

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

function fetch_action_listing($action) {
    $string = "<span style='margin-left:20px;cursor:pointer;";
    $string = has_action_been_worked_on($action->id) ? $string . "color:grey;text-decoration:line-through;' 
                        title='Cancel work'  
                        onmouseover=\"$(this).css('text-decoration', 'none');\"  
                        onmouseleave=\"$(this).css('text-decoration', 'line-through');\"
                        onclick=\"cancelWork($action->id);\"" : $string . "' title='Click to indicate action worked.' 
                        onmouseover=\"$(this).css('text-decoration', 'line-through');\" 
                        onmouseleave=\"$(this).css('text-decoration', 'none');\" 
                        onclick=\"createWork($action->id);\"";
    $string = $string . "> $action->name [" . convert_work_num_to_caption($action->work) . "]</span>";
    if ($action->type == 1) {
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

function list_new_relations() {
    global $connection;
    $statement = $connection->query("select * from achievements where active=1 order by name");
    echo "<option>Please indicate which achievement you'd like to create a relation for.</option>";
    while ($achievement = $statement->fetchObject()) {
        echo "<option value='$achievement->id'>$achievement->name</option>";
    }
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

function list_notes($achievement_id) {
    global $connection;
    $statement = $connection->prepare("select * from notes where active=1 and achievement=? order by created desc");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($note = $statement->fetchObject()) {
        echo "<div style='background-color:lightgray;width:800px;'>
                <h6 style='background-color:white';margin:0px;>
                    <input type='button' value='X' onclick=\"deleteNote($note->id, $note->achievement);\" /> "
        . date("m/d/y h:i:s", strtotime($note->created))
        . "</h6>
                <div style='padding:12px;'>" .
        str_replace("\n", "<BR>", $note->body);
        echo "</div>
            </div>";
    }
}

function list_relations($achievement_id) {
    global $connection;
    if (fetch_number_of_relations_for($achievement_id) == 0) {
        echo "<div style=' font-style:italic;'>No other achievements are related.</div>";
        return;
    }
    $query = "select achievements.id, achievements.name, relations.id from achievements 
                inner join relations on achievements.id=relations.a or achievements.id=relations.b where relations.active=1 and 
                ((relations.a=:id and relations.b!=:id) or (relations.a!=:id and relations.b=:id)) and achievements.id!=:id";
    $statement = $connection->prepare($query);
    $statement->bindValue(":id", $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($result = $statement->fetch(PDO::FETCH_NUM)) {
        $achievement_id_from_result = $result[0];
        $achievement_name = $result[1];
        $relation_id = $result[2];
        echo "  <div>
                    <input type='button' value='X' onclick=\"deleteRelation($relation_id, $achievement_id)\" />
                    <a href='" . \SITE_ROOT . "?rla=$achievement_id_from_result'>$achievement_name</a>
                </div>";
    }
}

function list_requirements($id, $type) {
    global $connection;       
    $type_arr=["for"=>"by", "by"=>"for"];
    $other_type=$type_arr[$type];
    if (there_are_no_requirements($id,$type)){
        return;
    }  
    $statement = $connection->prepare("select achievements.id, achievements.name, requirements.id, requirements.required_$type from achievements 
                                         inner join requirements on achievements.id=requirements.required_$other_type 
                                         where requirements.active=1 and required_$type=? order by achievements.name");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($result = $statement->fetch(PDO::FETCH_NUM)) {
        $achievement_id = $result[0];
        $achievement_name = $result[1];
        $requirement_id = $result[2];
        $requirement_required = $result[3];
        echo "  <div>
                    <input type='button' value='X' 
                      onclick=\"deleteRequirement($requirement_id, $requirement_required);\" />
                    <a href='http://" . $_SERVER['SERVER_NAME'] . "/rla/?rla=$achievement_id'>$achievement_name</a>
                </div>";
    }
}

function there_are_no_requirements($id, $type){
    if (count_requirements_with($id, $type) == 0) {
        echo "<div style=' font-style:italic;'>";
        if ($type == "for") {
            echo "No other achievements required to complete this achievement.";
        } else if ($type == "by") {
            echo "No other achievements require this achievement for completion.";
        }
        echo "</div>";
        
    }   
}