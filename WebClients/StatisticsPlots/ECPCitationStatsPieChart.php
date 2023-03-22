<?php
/* This script will draw ECP download purpose pie chart on fly
* @Author: Sean Cao created on 03/21/2023
*
*/
require_once 'ECPStatsPlot.php';

$ECPStatsPlot = new ECPStatsPlot("http://portal.earthchem.org/holdings", array("disp"=>"xml"));
$pieData = json_decode( $ECPStatsPlot->getHoldingsData() );

$navdatCnt = str_replace(',', '', $pieData->NAVDAT->citations);
$earthchemCnt = str_replace(',', '', $pieData->EarthChem->citations);
$georocCnt = str_replace(',', '', $pieData->GEOROC->citations);
$usgsCnt = str_replace(',', '', $pieData->USGS->citations);
$seddbCnt = str_replace(',', '', $pieData->SedDB->citations);
$metpetdbCnt = str_replace(',', '', $pieData->MetPetDB->citations);
$darwinCnt = str_replace(',', '', $pieData->DARWIN->citations);
$otherCnt = $seddbCnt + $metpetdbCnt + $darwinCnt;
$totalCnt = $pieData->TOTAL->citations;
?>
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Database', 'Citations'],
          ['NAVDAT', <?= $navdatCnt?>],
          ['EarthChem', <?= $earthchemCnt ?>],
          ['GEOROC', <?= $georocCnt ?>],
          ['USGS', <?= $usgsCnt ?>],
          ['SedDB', <?= $seddbCnt ?>],
          ['MetPetDB', <?= $metpetdbCnt ?>],
          ['DARWIN', <?= $darwinCnt ?>]
        ]);

        var options = {
          title: 'EarthChem Portal Citation Statistics\n* <?=$totalCnt?> total citations\n* USGS is not represented as their data does not come from citations',
          titleTextStyle: {'color':'#893d12'},
          // colors: ['#e6693e', '#6a88c1', '#8ff7b6', '#f3b49f', '#e6693e'],
          backgroundColor: '#f7f7f7',
          sliceVisibilityThreshold: 0,
          pieSliceText: 'none',
          tooltip: {isHtml: true},
          legend: { position: 'labeled', labeledValueText: 'value' }

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
