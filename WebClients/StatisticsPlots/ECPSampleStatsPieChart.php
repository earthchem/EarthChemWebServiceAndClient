<?php
/* This script will draw ECP download purpose pie chart on fly
* @Author: Sean Cao created on 03/21/2023
*
*/
require_once 'ECPStatsPlot.php';

$ECPStatsPlot = new ECPStatsPlot("http://portal.earthchem.org/holdings", array("disp"=>"xml"));
$pieData = json_decode( $ECPStatsPlot->getHoldingsData() );

$navdatSamples = (int)str_replace(',', '', $pieData->NAVDAT->samples);
$earthchemSamples = (int)str_replace(',', '', $pieData->EarthChem->samples);
$georocSamples = (int)str_replace(',', '', $pieData->GEOROC->samples);
$usgsSamples = (int)str_replace(',', '', $pieData->USGS->samples);
$seddbSamples = (int)str_replace(',', '', $pieData->SedDB->samples);
$metpetdbSamples = (int)str_replace(',', '', $pieData->MetPetDB->samples);
$darwinSamples = (int)str_replace(',', '', $pieData->DARWIN->samples);
$otherSamples = $seddbSamples + $metpetdbSamples + $darwinSamples;
$totalSamples = $pieData->TOTAL->samples;

$navdatChemical = (int)str_replace(',', '', $pieData->NAVDAT->analyses);
$earthchemChemical = (int)str_replace(',', '', $pieData->EarthChem->analyses);
$georocChemical = (int)str_replace(',', '', $pieData->GEOROC->analyses);
$usgsChemical = (int)str_replace(',', '', $pieData->USGS->analyses);
$seddbChemical = (int)str_replace(',', '', $pieData->SedDB->analyses);
$metpetdbChemical = (int)str_replace(',', '', $pieData->MetPetDB->analyses);
$darwinChemical = (int)str_replace(',', '', $pieData->DARWIN->analyses);
$otherChemical = $seddbChemical + $metpetdbChemical;
$totalChemical = $pieData->TOTAL->analyses;
?>
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        const data = [
          ['NAVDAT', <?= $navdatSamples?>, customTooltip('NAVDAT')],
          ['EarthChem', <?= $earthchemSamples ?>, customTooltip('EarthChem')],
          ['GEOROC', <?= $georocSamples ?>, customTooltip('GEOROC')],
          ['USGS', <?= $usgsSamples ?>, customTooltip('USGS')],
          ['Other', <?= $otherSamples ?>, customTooltip('Other')]
        ]

        // const dataTable = google.visualization.arrayToDataTable();

        const dataTable = new google.visualization.DataTable();
        dataTable.addColumn('string', 'Year');
        dataTable.addColumn('number', 'Sales');
        // A column for custom tooltip content
        dataTable.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});
        dataTable.addRows(data);

        const options = {
          title: 'EarthChem Portal Sample Statistics\n* <?=$totalSamples?> total samples',
          titleTextStyle: {'color':'#893d12'},
          // colors: ['#e6693e', '#6a88c1', '#8ff7b6', '#f3b49f', '#e6693e'],
          backgroundColor: '#f7f7f7',
          sliceVisibilityThreshold: 0,
          pieSliceText: 'none',
          tooltip: {isHtml: true},
          legend: { position: 'labeled',labeledValueText: 'both', }
        };

        const chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(dataTable, options);
      }

      function customTooltip(database) {
        let sampleData;
        let chemicalData;
        if (database == 'NAVDAT') {
          sampleData = <?=$navdatSamples?>;
          chemicalData = <?=$navdatChemical?>;
        }
        if (database == 'EarthChem') {
          sampleData = <?=$earthchemSamples?>;
          chemicalData = <?=$earthchemChemical?>;
        }
        if (database == 'GEOROC') {
          sampleData = <?=$georocSamples?>;
          chemicalData = <?=$georocChemical?>;
        }
        if (database == 'USGS') {
          sampleData = <?=$usgsSamples?>;
          chemicalData = <?=$usgsChemical?>;
        }
        if (database == 'Other') {
          sampleData = <?=$otherSamples?>;
          chemicalData = <?=$otherChemical?>;
        }
        return '<div style="padding:5px 20px;font-color:#893d12;font-size:16px;">'+
            '<p>' + database + '</p>' +
            '<p>' + sampleData.toLocaleString("en-US") +' samples</p>' +
            '<p>' + chemicalData.toLocaleString("en-US") +' chemical values</p>' +
            '</div>';
      }
    </script>
  </head>
  <body>
      <div id="piechart" style="width: 900px; height: 500px;"></div>
  </body>

</html>
