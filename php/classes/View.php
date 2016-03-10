<?php
class View{   
    private $view;
    function load($view){
        $this->view = $view;
    }
    function display(){
        echo $view;    
    }
}
