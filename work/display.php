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
    echo
    "<span style='margin-left:20px;cursor:pointer;";

    if (has_action_been_worked_on($action->id)) {
        echo "color:grey;text-decoration:line-through;' 
                title='Cancel work'  
                onmouseover=\"$(this).css('text-decoration', 'none');\"  
                onmouseleave=\"$(this).css('text-decoration', 'line-through');\" 
                onclick=\"cancelWork($action->id);\"";
    } else {
        echo "' title='Click to indicate action worked.' 
                onmouseover=\"$(this).css('text-decoration', 'line-through');\" 
                onmouseleave=\"$(this).css('text-decoration', 'none');\" 
                onclick=\"createWork($action->id);\"";
    }
    echo "> $action->name</span>";
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
    $statement = $connection->query("select * from work where action_id!=0 and active=1 order by created desc");
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
            if (date("H:i", strtotime($work->created)) == "00:00") {
                echo "<h3 style='font-weight:bold;margin-bottom:0px;'> Incomplete </h3>";
            } else {
                echo "<div>" . date("H:i", strtotime($work->created));

                if (!strtotime($work->updated)) {
                    echo " - <span style='color:red;cursor:pointer;' 
                onmouseover=\"$(this).css('text-decoration', 'underline');\"  
                onmouseleave=\"$(this).css('text-decoration', 'none');\" 
                onclick=\"cancelWork($action->id);\" >Cancel</span>";
                }
                echo"</div>";
            }
            $last_time = date("H:i", strtotime($work->created));
        }

        //var_dump (strtotime($work->updated));
        echo "<div>";

        echo $work->worked ? "Finished " : "<span style='color:red;'>Failed</span> "; //Failed might be too harsh of a word.

        echo "[$work->work] work on \"$action->name\"";

        if (strtotime($work->updated)) {
            echo " then cancelled at " . date("H:i:s", strtotime($work->created));
        }

        echo "</div>";
    }
}

function display_queue() {
    global $connection;
    $statement = $connection->query("select * from achievements where active=1 and quality=false and work>0 order by work asc");
    $statement->execute();
    while ($achievement = $statement->fetchObject()) {
        if (!has_achievement_been_worked_on($achievement->id)) {
            echo "<div><div style='font-weight:bold;'>$achievement->name</div>";
            $action_statement = $connection->query("select * from actions where active=1 and achievement_id=$achievement->id");
            $action_statement->execute();
            while ($action = $action_statement->fetchObject()) {
                echo "<div style='margin-left:16px;'>";
                display_action($action);
                echo "</div>";
            }
            echo "</div>";
        }
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
