<?php
require_once("../php/classes/App.php");
$var=[];
$function=[];
$app= new App();
$uri = $_SERVER['REQUEST_URI'];
$type = $_SERVER['REQUEST_METHOD'];
if ($type="GET"){
    $content = $_GET;
} else if ($type="POST"){
    $content= $_POST;
}

$app->run($uri, $type, $content);
