<?php
/* PetDBStatsPlot class will get PetDB Search statistics information from web service or static files.
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

    static public function getPieChartDataFromFile()
    {
        $myFile = fopen("petdb_download_feedback.csv","r");
        $data=null;
        $idx=0;
        $myline = fgets($myFile); //skip first line which is column header
        $IPavoid= array('129.236.40.238','129.236.6.17'  ,'128.118.52.28','129.236.40.190',
                  '129.236.40.215','129.236.40.174','129.236.40.157','129.236.40.200',
                  '129.236.6.198' ,'129.236.40.156'
                 );
        $EducationCnt=0;
        $ResearchCnt=0;
        $OtherCnt=0;
        $EmailCnt=0;
        $NullCnt=0;
        $ipArr = array();
        while(!feof($myFile))
        {
            $myline = fgets($myFile);
            if(strlen($myline) <=0 ) break;
            $linedata = explode(",",$myline);
            $IPAddress = $linedata[1];
            if( in_array($IPAddress,$IPavoid) ) continue;
            if( !isset( $ipArr[$IPAddress] ) )
              $ipArr[$IPAddress] = 1; 
            else
              $ipArr[$IPAddress] +=1; 

            $purpose = $linedata[2];
            if(preg_match("/Education/",$purpose)) $EducationCnt++;
            else if(preg_match("/Research/",$purpose)) $ResearchCnt++;
            else if(preg_match("/Other/",$purpose)) $OtherCnt++;
            else $NullCnt++;
            if( isset($linedata[3]) && strlen(trim($linedata[3])) != 0) $EmailCnt++;
        }
        fclose($myFile);
        //echo "Education cnt:".$EducationCnt."\n";
        //echo "Research cnt:".$ResearchCnt."\n";
        //echo "Other cnt:".$OtherCnt."\n";
        //echo "Null cnt:".$NullCnt."\n";
        //echo "Email cnt:".$EmailCnt."\n";
        $totalCnt = intval($EducationCnt) +intval($ResearchCnt)+intval($OtherCnt)+intval($NullCnt);
        //$EmailRatio = intval($EmailCnt)/intval($totalCnt);
        $data = array( 'education'=>$EducationCnt,
                       'research'=>$ResearchCnt,
                       'other' => $OtherCnt,
                       'null' => $NullCnt,
                       'total' => $totalCnt,
                       //'emailRatio'=> $EmailRatio,
                       'uniqueip' => sizeof($ipArr)
                     );
        return json_encode($data);
    }


    static public function getMonthlyIPAndDownLoadCountsFromFile()
    {
        $myFile = fopen("petdb_download_feedback.csv","r");
        $data=null;
        $idx=0;
        $myline = fgets($myFile); //skip first line which is column header
        $IPavoid= array('129.236.40.238','129.236.6.17'  ,'128.118.52.28','129.236.40.190',
                  '129.236.40.215','129.236.40.174','129.236.40.157','129.236.40.200',
                  '129.236.6.198' ,'129.236.40.156'
                 );
        $IPCnt=0;
        $DownloadCnt=0;
        $ipArr = array();
        $statistics = array();
        while(!feof($myFile))
        {
            $myline = fgets($myFile);
            if(strlen($myline) <=0 ) break;
            $linedata = explode(",",$myline);
            $IPAddress = $linedata[1];

            if( in_array($IPAddress,$IPavoid) ) continue;

            $daystr = substr($linedata[0],0,10);
            $dayarr = explode("/",$daystr);
            $month = $dayarr[0].'-'.$dayarr[2];
            if(isset($statistics[$month]) )
            {
                if(!in_array($IPAddress, $ipArr) ) 
                {
                    $ipArr[] = $IPAddress;
                    $cnt = $statistics[$month]['IP'];
                    $statistics[$month]['IP'] = intval($cnt) +1;;
                }
                $dcnt = $statistics[$month]['downloadCnt'];
                $statistics[$month]['downloadCnt'] = intval($dcnt)+1;
            }
            else
            {
                $mymounthcnt = array('IP' => 1, 'downloadCnt'=>1);
                $ipArr = array();
                $ipArr[] = $IPAddress;
                $statistics[$month] = $mymounthcnt;
            }
        }
        fclose($myFile);
        foreach($statistics as $month => $cnts)
        {
            echo "$month";
            foreach($cnts as $key =>$value)
            {
                echo ",$value";
            }
            echo "\n";
        }
    }

    static public function getDaylyIPAndDownLoadCountsFromFile()
    {
        $myFile = fopen("petdb_download_feedback.csv","r");
        $data=null;
        $idx=0;
        $myline = fgets($myFile); //skip first line which is column header
        $IPavoid= array('129.236.40.238','129.236.6.17'  ,'128.118.52.28','129.236.40.190',
                  '129.236.40.215','129.236.40.174','129.236.40.157','129.236.40.200',
                  '129.236.6.198' ,'129.236.40.156'
                 );
        $IPCnt=0;
        $DownloadCnt=0;
        $ipArr = array();
        $statistics = array();
        while(!feof($myFile))
        {
            $myline = fgets($myFile);
            if(strlen($myline) <=0 ) break;
            $linedata = explode(",",$myline);
            $IPAddress = $linedata[1];

            if( in_array($IPAddress,$IPavoid) ) continue;

            $daystr = substr($linedata[0],0,10);
            if(isset($statistics[$daystr]) )
            {
                if(!in_array($IPAddress, $ipArr) ) 
                {
                    $ipArr[] = $IPAddress;
                    $cnt = $statistics[$daystr]['IP'];
                    $statistics[$daystr]['IP'] = intval($cnt) +1;;
                }
                $dcnt = $statistics[$daystr]['downloadCnt'];
                $statistics[$daystr]['downloadCnt'] = intval($dcnt)+1;
            }
            else
            {
                $mymounthcnt = array('IP' => 1, 'downloadCnt'=>1);
                $ipArr = array();
                $ipArr[] = $IPAddress;
                $statistics[$daystr] = $mymounthcnt;
            }
        }
        fclose($myFile);
        foreach($statistics as $daystr => $cnts)
        {
            echo "$daystr";
            foreach($cnts as $key =>$value)
            {
                echo ",$value";
            }
            echo "\n";
        }
    }
}

?>
