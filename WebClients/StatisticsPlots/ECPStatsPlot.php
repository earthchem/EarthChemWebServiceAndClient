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
		//$plotArray[$idx++]=array('Year', 'Unique data downloads', 'Unique IP addresses');
		foreach( $xmldata->row as $row )
		{
			//if( $idx == 13 ) break;
			$dateSubStrs= explode("-", $row->start_date);
			$dateStr = $dateSubStrs[0].','.$dateSubStrs[1];
			$plotArray[$idx]= array("$dateStr",intval("$row->unique_downloads"),intval("$row->unique_ips"));
			$idx=$idx+1;
		}
//var_dump( $plotArray );
		return json_encode($plotArray);
	}
}

?>
