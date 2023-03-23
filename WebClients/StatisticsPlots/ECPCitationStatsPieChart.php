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
          colors: ['#D6706a', '#178497', '#89211B', '#364e55', '#364e55', '#45110e'],
          backgroundColor: '#f7f7f7',
          sliceVisibilityThreshold: 0,
          pieSliceText: 'none',
          tooltip: {isHtml: true},
          legend: { position: 'labeled', labeledValueText: 'value' },
          chartArea: {'width': '90%', 'height': '60%'},
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div style="max-width: 100%; padding-top: 50%; position: relative; width: 100%;">
      <div id="piechart" style="position: absolute; top: 0; left: 0;width: 100%; height: 100%;"></div>
    </div>
  </body>

</html>
