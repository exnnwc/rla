<?php
session_start();
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
