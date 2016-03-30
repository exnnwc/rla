<?php
define ("DB_HOST", "localhost");
define ("DB_NAME", "taqfu036_rla");
define ("DB_PWD", "taqfu036_dyex9mird8ecLJyOtkP6tArKiItC6E79");
define ("DB_USER", "taqfu036_rlaroot");
define ("DOC_ROOT", $_SERVER["DOCUMENT"] ."/rla/");
define ("SITE_NAME", "Real Life Achievements");
define ("SITE_ROOT", "http://" . $_SERVER['SERVER_NAME'] . "/rla"   );
define ("DEFAULT_LISTING", "where published=0 and deleted=0 and abandoned=0 and parent=0 and completed=0");
define ("DEFAULT_WHERE", "where published=0 and deleted=0 and abandoned=0 and completed=0");
define ("SECS_BTWN_REGISTRATIONS", 60); //in seconds 

if (session_status()==PHP_SESSION_NONE){
    session_start();
}

