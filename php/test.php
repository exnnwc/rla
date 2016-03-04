<?php

$tld_file=file_get_contents("http://data.iana.org/TLD/tlds-alpha-by-domain.txt");
$tlds=explode("\n", $tld_file);
unset($tlds[0]);
var_dump($tlds);