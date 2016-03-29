<?php require_once("config.php");


$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
$user_profile_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$user_id = fetch_current_user_id();
if ($user_profile_id == 0 && $user_id!=false){
    $user_profile_id = $user_id;
} else if ($user_profile_id == 0 && $user_id==false){
    return;    
}
