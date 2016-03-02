<?php
if (session_status()==PHP_SESSION_NONE){
    session_start();
}
function fetch_required_filter_status(){
    echo isset($_SESSION['filter']['required']) 
        ? json_encode($_SESSION['filter']['required'])
        : json_encode(false);
    
}
function is_filter_active(){
    if (!isset($_SESSION['filter'])
      || (isset($_SESSION['filter']) && ($_SESSION['filter']=="clear" || $_SESSION['filter']=="default"))){
        echo json_encode(false);
        return;
    }
    echo json_encode(true);
}

function return_if_filter_active(){
    if (!isset($_SESSION['filter'])
      || (isset($_SESSION['filter']) && ($_SESSION['filter']=="clear" || $_SESSION['filter']=="default"))){
        return false;
    }
    return true;
}

function process_filter_to_query($filter) {
    $generic_query = DEFAULT_LISTING;

    if ($filter == "clear" || $filter == "default" || empty($filter)) {
        return $generic_query;
    }
    $query = $generic_query;
    if (isset($filter["filter_tags"])) {
        foreach ($filter["filter_tags"] as $tag) {
            $tag = fetch_tag($tag);
            $tag_filter = !isset($tag_filter) ? "(name=\"$tag->name\"" : $tag_filter . " or name=\"$tag->name\"";
        }
        $tag_filter = $tag_filter . "))";
        $query = $query . " and 
                id in (select distinct achievement_id from tags where active=1 and $tag_filter";
    }
    if ($filter["required"]) {        
        $query = $query . " and id not in (select distinct required_for from requirements where active=1)";
    }
    return $query;
}
