<?php
/**
 * File: ECLStatsPlot
 * 
 *ECLStatsPlot class will get ECL Search statistics information from web service or static file.
 *
 * @author: Lulin Song
 * @created Oct 10, 2017
 *
*/

require_once 'WebClient.php';

class ECLStatsPlot extends WebClient
{	
    public function getDataFromFile()
    {
        $myFile = fopen("ECLStatistics.csv","r");
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
        /*{
	$xmldata=$this->getSimpleXMLElement();
	$idx=0;
        $plotArray2 = null;
	foreach( $xmldata->RECORD as $row )
        {	
		$year= $row->YEAR;
                if( intval($year) < intval($lastyear)) continue;
		$month = $row->MONTH;
                if( intval($month) <= intval($lastmonth)) continue;
		$dateStr = $year.",".$month;
		$plotArray2[$idx]= array("$dateStr",intval("$row->UNIQUE_IP"), intval("$row->MONTHLY_DOWNLOAD"));
		$idx=$idx+1;
	}

	$arr = array_merge($plotArray,$plotArray2);
        }*/
	$arr = $plotArray;
	return json_encode($arr);
    }

    static public function getPieChartDataFromFile()
    {
        $myFile = fopen("earthchem_library_downloads_20171010.csv","r");
        $data=null;
        $idx=0;
        $myline = fgets($myFile); //skip first line which is column header
        $IPavoid= array('129.236.40.238','129.236.6.17'  ,'128.118.52.28','129.236.40.190',
                  '129.236.40.215','129.236.40.174','129.236.40.157','129.236.40.200',
                  '129.236.6.198' ,'129.236.40.156'
                 );
        $emailavoid= array("e109084@metu.edu.tr","song@ldeo.columbia.edu","lhsu@ldeo.columbia.edu","annika@ldeo.columbia.edu","nshane@ldeo.columbia.edu","mcarter@ldeo.columbia.edu","bhchen@ldeo.columbia.edu");

        $SubmissionCnt=0;
        $EducationCnt=0;
        $ResearchCnt=0;
        $CommercialCnt=0;
        $OtherCnt=0;
        $EmailCnt=0;
        $NullCnt=0;
        $ipArr = array();
        while(!feof($myFile))
        {
            $myline = fgets($myFile);
            if(strlen($myline) <=0 ) break;
            $linedata = explode(",",$myline);
            $IPAddress = $linedata[2];
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

            $purpose = $linedata[4];
            if(preg_match("/Education/",$purpose)) $EducationCnt++;
            else if(preg_match("/Research/",$purpose)) $ResearchCnt++;
            else if(preg_match("/Commercial Use/",$purpose)) $CommercialCnt++;
            else if(preg_match("/Other/",$purpose)) $OtherCnt++;
            else $NullCnt++;
            if( isset($linedata[3]) && strlen(trim($linedata[3])) != 0) $EmailCnt++;
        }
        fclose($myFile);

        $totalCnt = intval($EducationCnt) +intval($ResearchCnt)+intval($OtherCnt)+intval($NullCnt);
        $EmailRatio = intval($EmailCnt)/intval($totalCnt);
        $data = array( 'education'=>$EducationCnt,
                       'research'=>$ResearchCnt,
                       'commercial'=>$CommercialCnt,
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
        $myFile = fopen("earthchem_library_downloads_20171010.csv","r");
        $data=null;
        $idx=0;
        $myline = fgets($myFile); //skip first line which is column header
        $IPavoid= array('129.236.40.238','129.236.6.17'  ,'128.118.52.28','129.236.40.190',
                  '129.236.40.215','129.236.40.174','129.236.40.157','129.236.40.200',
                  '129.236.6.198' ,'129.236.40.156'
                 );

        $emailavoid= array("e109084@metu.edu.tr","song@ldeo.columbia.edu","lhsu@ldeo.columbia.edu","annika@ldeo.columbia.edu","nshane@ldeo.columbia.edu","mcarter@ldeo.columbia.edu","bhchen@ldeo.columbia.edu");

        $IPCnt=0;
        $DownloadCnt=0;
        $ipArr = array();
        $statistics = array();
        while(!feof($myFile))
        {
            $myline = fgets($myFile);
            if(strlen($myline) <=0 ) break;
            $linedata = explode(",",$myline);
            $IPAddress = $linedata[2];
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

            $daystr = substr($linedata[1],0,10);
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
        $myFile = fopen("earthchem_library_downloads_20171010.csv","r");
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
            $IPAddress = $linedata[2];

            if( in_array($IPAddress,$IPavoid) ) continue;

            $daystr = substr($linedata[1],0,10);
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

    static public function assembleSQLFromFile()
    {
        $myFile = fopen("earthchem_library_downloads_20171010.csv","r");
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
        $emailavoid= array("e109084@metu.edu.tr","song@ldeo.columbia.edu","lhsu@ldeo.columbia.edu","annika@ldeo.columbia.edu","nshane@ldeo.columbia.edu","mcarter@ldeo.columbia.edu","bhchen@ldeo.columbia.edu");
        while(!feof($myFile))
        {
          $query = "INSERT INTO download_stats2 (submission_id,download_date,download_ip,email,use_field) VALUES (";
            $myline = fgets($myFile);
            if(strlen($myline) <=0 ) break;
            $linedata = explode(",",$myline);
            $IPAddress = $linedata[2];
            $email = null;
            if( isset($linedata[3]) && strlen($linedata[3]) !=0 )
              $email = trim($linedata[3]);
            if( isset( $email ) && !empty( $email) )
            {
              if(in_array($email,$emailavoid) )
              {
                  continue; //Skip some hacking or internal email.
              }
            }

            if( in_array($IPAddress,$IPavoid) ) continue;
            $sid = $linedata[0];
            $datet = $linedata[1];
            $use = $linedata[4];
            if( !isset($sid) ) $query .="null,";
            else if( strlen($sid)==0 ) $query .="null,";
            else    $query .=$sid.",";
            $query .="to_date('".$datet."','mm/dd/yyyy-h:m'),";
            $query .="'".$IPAddress."',";
            if(!isset($email)) $query .="'',";
            else $query .="'".$email."',";
            if(!isset($use)) $query .="''";
            else $query .="'".trim($use)."'";
            $query .=");";
            echo $query."\n";
        }
        fclose($myFile);
    }
}

?>
