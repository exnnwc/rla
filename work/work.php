<?php

include ("../config.php");
$connection = new PDO("mysql:host=localhost;dbname=rla", "root", "");



function cancel_work($action_id) {
    echo $achievement_id;
    global $connection;
    $statement = $connection->prepare("update work set active=0 where active=1 and action_id=? order by created desc limit 1");
    $statement->bindValue(1, $action_id, PDO::PARAM_INT);
    $statement->execute();
}

function change_work($id, $work) {
    //echo "$id $work";
    global $connection;
    $statement = $connection->prepare("update achievements set work=? where id=?");
    $statement->bindvalue(1, $work, PDO::PARAM_INT);
    $statement->bindvalue(2, $id, PDO::PARAM_INT);
    $statement->execute();
}

function check_work() {
//Weekly and monthly check only work on Sunday or 1st day of the month. Need to check if it hasn't been checked before.
    global $connection;
    $check = 1;
    while ($check < 4) {
        $statement = false;
        switch ($check) {
            case 1:
                $statement = $connection->query("select * from actions where active=1 and work=2
		        and id not in (select action_id from work 
			where worked=true and date(created)=current_date-interval 1 day)");
                break;
            case 2:
                if (date("D", time()) == "Sun") {
                    $statement = $connection->query("select * from actions 
                            where active=1 and work=3 and id not in (select action_id from work 
				where  worked=true and week(created)=week(current_date-interval 1 week))");
                }
                break;
            case 3:               
                if (date("j", time()) == "1") {
                    echo "SUP";
                    var_dump(date("j", time()));
                    $statement = $connection->query("select * from actions where active=1 and work=4
		            and id not in (select action_id from work 
				where worked=true and  month(created)=month(current_date-interval 1 month)");
                }
                break;
        }
        if ($statement) {
            $statement->execute();
            while ($action = $statement->fetchObject()) {
                $work=$action->work;
                //This only works for the daily checks. Not weekly or monthly.
                if ($work==2){
                    $created="(current_date-interval 1 day)";
                } else {
                    $created="current_date";
                }
                echo "<div style='color:grey;'>'$action->name' has not been worked. Creating fail record in work log... $action->work</div>";
		$connection->exec("insert into work (action_id, work, created, worked, summary) values ($action->id, $action->work, $created, false, 'test')");
            }
            $connection->exec("insert into work (action_id, work,  worked, summary) values (0, $work, false, 'v1')");
        }
        $check++;
    }
}

function create_work($action_id) {
    global $connection;
    $action=fetch_action($action_id);
    $statement = $connection->prepare("insert into work (action_id, work) values (?, $action->work)");
    $statement->bindValue(1, $action_id, PDO::PARAM_INT);
    $statement->execute();
}



function fetch_achievement($id) {
    global $connection;
    $statement = $connection->prepare("select * from achievements where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}


function days_since_last_worked($action_id) {
    global $connection;
    if (when_last_Worked($action_id) == "12/31/69") {
        return false;
    }
    $statement = $connection->prepare("select datediff(curdate(), created) as days from work where action_id=? and active=1 order by created desc limit 1");
    $statement->bindValue(1, $action_id, PDO::PARAM_INT);
    $statement->execute();
    return (int) $statement->fetchColumn();
}



function display_new_action_options($id) {
    global $connection;
    $query = "select * from achievements where active=1 and id not in (select achievement_id from actions where active=1 and (id=? or reference=?)) order by name";
    $statement = $connection->prepare($query);
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->execute();
    while ($achievement = $statement->fetchObject()) {
        echo "<option value='$achievement->id'>$achievement->name</option>";
    }
}

function has_achievement_been_worked_on($id){
	global $connection;
	$achievement=fetch_achievement($id);
        $statement=$connection->prepare("select count(*) from actions where achievement_id=? and active=1");
	$statement->bindValue(1, $id, PDO::PARAM_INT);
	$statement->execute();
        if ($statement->fetchColumn()==0){
            return false;                
        }
	$statement=$connection->prepare("select * from actions where achievement_id=? and active=1");
	$statement->bindValue(1, $id, PDO::PARAM_INT);
	$statement->execute();
	while ($action=$statement->fetchObject()){
		if (!has_action_been_worked_on($action->id)){
			return false;
		}
	}
        
	return true;
}

function has_action_been_worked_on($action_id) {
    global $connection;
    $action = fetch_action($action_id);
    if (when_last_worked($action_id) == "12/31/69") {
        return false;
    } else {
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
                    //echo "a";
                    return false;
                } else if (days_since_last_worked($action_id) < 7) {
                    //echo "b";
                    return true;
                } else {
                    //echo "c";
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
}

function has_work_been_checked() {
    global $connection;
    $statement = $connection->query("select created from work where action_id=0 and active=1 order by created desc limit 1");
    $statement->execute();

    if (date("m/d/y", strtotime($statement->fetchColumn())) == date("m/d/y", time())) {
        return true;
    } else {
        return false;
    }
}

function when_last_worked($action_id) {
    global $connection;
    $statement = $connection->prepare("select created from work where action_id=? and active=1 and worked=1 order by created desc limit 1");
    $statement->bindValue(1, $action_id, PDO::PARAM_INT);
    $statement->execute();
    return strtotime($statement->fetchColumn());
}


function should_it_have_been_worked_on($id) {
    global $connection;
    $action = fetch_action($id);
    $days_since_last_worked = days_since_last_worked($id);

    if (!$days_since_last_worked) {
        return false;
        //deal with when it has no previous work history
    } else {
        if ($action->work == 2 && $days_since_last_worked > 0) {
            return true;
        } else if ($action->work == 3 && $days_since_last_worked > 6) {
            return true;
        } else if ($action->work == 4 && $days_since_last_worked > 28) {
            return true;
        } else {
            return false;
        }
    }
}




