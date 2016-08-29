<?php

//echo "test<br>";
//ini_set('display_errors', 'on'); 
//ini_set('error_reporting', 'E_ALL');
//error_reporting(E_ALL);

//test database connection

include("db.php");

//$mycount=$db->get_var("select count(*) from sample");

$rows=$db->get_results("select count(*) as count from q_geoobject");

//echo "x";
//print_r($rows);
//echo "x";



foreach($rows as $row){

	echo $row->COUNT."<br>";

}


?>