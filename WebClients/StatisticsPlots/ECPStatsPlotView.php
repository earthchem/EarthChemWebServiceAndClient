<?php
/* This script will draw EarthChem Portal usage graph on fly
* @Author: Lulin Song created on April 18, 2013
*
* $Id: ECPStatsPlotView.php 1313 2016-08-23 19:30:28Z song $
* $LastChangedDate: 2016-08-23 15:30:28 -0400 (Tue, 23 Aug 2016) $
* $LastChangedBy: song $
* $LastChangedRevision: 1313 $
*/
require_once 'ECPStatsPlot.php';
date_default_timezone_set('America/New_York');
$nextyear  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-1);
//$monthOneYearBefore = date('Y-n', $nextyear);
$monthOneYearBefore = '2011-01';

$plotview = new ECPStatsPlot("http://www.earthchemportal.org/citation_stats",  
                              array("v"=>"xml","start_month"=>"$monthOneYearBefore") 
                            );
$plotData = json_decode( $plotview->getPlotArray() );

?>
<html>
<head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

	google.load("visualization", "1", {packages: ["corechart","table","annotatedtimeline"] });
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var dom_data = new google.visualization.DataTable();
		dom_data.addColumn('date', 'Month');
		dom_data.addColumn('number', 'Unique IP addresses');
		dom_data.addColumn('number', 'Unique data downloads');
                dom_data.addRows(<?= sizeof($plotData) ?>);
                 <?php
                 $idx=0;
                 //Important: When using this Date String Represenation, as when using the new Date() constructor, months are indexed starting at zero (January is month 0, December is month 11). 
                 foreach ($plotData as $index => $row )
                 {
                     $dateStrArr = explode(",",$row[0]);
                     $dateStr = $dateStrArr[0].",".(intval($dateStrArr[1])-1);
                     if(intval($dateStrArr[1]) == 2 ) 
                       $dateStr .=",28";
                     else if(intval($dateStrArr[1]) %2 == 0 ) //Odd number of month
                     {
                       if(intval($dateStrArr[1]) <=7 )
                         $dateStr .=",30";
                       else
                         $dateStr .=",31";
                     }
                     else
                     {
                       if(intval($dateStrArr[1]) <= 7 )
                         $dateStr .=",31";
                       else
                         $dateStr .=",30";
                     }

                 ?>
                 dom_data.setCell( <?=$index?>,0, new Date( <?= $dateStr ?> ) );
                 dom_data.setCell( <?=$index?>,1, <?= $row[2] ?> );
                 dom_data.setCell( <?=$index?>,2, <?= $row[1] ?> );
                 <?php
                     $idx++;
                 } 
                 ?>
		var dom_options = {
			title: 'EarthChem Portal Usage',
                        colors:['#728FCE','#C34A2C'],
                        selectionMode: 'multiple',
			isStacked: false,
                        areaOpacity:'0.4',
			hAxis: {title: 'Month',
                                gridlines:{count: '8'}
                               },
                        vAxes: { 
                                 0: {title: 'IP addresses',
                                    minValue:0},
                                 1: {title: 'Downloads',
                                    minValue: 0}
                               },
                        series: { 0:{targetAxisIndex:0},
                                  1:{targetAxisIndex:1}
                                },

			focusTarget: 'category',
			aggregationTarget: 'category',
                        legend: 'top'
		};
		var dom_chart= new google.visualization.AreaChart(document.getElementById('chart_div'));
		dom_chart.draw(dom_data,dom_options);
    }
    </script>
    </head>
    <body>
    <div>
         <div id="chart_div" style="width:100%;height:200px;"></div>
    </div>
    </body>
</html>
