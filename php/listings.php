<?php



require_once ("config.php");
require_once("tags.php");
require_once("work.php");
$achievement_was_set=false;
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
$sort_by = filter_input(INPUT_POST, 'sort_by', FILTER_SANITIZE_STRING);
$user_id=fetch_current_user_id();

if ($user_id==false){
    echo "You must be logged into view this page.";
    return;
}

if ($sort_by == "default") {
    $sort_by = isset($_SESSION["sort_by"]) ? $_SESSION["sort_by"] : "rank";
}
$_SESSION["sort_by"] = $sort_by;

$filter = isset($_POST['filter']) ? $_POST['filter'] : "default";
if ($filter != "default") {
    $query = process_filter_to_query($filter);
    $_SESSION['filter'] = $_POST['filter'];
} else if ($filter == "default") {
    $query = isset($_SESSION['filter']) ? process_filter_to_query($_SESSION['filter']) : process_filter_to_query($filter);
}
echo "<table style='text-align:center;'>" . fetch_table_header($sort_by);


  $statement = $connection->query("select * from achievements " . $query . fetch_order_query($sort_by));

  while ($achievement = $statement->fetchObject()) {
    echo fetch_listing_row($achievement);
  }
  echo "</table>";
  list_completed_achievements();
    echo "<h3 style='text-align:center;'>
        <span id='show_abandoned' class='h-normal hand text-button' style='float:left;'>[ Show ] </span>
              <span id='hide_abandoned' class='h-normal hand text-button' style='display:none;float:left;'>[ Hide ] </span>
              Abandoned Achievements</h3>
              
              <div id='abandoned_achievements_list' style='display:none;'>";
    list_abandoned_achievements();
    echo "</div>";
function fetch_order_query($sort_by) {
    $order_by = ["default" => " order by quality asc, rank asc",
        "power" => " order by power asc",
        "powerrev" => " order by power desc, rank asc",
        "adjusted" => " order by power_adj asc",
        "adjusted_rev" => " order by power_adj desc, rank asc",
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

function fetch_table_header($sort_by) {
    $headers = ["Rank", "Power", "Adjusted",  "Name"];
    $string = "<tr>";
    foreach ($headers as $header) {
        $string = $string . "<td id='sort_";
        $string = $sort_by == strtolower($header) ? $string . strtolower($header) . "rev" : $string . strtolower($header);


        $string = $string . "_button' class='hand text-button sort_button'>";
        if ($sort_by == strtolower($header) . "rev") {
            $string = $string . "&uarr;";
        }
        $string = $string . $header;
        if ($sort_by == strtolower($header) || ($sort_by == "default" && $header == "Rank")) {
            $string = $string . "&darr;";
        }
        $string = $string . "</td>";
    }
    $string = $string . "</tr>";
    return $string;
}
function list_abandoned_achievements(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    
    $statement = $connection->query("select count(*) from achievements where abandoned=1 and deleted=0 and completed=0");
    if ((int) $statement->fetchColumn() == 0) {
        echo "<div>None.</div>";
        return;
    }
    $statement = $connection->query("select * from achievements where abandoned=1 and deleted=0 and completed=0");
    while ($achievement = $statement->fetchObject()) {
        echo "  <div>
                    <span id='delete$achievement->id' class='remove_achievement_button hand' style='color:darkred'>
                        [x]
                    </span>
                    <span style='font-weight:bold'>
                        <a href='" . SITE_ROOT . "/?rla=$achievement->id' style='text-decoration:none;color:black;'>
                        $achievement->name                             
                        </a>
                    </span>
                    
                    <span id='restore$achievement->id' class='restore_achievement_button hand text-button'>
                        [ Undo ]
                    </span>
                </div>";
    }
}    
    


function list_completed_achievements() {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    echo "<h3 style='text-align:center;'>Completed Achievements</h3>";
    $query="select count(*) from achievements where active=0 and completed!=0";
    $user_id=fetch_current_user_id();
    $query = $user_id==false
        ? $query . " and public=1"
        : $query . " and owner=$user_id";
    $statement = $connection->query($query);
    if ((int) $statement->fetchColumn() == 0) {
        echo "<div>None.</div>";
        return;
    }
    $statement = $connection->query("select * from achievements where completed!=0");
    while ($achievement = $statement->fetchObject()) {
        echo "  <div>
                    
                    <span style='font-weight:bold'>
                        <a href='" . SITE_ROOT . "/?rla=$achievement->id' style='text-decoration:none;'>
                        $achievement->name                             
                        </a>
                    </span>
                        <div>
                            <span>Created:" . date("m/d/y", strtotime($achievement->created)) . "</span>            
                            <span>Completed:" . date("m/d/y", strtotime($achievement->completed)) . "</span>
                            <input id='cancel$achievement->id' class='cancel_completion_button' type='button' value='Cancel' />                                
                        </div>
                </div>";
    }
}

function list_qualities() {
    echo "<h1>Qualities</h1>";
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("select * from achievements where quality=1 and deleted=0");
    while ($quality = $statement->fetchObject()) {
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

function display_tag_filters() {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection->query("select * from tags where active=1 and achievement_id=0");
    while ($tag = $statement->fetchObject()) {
        echo "<span>$tag->name($tag->tally)</span>";
    }
}

