<?php
/* This script will draw PetDB download purpose pie chart on fly
* @Author: Lulin Song created on Sept. 14, 2016
*
*/
require_once 'PetDBStatsPlot.php';
date_default_timezone_set('America/New_York');
$nextyear  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-1);
$currentTime  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y") -0);
$monthOneYearBefore = date('Y-m-d', $nextyear);
$monthCurrentYear = date('Y-m-d', $currentTime);

$data = json_decode(PetDBStatsPlot::getPieChartDataFromFile());
var_dump($data);
//PetDBStatsPlot::getMonthlyIPAndDownloadCountsFromFile();
//PetDBStatsPlot::getDaylyIPAndDownloadCountsFromFile();
?>
