<?php
session_start();
function fetch_required_filter_status(){
    echo isset($_SESSION['filter']) 
        ? json_encode($_SESSION['filter']['required'])
        : json_encode(false);
        
    
    
}

