<?php
/* This script will draw ECL download purpose pie chart on fly
* @Author: Lulin Song created on Sept. 14, 2016
*
*/
require_once 'ECLStatsPlot.php';
date_default_timezone_set('America/New_York');
$nextyear  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-1);
$currentTime  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y") -0);
$monthOneYearBefore = date('Y-m-d', $nextyear);
$monthCurrentYear = date('Y-m-d', $currentTime);

$pieChartData = json_decode( ECLStatsPlot::getPieChartDataFromFile());

foreach ($pieChartData as $index => $value )
{
  if($index =='education') $eduCnt = $value;
  if($index =='research') $resCnt = $value;
  if($index =='other') $otherCnt = $value;
  if($index =='null') $nullCnt = $value;
  if($index =='commercial') $commerCnt = $value;
  if($index =='total') $totalCnt = $value;
  //if($index =='emailRatio') $emailr = intval($value*100);
  if($index =='uniqueip') $ipCnt = $value;
}

?>
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Download Purpose', 'Downloads'],
          ['Education',     <?= $eduCnt?>],
          ['Research',      <?= $resCnt ?>],
          ['Other',  <?= $otherCnt ?>],
          ['Not Reported', <?= $nullCnt ?>],
          ['Commercial Use',     <?= $commerCnt?>]
        ]);

        var options = {
          title: 'ECL Download Statistics (Apr 19, 2012 to Oct 31, 2017)\n* <?=$totalCnt?> dataset downloads\n* <?=$ipCnt?> unique IP addresses',
          titleTextStyle: {'color':'#893d12'},
          is3D: true,
          colors: ['#e6693e', '#6a88c1', '#8ff7b6', '#f3b49f','#835C3B']
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
      <div id="piechart" style="width: 900px; height: 500px;"></div>
  </body>

</html>
