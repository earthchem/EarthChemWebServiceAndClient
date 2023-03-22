<?php
/* ECPStatsPlot class will get EarthChem Portal statistics information from web service. Then create a plot using Google Graph Toolkit.
*
* @Author: Lulin Song created on April 18, 2013
*
* $Id: ECPStatsPlot.php 1313 2016-08-23 19:30:28Z song $
* $LastChangedDate: 2016-08-23 15:30:28 -0400 (Tue, 23 Aug 2016) $
* $LastChangedBy: song $
* $LastChangedRevision: 1313 $
*/

require_once 'WebClient.php';

class ECPStatsPlot extends WebClient
{	
	public function getPlotArray()
	{
		$xmldata=$this->getSimpleXMLElement();
		$idx=0;
                $firstDayThisMonth = strtotime("first day of this month");
                date_default_timezone_set('UTC');
                $timeoftoday = time();
		foreach( $xmldata->row as $row )
		{
		    $dateSubStrs= explode("-", $row->start_date);
		    $enddateSubStrs= explode("-", $row->end_date);
                    $enddateYear = $enddateSubStrs[0];
                    $enddateMon = $enddateSubStrs[1];
                    $enddateDay = $enddateSubStrs[2];
                    $enddatetime = mktime(0,0,0,$enddateMon, $enddateDay,$enddateYear);
                    if( $enddatetime > $timeoftoday ) continue; //we skip imcomplete month
		    $dateStr = $dateSubStrs[0].','.$dateSubStrs[1];
		    $plotArray[$idx]= array("$dateStr",intval("$row->unique_downloads"),intval("$row->unique_ips"));
		    $idx=$idx+1;
		}
		return json_encode($plotArray);
	}

	public function getHoldingsData()
	{
		$xmldata=$this->getSimpleXMLElement();
		$data = array();
		$idx = 0;
		foreach( $xmldata->row as $row )
		{
			$rowName = (string) $row->name;
			unset($row->name);
			$data[$rowName] = $row;

		}
		return json_encode($data);
	}
}

?>
