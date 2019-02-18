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
        $tt = explode(",",$ti[0]);
        $lastyear = $tt[0];
        $lastmonth = $tt[1];
        //
        //Turn on the following once we have data in the database.
        //
	$xmldata=$this->getSimpleXMLElement();
	$idx=0;
        $plotArray2 = null;
	foreach( $xmldata->RECORD as $row )
        {	
		$year= $row->YEAR;
                if( intval($year) < intval($lastyear)) continue;
		$month = $row->MONTH;
                if( (intval($year) == intval($lastyear)) && intval($month) <= intval($lastmonth)) continue;
		$dateStr = $year.",".$month;
		$plotArray2[$idx]= array("$dateStr",intval("$row->UNIQUE_IP"), intval("$row->MONTHLY_DOWNLOAD"));
		$idx=$idx+1;
	}


        //Get ECDB statistics 
        //$ecdburl = "https://ecapi.earthchem.org/statistics/downloadstatistics?start=2018-11-01&end=2019-03-01";
        $ecdburl = "https://ecapi.earthchem.org/statistics/downloadstatistics";
        $firstParam=true;
        foreach ($this->params as $key => $value )
        {
               if( $firstParam )
               {
                 $ecdburl .= '?'.$key."=".$value;
                 $firstParam=false;
               }
               else
               {
                 $ecdburl .= "&".$key."=".$value;
               }
        }

        $ecdbxml=file_get_contents($ecdburl);
$stringxml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<STATISTICS type="DOWNLOAD">
  <RECORD>
    <YEAR>2018</YEAR>
    <MONTH>12</MONTH>
    <UNIQUE_IP>3</UNIQUE_IP>
    <UNIQUE_EMAIL>2</UNIQUE_EMAIL>
    <MONTHLY_DOWNLOAD>4</MONTHLY_DOWNLOAD>
  </RECORD>
  <RECORD>
    <YEAR>2019</YEAR>
    <MONTH>1</MONTH>
    <UNIQUE_IP>106</UNIQUE_IP>
    <UNIQUE_EMAIL>70</UNIQUE_EMAIL>
    <MONTHLY_DOWNLOAD>332</MONTHLY_DOWNLOAD>
  </RECORD>
</STATISTICS>
';
        //$ecdbdata = new SimpleXMLElement($xml);
        $ecdbdata = new SimpleXMLElement($stringxml);

        //Merge PetDB and ECDB statistics data
        foreach( $ecdbdata->RECORD as $row )
        {
            $year  = $row->YEAR;
            $month = $row->MONTH;
            $dateStr = $year.",".$month;
            foreach ( $plotArray2 as $index=>$values)
            {
                  if( $dateStr == $values[0]) //same year and same month
                  { //add ecdb stats to plotArray2
                    $totalIPs = intval($row->UNIQUE_IP) + intval($values[1]);
                    $totalDownloads = intval($row->MONTHLY_DOWNLOAD) + intval($values[2]);
                    $plotArray2[$index]= array($dateStr,$totalIPs, $totalDownloads);
                  }
            }
        }

        //Merge all statistics
	$arr = array_merge($plotArray,$plotArray2);

	return json_encode($arr);
    }

    static public function getPieChartData()
    {
        $plotArray1 = PetDBStatsPlot::getPieChartDataFromFile();
        
        date_default_timezone_set('America/New_York');
        $currentTime  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y") -0);
        $today = date('Y-m-d', $currentTime);

        $url = "http://www.earthchem.org/petdbWeb/search/download_purpose_stat.jsp?start=2017-11-01&end=".$today;
        $xml=file_get_contents($url);
        $data = new SimpleXMLElement($xml);

        $plotArray2 = array();
	foreach( $data->RECORD as $row )
        {	
          $purpose = $row->PURPOSE;
          $cnt  = $row->PURPOSE_CNT;
          $plotArray2["$purpose"] = "$cnt";
        }
        $plotArray = array();
        foreach ($plotArray1 as $key => $val)
        {
            if(isset($plotArray2["$key"]))
                $plotArray["$key"] = intval($plotArray1["$key"]) + intval($plotArray2["$key"]);
            else 
                $plotArray["$key"] = intval($plotArray1["$key"]);
        }

        return json_encode($plotArray);
    }

    static public function getPieChartDataFromFile()
    {
        $myFile = fopen("petdb_download_feedback.csv","r");
        $data=null;
        $idx=0;
        $myline = fgets($myFile); //skip first line which is column header
        $IPavoid= array('129.236.40.238','129.236.6.17'  ,'128.118.52.28','129.236.40.190','68.228.39.70',
                  '129.236.40.215','129.236.40.174','129.236.40.157','129.236.40.200',
                  '129.236.6.198' ,'129.236.40.156'
                 );
        $emailavoid= array("e109084@metu.edu.tr","song@ldeo.columbia.edu");

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
            $email = null;
            if( isset($linedata[3]) && strlen($linedata[3]) !=0 )
              $email = trim($linedata[3]);
            if( isset( $email ) && !empty( $email) )
            {
              if(in_array($email,$emailavoid) ) 
              {
                  continue; //Skip some hacking email.
              }
            }
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
        return $data;
    }


    static public function getMonthlyIPAndDownLoadCountsFromFile()
    {
        $myFile = fopen("petdb_download_feedback.csv","r");
        $data=null;
        $idx=0;
        $myline = fgets($myFile); //skip first line which is column header
        $IPavoid= array('129.236.40.238','129.236.6.17'  ,'128.118.52.28','129.236.40.190','68.228.39.70',
                  '129.236.40.215','129.236.40.174','129.236.40.157','129.236.40.200',
                  '129.236.6.198' ,'129.236.40.156'
                 );

        $emailavoid= array("e109084@metu.edu.tr","song@ldeo.columbia.edu");

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
            $email = null;
            if( isset($linedata[3]) && strlen($linedata[3]) !=0 )
              $email = trim($linedata[3]);
            if( isset( $email ) && !empty( $email) )
            {
              if(in_array($email,$emailavoid) ) 
              {
                  continue; //Skip some hacking email.
              }
             }

            if( in_array($IPAddress,$IPavoid) ) continue; //skip IEDA employee

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
        $IPavoid= array('129.236.40.238','129.236.6.17'  ,'128.118.52.28','129.236.40.190','68.228.39.70',
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
