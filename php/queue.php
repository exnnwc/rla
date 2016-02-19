<?php

require_once ("config.php");
require_once("work.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
$statement = $connection->query("select * from achievements where active=1 and quality=false and work>0 order by rank asc");
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
$statement = $connection->query("select * from achievements where active=1 and quality=false and work>0 order by rank asc");
$statement->execute();
while ($achievement = $statement->fetchObject()) {
    if (has_achievement_been_worked_on($achievement->id) && is_it_the_appropriate_day($achievement->work)) {
        echo "  <div>
                        <div style='font-weight:bold;text-decoration:line-through;'>
                            <a href='" . SITE_ROOT . "/?rla=$achievement->id'>
                                $achievement->name [" . convert_work_num_to_caption($achievement->work) . "]
                            </a>
                        </div>";        
    }
}

