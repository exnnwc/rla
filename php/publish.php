<?php 
function publish_achievement($id){
    if (!user_owns_achievement($id)) {
        //BAD
        return;
    }
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $achievement = fetch_achievement($id);
    if ($achievement->parent!=0){
        //Send a confirmation to user that they will lose the parent achievement in this hierarchy.
    } 
    $statement = $connection->prepare("insert into achievements (
}
