<?php
require_once("/opt/lampp/htdocs/rla/vendor/twig/twig/lib/Twig");

class View{   
    private $view;
    function load($view){
        $this->view = $view;
    }
    function display(){
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('/path/to/templates');
        $twig = new Twig_Environment($loader, array(
            'cache' => '/path/to/compilation_cache',
        ));
        echo $view;    
    }
}
