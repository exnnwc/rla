<?php

include_once ("../php/config.php");
include_once ("../php/work.php");
include_once ("../php/actions.php");
include_once ("../php/achievements.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);

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
        echo "<h2>" . date("m/d/y l", strtotime($work->created)) . "</h2>";
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

