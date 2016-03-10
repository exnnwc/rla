<?php

class Controller{
    private $data;
    function init($uri, $type, $content){
        $uri_arr=explode ("/" , $uri);
        for($i=0;$i<=2;$i++){
            unset($uri_arr[$i]);
        }
        
        foreach($uri_arr as $key=>$val){
            if (($key-1) % 2==0  && !empty($val)){
                $function[]=$val;
            } else if (($key-1) % 2!=0  && !empty($val)){
                $var[]=$val;
        
            }
        }
        if (sizeof($function)==1){
            switch($function[0]) {
                case "achievement":
                    if(!empty($val[0])){
                        $this->data= ["displayProfile", $val[0]];
                    }
                    break;
            }
        }
        //continue using the numbero f fuctions with how to proceed
    }

}
