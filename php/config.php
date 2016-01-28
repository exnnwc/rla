<?php

define ("SITE_NAME", "Real Life Achievements");
if ($_SERVER['SERVER_NAME']=="rla.dev"){
	define ("SITE_ROOT", "http://" . $_SERVER['SERVER_NAME']  );
} else {
	define ("SITE_ROOT", "http://" . $_SERVER['SERVER_NAME'] . "/rla"   );
}
define ("DB_HOST", "localhost");
define ("DB_NAME", "rla");
define ("DB_PWD", "");
define ("DB_USER", "root");
