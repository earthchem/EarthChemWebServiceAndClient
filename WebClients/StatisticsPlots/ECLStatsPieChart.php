<?php
/* This script will draw ECL download purpose pie chart on fly
* @Author: Lulin Song created on Sept. 14, 2016
*
*/
require_once 'ECLStatsPlot.php';

$pieChartData = json_decode( ECLStatsPlot::getPieChartData());

foreach ($pieChartData as $index => $value )
{
  if(strtolower($index) =='education') $eduCnt = $value;
  if(strtolower($index) =='research') $resCnt = $value;
  if(strtolower($index) =='other') $otherCnt = $value;
  if(strtolower($index) =='null') $nullCnt = $value;
  if(strtolower($index) =='commercial use') $commerCnt = $value;
  if(strtolower($index) =='total') $totalCnt = $value;
  if(strtolower($index) =='uniqueip') $ipCnt = $value;
  if(strtolower($index) =='start_date') $start_date = $value;
  if(strtolower($index) =='end_date') $end_date = $value;
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
          title: 'ECL Download Statistics (<?= $start_date ?> to <?= $end_date?>)\n* <?=$totalCnt?> dataset downloads\n* <?=$ipCnt?> unique IP addresses',
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
