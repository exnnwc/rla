<?php
define ("DB_HOST", "localhost");
define ("DB_NAME", "rla");
define ("DB_PWD", "");
define ("DB_USER", "root");
define ("SITE_NAME", "Real Life Achievements");
define ("SITE_ROOT", "http://" . $_SERVER['SERVER_NAME'] . "/rla"   );
define ("DEFAULT_LISTING", "where deleted=0 and parent=0 and completed=0");
