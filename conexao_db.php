<?php
$host = "host = teste-testedw.g.aivencloud.com;";
$port = "port = 22068;";
$dbname = "dbname = defaultdb";
$dbuser = "avnadmin";
$dbpassword = "AVNS_0okcPqAvFTp-s2F38uF";

$db_con = new PDO('pgsql:' . $host . $port . $dbname, $dbuser, $dbpassword);
$db_con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
