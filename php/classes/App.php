<?php
require_once("Model.php");
require_once("View.php");
require_once("Controller.php");
class App{
    private $view;
    private $model;
    private $controller;
    function __construct(){
        $this->view = new View();
        $this->model = new Model();
        $this->controller = new Controller();
    }
    function run($uri, $type, $content){
        $view = $this->view;
        $model = $this->model;
        $controller = $this->controller;
        $controller->init($uri, $type, $content);
        
    }

}
