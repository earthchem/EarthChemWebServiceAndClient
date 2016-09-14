<?php
/* PetDBStatsPlot class will get PetDB Search statistics information from web service. Then create a plot using Google Graph Toolkit.
*
* @Author: Lulin Song created on July 18, 2016
*
* $Id: $
* $LastChangedDate: $
* $LastChangedBy: $
* $LastChangedRevision: $
*/

require_once 'WebClient.php';

class PetDBStatsPlot extends WebClient
{	
    public function getDataFromFile()
    {
        $myFile = fopen("PetDBStatistics.csv","r");
        $data=null;
        $idx=0;
        $myline = fgets($myFile); //skip first line which is column header
        while(!feof($myFile))
        {
            $myline = fgets($myFile);
            if(strlen($myline) <=0 ) break;
            $linedata = explode(",",$myline);
            $tarray = explode("-",$linedata[0]);

            $data[$idx] = array("$tarray[1],$tarray[0]",intval( $linedata[1] ),intval( $linedata[2]) );

            $idx++;
        }
        fclose($myFile);
        return $data;
    }

    public function getPlotArray()
    {
        $plotArray = $this->getDataFromFile();
        $s = sizeof($plotArray); 
        $ti = $plotArray[(intval($s)-1) ];

        //
        //Turn on the following once we have data in the database.
        //
	//$xmldata=$this->getSimpleXMLElement();
	//$idx=$s;
	//foreach( $xmldata->RECORD as $row )
	//{
		//if( $idx == 13 ) break;
	//	$year= $row->YEAR;
	//	$month = $row->MONTH;
	//	$dateStr = $year.",".$month;
	//	$plotArray[$idx]= array("$dateStr",intval("$row->MONTHLY_DOWNLOAD"),intval("$row->UNIQUE_IP"));
	//	$idx=$idx+1;
	//}
	return json_encode($plotArray);
    }
}

?>
