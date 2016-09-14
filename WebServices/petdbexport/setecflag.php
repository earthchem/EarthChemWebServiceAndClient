<?
/*
This script accepts either a minimun sample num (snum_min) and a maximum
sample num (snum_max) or just a sample num (snum) and sets the ec
populated flag based on the flag variable. (set unset)
*/

include("db.php");

$snum_min=$_GET['snum_min'];
$snum_max=$_GET['snum_max'];
$snum=$_GET['snum'];
$flag=$_GET['flag'];

if($snum_min!="" and $snum_max!=""){
	$thismin=$snum_min;
	$thismax=$snum_max;
}elseif($snum!=""){
	$thismin=$snum;
	$thismax=$snum;
}else{
	echo "error. sample info not given";
	exit();
}


if($flag=='set'){
	$thisflag='true';
}elseif($flag=='unset'){
	$thisflag='false';
}else{
	echo "error. flag not set correctly.";
	exit();
}


//Set the flag in the database based on varibles given.
$db->query("update sampletable set ecflag=$thisflag where samplenumber >= $thismin and samplenumber <= $thismax");



?>