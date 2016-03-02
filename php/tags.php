<?php
require_once ("config.php");
require_once ("achievements.php");
function check_tag_integrity(){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement=$connection->query("select * from tags where active=1 and achievement_id!=0");
    while ($tag=$statement->fetchObject()){
        $achievement=fetch_achievement($tag->achievement_id);
        if ($achievement->deleted){
            error_log("Line #".__LINE__ . " " . __FUNCTION__ . "(): Tag should have been deleted.");                
            deactivate_tag($tag->id);
        }
    }
    $statement = $connection -> query ("select * from tags where active=1 and achievement_id=0");
    while ($top_tag=$statement->fetchObject()){
        if ($top_tag->tally==0){
            error_log("Line #".__LINE__ . " " . __FUNCTION__ . "(): Top tag id #$top_tag->id's tally is 0.");                
            deactivate_tag($top_tag->id);
        } else if ($top_tag->tally>0){
            $statement=$connection->prepare("select count(*) from tags where active=1 and achievement_id!=0 and name=?");
            $statement->bindValue(1, $top_tag->name, PDO::PARAM_STR);
            $statement->execute();
            $num_of_associated_tags=(int)$statement->fetchColumn();
            if ($num_of_associated_tags==0){
                error_log("Line #".__LINE__ . " " . __FUNCTION__ . "(): No tags associated with top tag id #$top_tag->id");                
                deactivate_tag($top_tag->id);
            }
            if ((int)$top_tag->tally!=$num_of_associated_tags){
                error_log("Line #".__LINE__ . " " . __FUNCTION__ . "(): Top tag #$top_tag->id's tally does not match number of associated tags.");                                
               $connection->exec("update tags set tally=$num_of_associated_tags where id=$top_tag->id");
            }
        }
    }
        
}
function create_tag($achievement_id, $name){
    $name=trim($name);
    $is_a_new_tag=!is_it_already_tagged(0, $name);
    if ($is_a_new_tag){
        insert_tag_into_db(0,$name);  
    }
    
    if (!is_it_already_tagged($achievement_id, $name)){
        insert_tag_into_db($achievement_id,$name);
            $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
            $statement = $connection ->prepare("update tags set tally=tally+1 where active=1 and achievement_id=0 and name=?");
            $statement->bindValue(1, $name, PDO::PARAM_INT);
            $statement->execute();            
    }
}
function deactivate_tag($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection ->prepare("update tags set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}
function delete_tag ($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $tag=fetch_tag($id);
    deactivate_tag($id);
    $statement = $connection->prepare("update tags set tally=tally-1 where active=1 and achievement_id=0 and name=?");
    $statement->bindValue(1, $tag->name, PDO::PARAM_STR);
    $statement->execute();
    $connection->exec("update tags set active=0 where achievement_id=0 and tally=0");
}

function fetch_tag($id){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement=$connection -> prepare("selecT * from tags where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchObject();
}
function is_it_already_tagged($achievement_id, $tag){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection -> prepare ("select count(*) from tags where active=1 and achievement_id=? and name=? limit 1");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->bindValue(2, $tag, PDO::PARAM_STR);
    $statement->execute();
    return (boolean)$statement->fetchColumn();
}
function insert_tag_into_db($achievement_id,$name){
    $connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
    $statement = $connection -> prepare("insert into tags (name, achievement_id) values (?, ?)");
    $statement->bindValue(1, $name, PDO::PARAM_STR);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT); 
    $statement->execute();
}
