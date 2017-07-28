<?php
//This file establishes a database connection

include_once "ez_sql_core.php";
//include_once "ez_sql_postgresql.php";
include_once "ez_sql_oracle8_9.php";

//put appropriate connection information here
$db = new ezSQL_oracle8_9('dbname','password','dbpath','iiillll');
?>
