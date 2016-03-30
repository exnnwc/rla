<?php
define ("DB_HOST", "localhost");
define ("DB_NAME", "rla");
define ("DB_PWD", "");
define ("DB_USER", "root");
define ("DOC_ROOT", "/opt/lampp/htdocs/rla/");
define ("SITE_NAME", "Real Life Achievements");
define ("SITE_ROOT", "http://" . $_SERVER['SERVER_NAME'] . "/rla"   );
define ("DEFAULT_LISTING", "where published=0 and deleted=0 and abandoned=0 and parent=0 and completed=0");
define ("DEFAULT_WHERE", "where published=0 and deleted=0 and abandoned=0 and completed=0");
define ("SECS_BTWN_REGISTRATIONS", 60); //in seconds 

if (session_status()==PHP_SESSION_NONE){
    session_start();
}

TESTING TO SEE IF THIS GETS PUT ON GITHUB
NEW TEST
TEST #4
