<?php
require_once("work.php");

include_once ("config.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
$sort_by=filter_input(INPUT_POST, 'sort_by', FILTER_SANITIZE_STRING);
echo "<table style='text-align:center;'>" . fetch_table_header();
$statement = $connection->query("select * from achievements where active=1 and parent=0 and completed=0" . fetch_order_query($sort_by));
while ($achievement = $statement->fetchObject()) {
    echo fetch_listing_row($achievement);
}
echo "</table>";
list_completed_achievements();

function fetch_listing_menu($achievement) {
    $string = " <tr><td>
                    <input class='new_shit' type='button' value='X' 
                        onclick=\"deleteAchievement($achievement->id, $achievement->parent, 0);\" />
                </td><td>
                    <input id='down_rank_$achievement->id' type='button' class='down_rank_button' value='-' />
                    <input id='change_rank_$achievement->id' type='text' 
                        class='change_rank' value='$achievement->rank' style='width:32px;text-align:center;' 
                        onkeypress=\"if (event.keyCode===13){changeRank($achievement->id, this.value, false, $achievement->parent);}\" />
                    <input id='up_rank_$achievement->id' type='button' class='up_rank_button' value='+' />
                </td>
                <td>$achievement->power</td>
                <td>$achievement->power_adj</td>
                <td><input id='turn_work_on_$achievement->id' type='button' 
                        class='change_work_button' value='" . convert_work_num_to_caption($achievement->work) . "' 
                        onclick=\"toggleWorkStatus($achievement->id, $achievement->work, $achievement->parent);\"/>
                </td><td>";
    $string = $achievement->quality 
            ? $string . "<input type='button' value='On' onclick=\"changeQuality($achievement->id, false);\"/></td><td>" 
            : $string . "<input type='button' value='Off' onclick=\"changeQuality($achievement->id, true);\"/></td><td>"; 
    $string=$string . "<input type='button' value='Complete' onclick=\"completeAchievement($achievement->id);\"/></td>";    
    return $string;
}

function fetch_listing_row($achievement) {
    $string = fetch_listing_menu($achievement)
            . " <td style='text-align:left'>
                <a href='" . SITE_ROOT . "/?rla=$achievement->id' style='text-decoration:none;";
    if ($achievement->quality) {
        $string = $string . "color:gray;";
    } else if ($achievement->work) {
        $string = $string . "color:green;";
    } else if (!$achievement->work) {
        $string = $string . "color:red;";
    }
    $string = $string . "    '>
                                $achievement->name 
                            </a></td></tr>";
    return $string;
}

function fetch_order_query($sort_by) {
    //I understand why this was flagged.  I could just reference an array.
    $order_by = ["default" => " order by quality asc, rank asc",
        "power" => " order by power asc",
        "powerrev" => " order by power desc, rank asc",
        "power_adj" => " order by power_adj asc",
        "power_adjrev" => " order by power_adj desc, rank asc",
        "rank" => " order by rank asc",
        "rankrev" => " order by rank desc",
        "created" => " order by created asc",
        "createdrev" => " order by created desc",
        "name" => " order by name asc",
        "namerev" => " order by name desc",
        "work" => " order by work",
        "workrev" => " order by work desc"];
    return $order_by[$sort_by];
}

function fetch_table_header() {
    return
            "<tr><td>
                X
            </td>
            <td>Rank</td>
            <td>Power</td>
            <td>Power (Adj)</td>
            <td>
                <a href = '" . SITE_ROOT . "/work/' style = 'color:black;'>Work</a>
            </td>
            <td>Quality</td>
            <td>Complete?</td>
            <td>Achievement Name</td>
            </tr>";
}

function list_completed_achievements(){
    global $connection;    
    echo "<h3 style='text-align:center;'>Completed Achievements</h3>";
    $statement=$connection->query("select count(*) from achievements where active=1 and completed!=0");
    if ((int)$statement->fetchColumn()==0){
        echo "<div>None.</div>";
        return;
    }
    $statement=$connection->query("select * from achievements where completed!=0");
    while ($achievement=$statement->fetchObject()){
        echo "  <div>
                    
                    <span style='font-weight:bold'>$achievement->name </span>
                        <div>
                            <span>Created:". date("m/d/y", strtotime($achievement->created)) ."</span>            
                            <span>Completed:". date("m/d/y", strtotime($achievement->completed)) ."</span>
                            <input type='button' value='Cancel' onclick=\"uncompleteAchievement($achievement->id);\" />                                
                        </div>
                </div>";
    }
    
}