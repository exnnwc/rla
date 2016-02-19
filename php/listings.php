<?php
require_once("work.php");

require_once ("config.php");
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
                    <input id='delete$achievement->id' class='delete_achievement_button' type='button' value='X' />                        
                </td><td>
                    <input id='rank$achievement->id' type='number' 
                        class='change_rank_button' value='$achievement->rank' style='width:32px;text-align:center;' />                    
                </td>
                <td>$achievement->power</td>
                <td>$achievement->power_adj</td>
                ";
    $string = $string . fetch_next_three_menu_cells($achievement);
            
        
    return $string;
}

function fetch_next_three_menu_cells($achievement){
    return  $achievement->quality 
            ? " <td>
                    N/A
                </td><td>
                    <input id='0quality$achievement->id' class='change_quality_button' type='button' value='On'\"/>
                </td><td>
                    N/A
               </td>" 
            : " <td>
                    <input id='work$achievement->id' type='button' 
                      class='change_work_button' value='" . convert_work_num_to_caption($achievement->work) . "' />
                    <input id='work_status$achievement->id' type='hidden' value='". json_encode ((int)$achievement->work)."' />
                </td><td>
                    <input id='1quality$achievement->id' class='change_quality_button'  type='button' value='Off'\"/>
                </td><td>
                    <input id='complete$achievement->id' class='complete_button' type='button' />
                </td>"; 
    
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
    //TEST $sort_by that the key is appropriate first.
    $order_by = 
       ["default" => " order by quality asc, rank asc",
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
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);    
    echo "<h3 style='text-align:center;'>Completed Achievements</h3>";
    $statement=$connection->query("select count(*) from achievements where active=1 and completed!=0");
    if ((int)$statement->fetchColumn()==0){
        echo "<div>None.</div>";
        return;
    }
    $statement=$connection->query("select * from achievements where completed!=0");
    while ($achievement=$statement->fetchObject()){
        echo "  <div>
                    
                    <span style='font-weight:bold'>
                        <a href='" . SITE_ROOT . "/?rla=$achievement->id' style='text-decoration:none;'>
                        $achievement->name                             
                        </a>
                    </span>
                        <div>
                            <span>Created:". date("m/d/y", strtotime($achievement->created)) ."</span>            
                            <span>Completed:". date("m/d/y", strtotime($achievement->completed)) ."</span>
                            <input id='cancel$achievement->id' class='cancel_completion_button' type='button' value='Cancel' />                                
                        </div>
                </div>";
    }
    
}