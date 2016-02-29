<?php

function format_appropriately($string){
    $urls=[];
    $url_captions=[];
    $words=preg_split("/\s/", $string);
    foreach($words as $key=>$word){
       if (preg_match("/[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-]+/", $word)){
            $url_captions[]=$word;
            $urls[]=str_replace("http://", "", $word);
        }
    }
    if (count($urls)>0){
        foreach ($urls as $key=>$val){
            $string=str_replace($url_captions[$key], "<a href='http://$urls[$key]'>$url_captions[$key]</a>", $string);
        }
    }
    return nl2br($string);
}
