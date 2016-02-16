<?php

require_once ("../php/config.php");
require_once ("../php/work.php");
require_once ("../php/actions.php");
require_once ("../php/achievements.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);

$today = 0;
$last_time = 0;
if (!has_work_been_checked()) {
    check_work();
}
$statement = $connection->query("select * from work where action_id!=0 and active=1 order by created desc");
$statement->execute();
while ($work = $statement->fetchObject()) {
    $action = fetch_action($work->action_id);
    $achievement = fetch_achievement($action->achievement_id);

    if ($today != date("m/d/y", strtotime($work->created))) {
        echo "<h2>" . date("m/d/y l", strtotime($work->created)) . "</h2>";
        $today = date("m/d/y", strtotime($work->created));
    }
    if ($last_time != date("H:i", strtotime($work->created))) {
        echo "<div style='margin-top:24px'>" . date("H:i", strtotime($work->created)) . "</div>";
        $last_time = date("H:i", strtotime($work->created));
    }
    echo "<div style='margin-top:8px'>";
    if (!strtotime($work->updated) && $work->worked) {
        echo " <input style='margin-right:8px;' type='button' value='X' onclick=\"cancelWork($action->id);\" />";
    }
    echo $work->worked ? "Finished " : "<span style='color:red;'>Incompleted </span> ";
    echo strtolower(convert_work_num_to_caption($work->work)) . " work on \"$action->name\"";

    if (strtotime($work->updated)) {
        echo " then cancelled at " . date("H:i:s", strtotime($work->created)) . " </span>";
    }
    echo "</div>";
}

