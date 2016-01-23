<?php
function display_achievement($achievement) {
    global $connection;

    echo "<div style='margin-left:32px;border-left:1px solid black;border-top:1px solid black;padding:8px;'>";


    echo "<div><div> <a style='color:black;text-decoration:none;' href='" . SITE_ROOT . "/?rla=$achievement->id';\">$achievement->name</a>";

    echo "</div><div>";
    if ($achievement->work != 2 && $achievement->parent == 0) {
        echo "<input type='button' value='Daily' onclick=\"ChangeWork($achievement->id, 2)\"/>";
    }
    if ($achievement->work != 3 && $achievement->parent == 0) {
        echo "<input type='button' value='Weekly' onclick=\"ChangeWork($achievement->id, 3)\" />";
    }
    if ($achievement->work != 4 && $achievement->parent == 0) {
        echo "<input type='button' value='Monthly' onclick=\"ChangeWork($achievement->id, 4)\"  />";
    }
    /* if ($achievement->parent == 0) {
      echo "</div>";
      } */
    echo "</div>
            <div>";
    $statement = $connection->query("select * from actions where achievement_id=$achievement->id and active=1 order by name");
    $statement->execute();
    while ($action = $statement->fetchObject()) {
        $completed = has_action_been_worked_on($action->id, $achievement->work);
        echo "<div>";
        if ($achievement->work != 1) {
            if ($completed) {
                echo "<input type='button' value='X' style='color:red' onclick=\"CancelWork($action->id)\" />";
            } else {
                echo "<input type='button' value='&#10004;' onclick=\"CreateWork($action->id)\" />";
            }
            echo "$action->name </div>";
        }
    }

    echo "</div></div>";

    $statement = $connection->query("select count(*) from achievements where parent=$achievement->id and active=1");
    $statement->execute();
    if ($statement->fetchColumn() > 0) {
        $statement = $connection->query("select id from achievements where parent=$achievement->id and active=1");
        $statement->execute();
        while ($child_id = $statement->fetchColumn()) {
            display_achievement(fetch_achievement($child_id));
        }
    }
    echo "</div>";
}



function display_action($action) {
    echo /*"  <div>$action->id $action->work " . date("m/d/y", when_last_worked($action->id)) . " " . days_since_last_worked($action->id) .
    " " . has_action_been_worked_on($action->id) . " 
                    "<input type='button' value='X' onclick=\"DeleteAction($action->id, true);\"/>
                                            <input id='show_action_options$action->id' type='button' value='+' style=''
                        onclick=\" $('#action_options$action->id').show();$('#show_action_options$action->id').hide();\"/>
                </div>*/
                "<div style='margin-left:20px;'>
                    <div style='cursor:pointer; ";
    if ($action->work > 0) {

        if (has_action_been_worked_on($action->id)) {
            echo "text-decoration:line-through;' title='Cancel work'  onmouseover=\"$(this).css('text-decoration', 'none');\"  onmouseleave=\"$(this).css('text-decoration', 'line-through');\" onclick=\"cancelWork($action->id);\"";
        } else {
            echo "color:green;' onmouseover=\"$(this).css('text-decoration', 'line-through');\" onmouseleave=\"$(this).css('text-decoration', 'none');\" onclick=\"createWork($action->id);\"";
        }
    } else {
        echo "'";
    }
    echo "  >$action->name</div>                           
                

                <div id='action_options$action->id' style='display:none'><div>
                    <input type='button' value='-' 
                        onclick=\" $('#action_options$action->id').hide();$('#show_action_options$action->id').show();\" \>";

    if ($action->work == 0) {
       
        echo "<input type='button' value='On'  onclick=\"changeWorkStatusOfAction($action->id, 1);\" />";
    } else {
        echo "<input type='button' value='Off' onclick=\"changeWorkStatusOfAction($action->id, 0);\"  />";
        if ($action->work != 1) {
            echo "<input type='button' value='Unassign' onclick=\"changeWorkStatusOfAction($action->id, 1);\"  />";
        }
        if ($action->work != 2) {
            echo "<input type='button' value='Daily' onclick=\"changeWorkStatusOfAction($action->id, 2);\"  />";
        }
        if ($action->work != 3) {
            echo "<input type='button' value='Weekly' onclick=\"changeWorkStatusOfAction($action->id, 3);\"  />";
        }
        if ($action->work != 4) {
            echo "<input type='button' value='Monthly' onclick=\"changeWorkStatusOfAction($action->id, 4);\" />";
        }
    }

    echo "</div><div><select id='new_achievement_for_action$action->id'>
              <option value=''>Please select an achievement to associate with this action here.</option>";
    display_new_action_options($action->id);
    echo "</select><input type='button' value='Associate' 
            onclick=\"associateAchievementWithAction($('#new_achievement_for_action$action->id').val(), $action->id);\"/></div>
              <div id='list_of_achievements_for_action$action->id'>";
    list_achievements_for_action($action->id);
    echo "</div>
              </div></div>";
}

function list_achievements_for_action($id) {
    global $connection;
    $query = "select actions.id, achievements.name from achievements inner join actions on achievements.id = actions.achievement_id 
        where achievements.active=1 and actions.active=1 and (actions.id=? or actions.reference=?) order by achievements.name";
    $statement = $connection->prepare($query);
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();

    while ($result = $statement->fetch()) {
        echo "<div><input type='button' value='X' onclick=\"DeleteAction($result[0], false)\"/>$result[1]</div>";
    }
}
function display_history() {
    global $connection;
    $statement = $connection->query("select * from work order by created desc");
    $statement->execute();
    $today = 0;
    $last_time = 0;
    if (!has_work_been_checked()) {

        check_work();
    }
    while ($work = $statement->fetchObject()) {
        $action = fetch_action($work->action_id);
        $achievement = fetch_achievement($action->achievement_id);

        if ($today != date("m/d/y", strtotime($work->created))) {
            echo "<h2>" . date("m/d/y", strtotime($work->created)) . "</h2>";
            $today = date("m/d/y", strtotime($work->created));
        }
        if ($last_time != date("H:i", strtotime($work->created))) {
            echo "<div>" . date("H:i", strtotime($work->created)) . "</div>";
            $last_time = date("H:i", strtotime($work->created));
        }

        //var_dump (strtotime($work->updated));
        echo "<div>Finished";
        switch ($action->work) {
            case 2:
                echo " daily ";
                break;
            case 3:
                echo " weekly ";
                break;
            case 4:
                echo " monthly ";
                break;
        }
        echo "work on \"$action->name\"";

        if (strtotime($work->updated)) {
            echo " then cancelled at " . date("H:i:s", strtotime($work->created));
        } else {
            echo "";
        }
        echo "</div>";
    }
}

function display_queue() {
    global $connection;
    $statement = $connection->query("select * from achievements where active=1 and quality=false and work>0 order by work asc");
    $statement->execute();
    while ($achievement = $statement->fetchObject()) {
        echo "<div><div>$achievement->name</div>";
            $action_statement=$connection->query("select * from actions where active=1 and achievement_id=$achievement->id");
            $action_statement->execute();
            while ($action=$action_statement->fetchObject()){
                echo "<div style='margin-left:16px;'>";
		if (has_achievement_been_worked_on($action->id)){
			echo $action->work . " " . date("m/d/y", when_last_worked($action->id)) . " " . days_since_last_worked($action->id) . "<BR>";
		}
                display_action($action);
                echo "</div>";
            }
        echo "</div>";
    }
}

function list_current_actions() {
    global $connection;
    $statement = $connection->query("select * from actions where reference=0 and active=1");
    $statement->execute();
    echo "<option></option>";
    while ($action = $statement->fetchObject()) {
        echo "<option value='$action->id'>$action->name</option>";
    }
}

function list_actions($achievement_id) {
    global $connection;
    $statement = $connection->prepare("select * from actions where active=1 and achievement_id=? order by name");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($action = $statement->fetchObject()) {
        echo "<div>
        <input type='button' value='X' onclick=\"DeleteAction($action->id, $action->achievement_id)\" />
        $action->name</div>";
    }
}

function list_work($work) {
    global $connection;
    $statement = $connection->prepare("select * from actions where active=1 and work=? and reference=0 order by name");
    $statement->bindValue(1, $work, PDO::PARAM_INT);
    $statement->execute();
    while ($action = $statement->fetchObject()) {
        display_action($action);
    }
}

function display_all_unfinished_actions($begin, $end) {
    global $connection;
    $begin = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $begin)));
    $end = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $end)));
    $query = "select name, created, updated from actions where id not in (select action_id from work where created>'$begin' and created<'$end')";
    //$statement=$connection->query()
    echo $query;
}

