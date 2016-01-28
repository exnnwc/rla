<?php

include_once("../php/work.php");



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





function display_queue() {
    global $connection;
    $statement = $connection->query("select * from achievements where active=1 and quality=false and work>0 order by work asc");
    $statement->execute();
    while ($achievement = $statement->fetchObject()) {
        if (!has_achievement_been_worked_on($achievement->id) && is_it_the_appropriate_day($achievement->work)) {
            echo "<div><div style='font-weight:bold;'><a href='" . SITE_ROOT . "/?rla=$achievement->id'>$achievement->name</a></div>";
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
function list_actions($achievement_id) {
    global $connection;
    $statement = $connection->prepare("select * from actions where active=1 and achievement_id=? order by name");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($action = $statement->fetchObject()) {
        echo "<div>
        <input type='button' value='X' onclick=\"deleteAction($action->id, $action->achievement_id)\" />
        $action->name</div>";
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
