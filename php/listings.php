<?php


require_once ("config.php");
require_once("tags.php");
require_once("work.php");

$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
$sort_by=filter_input(INPUT_POST, 'sort_by', FILTER_SANITIZE_STRING);

echo "<table style='text-align:center;'>" . fetch_table_header();
if (isset($_POST['filter'])){
    $query=process_filter_to_query($_POST['filter']);
} else if (!isset($_POST['filter'])){
    $query=process_filter_to_query("default");   
}
$statement = $connection->query($query . fetch_order_query($sort_by));
while ($achievement = $statement->fetchObject()) {
    echo fetch_listing_row($achievement);
}
echo "</table>";
list_completed_achievements();

function fetch_listing_menu($achievement) {
    $string = " <tr><td>
                    <input id='rank$achievement->id' type='number' 
                        class='change_rank_button' value='$achievement->rank' style='width:50px;text-align:center;' />
                </td>
                <td title='$achievement->power'>$achievement->power_adj </td>
                ";
    $string = $string . fetch_next_three_menu_cells($achievement);
            
        
    return $string;
}

function fetch_next_three_menu_cells($achievement){
    return  $achievement->quality 
            ? " <td>
                    N/A
                </td><td>
                    <input id='0quality$achievement->id' class='change_quality_button' type='checkbox' checked \"/>
               </td>" 
            : "<td> " . fetch_work_button($achievement) . "
                </td>"; 
    
}
function fetch_listing_row($achievement) {
    $string = fetch_listing_menu($achievement)
            . " <td style='text-align:left'>
                <a href='" . SITE_ROOT . "/?rla=$achievement->id' style='text-decoration:none;";
    if ($achievement->active) {
        $string = $string . "color:green;";
    } else if (!$achievement->active) {
        $string = $string . "color:red;";
    }
    $string = $string . "    '>
                                $achievement->name 
                            </a></td></tr>";
    return $string;
}

function fetch_order_query($sort_by) {
    $order_by = 
       ["default" => " order by quality asc, rank asc",
        "power" => " order by power_adj asc",
        "powerrev" => " order by power_adj desc, rank asc",
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
            "<tr>
            <td>Rank</td>
            <td>Power</td>
            <td>Active</td>
            <td>Achievement Name</td>
            </tr>";
}
function fetch_work_button($achievement){

    $string="<input type='button'  id='activity$achievement->id' ";
    $string = !$achievement->active 
        ? $string . "class='activate_button' style='background-color:green;' />"
        : $string .  "class='deactivate_button' style='background-color:red;' />";
   return $string;
}
function list_completed_achievements(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);    
    echo "<h3 style='text-align:center;'>Completed Achievements</h3>";
    $statement=$connection->query("select count(*) from achievements where active=0 and completed!=0");
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
function list_qualities(){
    echo "<h1>Qualities</h1>";
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);    
    $statement = $connection->query("select * from achievements where quality=1 and deleted=0");
    while ($quality= $statement->fetchObject()){
        echo "  <div>
                    <span>
                        <input id='rank$quality->id' type='number' 
                          class='change_rank_button' value='$quality->rank' style='width:50px;text-align:center;' />
                    </span><span>
                        <a href='" . SITE_ROOT . "/?rla=$quality->id'";
            
        echo "          $quality->name
                    </span>
                </div>";
    }

}
function display_tag_filters(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);    
    $statement = $connection -> query ("select * from tags where active=1 and achievement_id=0");
    while ($tag = $statement->fetchObject()){
        echo "<span>$tag->name($tag->tally)</span>";
    }
    
}

function process_filter_to_query($filter){
    $generic_query="select * from achievements where deleted=0 and parent=0 and completed=0 and quality=0";

    if ($filter=="default" || empty($filter)){
        return $generic_query;
    }
    foreach($filter["filter_tags"] as $tag){                
        $tag=fetch_tag($tag);
         $string = !isset($string) 
            ? " name=\"$tag->name\"" 
            : $string .  " or name=\"$tag->name\"";
    }
        $string = $string . ")";
            return "$generic_query and 
                      id in (select distinct achievement_id from tags where active=1 and $string";
}
