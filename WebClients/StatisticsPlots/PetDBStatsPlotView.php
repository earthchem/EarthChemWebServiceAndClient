<?php
/* This script will draw PetDB Download statistics graph on fly
* @Author: Lulin Song created on July 18, 2016
*
* $Id:$
* $LastChangedDate:$
* $LastChangedBy:$
* $LastChangedRevision:$
*/
require_once 'PetDBStatsPlot.php';
date_default_timezone_set('America/New_York');
$startTime = "2017-06-01";
$firstDayThisMonth = date('Y-n-j', strtotime("first day of this month"));

$plotview = new PetDBStatsPlot("http://isotope.ldeo.columbia.edu:7001/petdbWeb/search/download_stat.jsp",  
                              array("start"=>"$startTime","end"=>"$firstDayThisMonth") 
                            );

$plotData = json_decode($plotview->getPlotArray());

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
                 dom_data.setCell( <?=$idx?>,0, new Date( <?= $dateStr ?> ) );
                 dom_data.setCell( <?=$idx?>,1, <?= $row[1] ?> );
                 dom_data.setCell( <?=$idx?>,2, <?= $row[2] ?> );
                 <?php
                     $idx++;
                 }
                 ?>
                var dom_options = {
                        title: 'PetDB Usage',
                        colors:['#728FCE','#C34A2C'],
                        selectionMode: 'multiple',
                        isStacked: false,
                        areaOpacity:'0.4',
                        hAxis: {title: 'Year',
                                gridlines:{count: '6'}
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
                var dom_chart= new google.visualization.AreaChart(document.getElementById('petdb_chart_div'));
                dom_chart.draw(dom_data,dom_options);
      }
    </script>
    </head>
    <body>
    <div>
         <div id="petdb_chart_div" style="width: 100%; height: 200px;" ></div>
    </div>
    </body>
</html>
