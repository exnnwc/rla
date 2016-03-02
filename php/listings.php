<?php

session_start();

require_once ("config.php");
require_once("tags.php");
require_once("work.php");

$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
$sort_by = filter_input(INPUT_POST, 'sort_by', FILTER_SANITIZE_STRING);
if ($sort_by == "default") {
    $sort_by = isset($_SESSION["sort_by"]) ? $_SESSION["sort_by"] : "rank";
}
$_SESSION["sort_by"] = $sort_by;

echo "<table style='text-align:center;'>" . fetch_table_header($sort_by);

$filter = isset($_POST['filter']) ? $_POST['filter'] : "default";
if ($filter != "default") {
    $query = process_filter_to_query($filter);
    $_SESSION['filter'] = $_POST['filter'];
} else if ($filter == "default") {
    $query = isset($_SESSION['filter']) ? process_filter_to_query($_SESSION['filter']) : process_filter_to_query($filter);
}

  $statement = $connection->query("select * from achievements " . $query . fetch_order_query($sort_by));

  while ($achievement = $statement->fetchObject()) {
  echo fetch_listing_row($achievement);
  }
  echo "</table>";
  list_completed_achievements();


function fetch_order_query($sort_by) {
    $order_by = ["default" => " order by quality asc, rank asc",
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

function fetch_table_header($sort_by) {
    $headers = ["Rank", "Power", "Active", "Name"];
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

function fetch_work_button($achievement) {

    return $string;
}

function list_completed_achievements() {
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    echo "<h3 style='text-align:center;'>Completed Achievements</h3>";
    $statement = $connection->query("select count(*) from achievements where active=0 and completed!=0");
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

function process_filter_to_query($filter) {
    $generic_query = "where deleted=0 and parent=0 and completed=0 ";

    if ($filter == "clear" || $filter == "default" || empty($filter)) {
        return $generic_query;
    }
    $query = $generic_query;
    if (isset($filter["filter_tags"])) {
        foreach ($filter["filter_tags"] as $tag) {
            $tag = fetch_tag($tag);
            $tag_filter = !isset($string) ? " name=\"$tag->name\"" : $tag_filter . " or name=\"$tag->name\"";
        }
        $tag_filter = $tag_filter . ")";
        $query = $query . " and 
                id in (select distinct achievement_id from tags where active=1 and $tag_filter";
    }
    if ($filter["required"]) {        
        $query = $query . "and id not in (select distinct required_for from requirements where active=1)";
    }
    return $query;
}
