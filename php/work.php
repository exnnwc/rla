<?php

include_once ("config.php");
require_once("achievements.php");
require_once("actions.php");
require_once("display.php");
function cancel_work($action_id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("update work set active=0 where active=1 and action_id=? order by created desc limit 1");
    $statement->bindValue(1, $action_id, PDO::PARAM_INT);
    $statement->execute();
}

function check_work() {
//Weekly and monthly check only work on Sunday or 1st day of the month. Need to check if it hasn't been checked before.
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $check = 1;
    while ($check < 4) {
        $statement = false;
        switch ($check) {
            case 1:
                $statement = $connection->query("select * from actions where active=1 and work=2 and id not in 
                        (select action_id from work where active=1 and worked=true and date(created)=current_date-interval 1 day)");
                break;
            case 2:
                $statement = $connection->query("select count(*) from work where active=1 and action_id=0 and work=3 and dayofweek(created)=1;");
                if (date("D", time()) == "Sun" || (int) $statement->fetchColumn() == 0) {
                    $statement = $connection->query("select * from actions 
                            where active=1 and work=3 and id not in 
                            (select action_id from work where active=1 and worked=true and week(created)=week(current_date-interval 1 week))");
                }
                break;
            case 3:
                $statement = $connection->query("select count(*) from work where active=1 and action_id=0 and work=4 and dayofmonth(created)=1;");
                if (date("j", time()) == "1" || (int) $statement->fetchColumn() == 0) {
                    $statement = $connection->query("select * from actions where active=1 and work=4 and id not in 
                        (select action_id from work where active=1 and worked=true and month(created)=month(current_date-interval 1 month))");
                }
                break;
        }
        $statement->execute();
        while ($action = $statement->fetchObject() && is_object($action)) {
            $work = $action->work;
            //This only works for the daily checks. Not weekly or monthly.
            $created = "current_date";
            if ($work == 2) {
                $created = "(current_date-interval 1 day)";
            } else if ($work == 3) {
                $created = "DATE_SUB(DATE(NOW()), INTERVAL DAYOFWEEK(NOW())-1 DAY)";
            } else if ($work == 4) {
                $created = "date_sub(current_date, interval dayofmonth(now()) day)";
            }
            echo "<div style='color:grey;'>'$action->name' has not been worked. Creating fail record in work log... $action->work</div>";
            $connection->exec("insert into work (action_id, work, created, worked, summary) values ($action->id, $action->work, $created, false, 'v1')");
        }
        isset($work) ? $connection->exec("insert into work (action_id, work,  worked, summary) values (0, $work, false, 'v1')") : $connection->exec("insert into work (action_id, summary) values (0, 'v1')");


        $check++;
    }
}

function convert_work_num_to_caption($work) {
    //convert to an array 02/04/16
    switch ($work) {
        case "max_number":
            return 5;
            break;
        case 0:
            return "Inactive";
            break;
        case 1:
            return "Active";
            break;
        case 2:
            return "Daily";
            break;
        case 3:
            return "Weekly";
            break;
        case 4:
            return "Monthly";
            break;
        case 5:
            return "Instant";
            break;
    }
}

function create_work($action_id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $action = fetch_action($action_id);

    $statement = $connection->prepare("insert into work (action_id, work) values (?, $action->work)");
    $statement->bindValue(1, $action_id, PDO::PARAM_INT);
    $statement->execute();
}

function days_since_last_worked($action_id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    if (when_last_Worked($action_id) == "12/31/69") {
        return false;
    }
    $statement = $connection->prepare("select datediff(curdate(), created) as days from work where action_id=? and active=1 order by created desc limit 1");
    $statement->bindValue(1, $action_id, PDO::PARAM_INT);
    $statement->execute();
    return (int) $statement->fetchColumn();
}

function has_achievement_been_worked_on($id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select count(*) from actions where achievement_id=? and active=1");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    if ($statement->fetchColumn() == 0) {
        return false;
    }
    $statement = $connection->prepare("select * from actions where achievement_id=? and active=1");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($action = $statement->fetchObject()) {
        if (!has_action_been_worked_on($action->id)) {
            return false;
        }
    }

    return true;
}

function has_action_been_worked_on($action_id) {
    $action = fetch_action($action_id);
    if (when_last_worked($action_id) == "12/31/69") {
        return false;
    }
    if ($action->work == 1) {
        $action->work = 2;
    }
    switch ($action->work) {
        case 2:
            if (date("z", when_last_worked($action_id)) != date("z", time())) {
                return false;
            } else if (days_since_last_worked($action_id) < 1) {
                return true;
            } else {
                return false;
            }
            break;
        case 3:
            if (date("W", when_last_worked($action_id)) != date("W", time())) {
                return false;
            } else if (days_since_last_worked($action_id) < 7) {
                return true;
            } else {
                return false;
            }
            break;
        case 4:
            if (date("m", when_last_worked($action_id)) != date("m", time())) {
                return false;
            } else if (days_since_last_worked($action_id) < 28) {
                return true;
            } else {
                return false;
            }
            break;
    }
}

function has_work_been_checked() {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("select created from work where action_id=0 and active=1 order by created desc limit 1");
    $statement->execute();

    return (date("m/d/y", strtotime($statement->fetchColumn())) == date("m/d/y", time()));
}

function is_it_the_appropriate_day($work) {
    if ($work == 1 && (date("D", time()) == "Sun" || date("D", time()) == "Sat")) {
        return false;
    }
    return true;
}

function when_last_worked($action_id) {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->prepare("select created from work where action_id=? and active=1 and worked=1 order by created desc limit 1");
    $statement->bindValue(1, $action_id, PDO::PARAM_INT);
    $statement->execute();
    return strtotime($statement->fetchColumn());
}

function should_it_have_been_worked_on($id) {
    $action = fetch_action($id);
    $days_since_last_work = days_since_last_worked($id);

    if (!$days_since_last_work) {
        return false;
        //deal with when it has no previous work history
    }

    if ($action->work == 2 && $days_since_last_work > 0) {
        return true;
    } else if ($action->work == 3 && $days_since_last_work > 6) {
        return true;
    } else if ($action->work == 4 && $days_since_last_work > 28) {
        return true;
    }

    return false;
}
