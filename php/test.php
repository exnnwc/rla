<?php
include ("config.php");
include ("../vendor/phpauth/phpauth/Auth.php");
include ("../vendor/phpauth/phpauth/Config.php");
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PWD);

$config = new PHPAuth\Config($connection);
$auth   = new PHPAuth\Auth($connection, $config);
