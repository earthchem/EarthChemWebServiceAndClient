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
$monthCurrentYear = date('M d, Y', $currentTime);

$pieChartData = json_decode( PetDBStatsPlot::getPieChartData());

foreach ($pieChartData as $index => $value )
{
  if($index =='education') $eduCnt = $value;
  if($index =='research') $resCnt = $value;
  if($index =='other') $otherCnt = $value;
  if($index =='null') $nullCnt = $value;
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
          ['Not Reported', <?= $nullCnt ?>]
        ]);

        var options = {
          title: 'PetDB Download Statistics (Oct 12, 2012 to <?= $monthCurrentYear ?>)\n* <?=$totalCnt?> integrated dataset downloads\n* <?=$ipCnt?> unique IP addresses',
          titleTextStyle: {'color':'#893d12'},
          is3D: true,
          colors: ['#e6693e', '#6a88c1', '#8ff7b6', '#f3b49f']
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
