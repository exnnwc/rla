<?php
include_once ("config.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
switch (filter_input(INPUT_POST,'function_to_be_called', FILTER_SANITIZE_STRING)){

    
}

function create_note($note, $achievement_id, $edit){
    global $connection;
    $statement=$connection->prepare("insert into notes (body, achievement) values (?, ?)");
    $statement->bindValue(1, $note, PDO::PARAM_STR);
    $statement->bindValue(2, $achievement_id, PDO::PARAM_INT);
   // $statement->bindValue(3, $edit, PDO::PARAM_INT);
    $statement->execute();
}

function edit_note($note, $achievement_id, $edit){
    //Thought I could just create a new one but the creation date needs to be preserved from the original. Maybe I could just add it on after.
}

function delete_note($id){
    global $connection;
    $statement=$connection->prepare("update notes set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}

function list_notes($achievement_id){
    global $connection;
    $statement=$connection->prepare("select * from notes where active=1 and achievement=? order by created desc");
    $statement->bindValue(1, $achievement_id, PDO::PARAM_INT);
    $statement->execute();
    while ($note=$statement->fetchObject()){
        echo "<div style='background-color:lightgray;width:800px;'>
                <h6 style='background-color:white';margin:0px;>
                    <input type='button' value='X' onclick=\"deleteNote($note->id, $note->achievement);\" /> "
                    .date("m/d/y h:i:s", strtotime($note->created))
              ."</h6>
                <div style='padding:12px;'>".
        str_replace("\n", "<BR>", $note->body);
        echo   "</div>
            </div>";
    }
}
