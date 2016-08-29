<?php
//This file establishes a database connection

include_once "ez_sql_core.php";
//include_once "ez_sql_postgresql.php";
include_once "ez_sql_oracle8_9.php";

$db = new ezSQL_oracle8_9('petdb','r0cksr0ck','localhost/petdb','iiillll');

?>