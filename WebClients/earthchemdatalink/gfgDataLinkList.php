<?php
/* It is a REST web service. It will search awardtracker database to get data collection and its detail information link back. It will get medata from four database related to the value number retrieved from awardtracker database. Final data will be returned as JSON or HMTL.
 * Usage: 
 *       http://app.iedadata.org/ws/gfgDataLinkList.php?num=8888877&format=json
 *       http://app.iedadata.org/ws/gfgDataLinkList.php?num=8888877&format=html
 *       
 * num is award tracker number and format is optional. Default format is HTML.
 * 
 * @Author: Lulin Song (Created on 2011)
 * $Id: gfgDataLinkList.php 1297 2016-03-22 20:05:07Z song $
 * $LastChangedDate: 2016-03-22 16:05:07 -0400 (Tue, 22 Mar 2016) $
 * $LastChangedBy: song $
 * $LastChangedRevision: 1297 $
 * $HeadURL: http://svn.geoinfogeochem.org/svn/prod/earthchem/trunk/WebServices/gfgDataLinkList.php $
*/

require_once 'DatabaseHandler.php';
require_once 'PostgreSQLDBHandler.php';
require_once 'OracleDBHandler.php';
require_once 'FormatConverter.php';
include("inc/DBCreds.php");

$award_num = isset($_GET['num']) ? $_GET['num'] : -1; //-1 is the default
if($award_num==-1) 
{  echo 'Please specify award number (eg. num=0819368).</br>
         http://app.iedadata.org/ws/gfgDataLinkList.php?num=0819368';
   exit;
}

$request_format= isset($_GET['format']) ? strtolower($_GET['format']) : 'html';

if($request_format !='json' && $request_format !='html') {
echo 'Format '.$request_format.' not supported. Format should be either json or html.';
exit;
}

/* connect to awardtracker db */
$awardHandle = new PostgreSQLDBHandler(SERVERNAME2, PORT1,DBNAME1, DBLOGIN1, DBPASSWORD1);
$awardHandle->connect();

/* Step 1. Get data_collection_id and data_value from tracker table for specific nsf_award_num */
/* grab the reference number for specific award num from the database. It may results multiple entries for different type of data. */
$query = "SELECT t.data_collection_id as cid, t.data_value as cnum, dc.collection_name as cname,dc.collection_url as curl from tracker t , data_collection dc
          where nsf_award_num='".$award_num."' and submit_status=5 and t.data_collection_id = dc.collection_id order by data_collection_id";
$result = $awardHandle->getQueryResults($query);

/* Parse the results and saved them to arrays */
$uriArray = array(); /*{"EarthChem" => array( "EarthChem Library"=> "http://www.earthchem.org/library/browse/view?id=")} */
$refData = array(
                   "EarthChem" => array(),
                   "PetDB"     => array(),
                   "SedDB"     => array(),
                   "VentDB"    => array(),
                   "SESAR"     => array()              
);

$petdbIdx=0;
$grlIdx=0;
$seddbIdx=0;
$ventdbIdx=0;
$sesardbIdx=0;
$collectionIdRefNums=0;
while ($row = pg_fetch_array($result)) {
	$collectionIdRefNums++; //count total entries in award tracker database
	$collectionId = $row["cid"]; //collection id
	
	//Store collection name, and collection url
	if($collectionId == 1) //earthchem
	{
	  if(!isset($uriArray["EarthChem"])) 
	  {
		$uriArray["EarthChem"] = array( "cname" => $row["cname"],"curl"  => $row["curl"]);
	  }
	  $refData["EarthChem"][$grlIdx++]=$row["cnum"];
	}
	if($collectionId == 2) //earthchem
	{
		if(!isset($uriArray["PetDB"]))
		{
			$uriArray["PetDB"] = array( "cname" => $row["cname"],"curl"  => $row["curl"]);
		}
		$refData["PetDB"][$petdbIdx++]=$row["cnum"];
	}
	if($collectionId == 3) //earthchem
	{
		if(!isset($uriArray["SedDB"]))
		{
			$uriArray["SedDB"] = array( "cname" => $row["cname"],"curl"  => $row["curl"]);
		}
		$refData["SedDB"][$seddbIdx++]=$row["cnum"];
	}
	if($collectionId == 5) //earthchem
	{
		if(!isset($uriArray["VentDB"]))
		{
			$uriArray["VentDB"] = array( "cname" => $row["cname"],"curl"  => $row["curl"]);
		}
		$refData["VentDB"][$ventdbIdx++]=$row["cnum"];
	}
	if($collectionId == 6) //earthchem
	{
		if(!isset($uriArray["SESAR"]))
		{
			$uriArray["SESAR"] = array( "cname" => $row["cname"],"curl"  => $row["curl"]);
		}
		$refData["SESAR"][$sesardbIdx++]=$row["cnum"];
	}
}

/* If nothing found, return nothing */
if(count($collectionIdRefNums) == 0) exit;


/* disconnect from the db */
$awardHandle->closeConnect();

$rntData = array(
                   "EarthChem" => array(),
                   "PetDB"     => array(),
                   "SedDB"     => array(),
                   "VentDB"    => array(),
                   "SESAR"     => array()              
);

//---------------------------- Get Author and dataset information from EarthChem Library
$ecdbHandle = new PostgreSQLDBHandler(SERVERNAME2, PORT5,DBNAME2, DBLOGIN2, DBPASSWORD2);
if ( $grlIdx > 0 )
  $ecCount = $ecdbHandle->getInformationFromGRL($refData["EarthChem"], $rntData["EarthChem"],$uriArray["EarthChem"]['curl'],$uriArray["EarthChem"]['cname']);
else
  $ecCount =0;
//-------------------------- Get Author and dataset information from PetDB database
//$petHandle = new OracleDBHandler(SERVERNAME1, PORT3,DBNAME3, DBLOGIN3, DBPASSWORD3);
//if($petdbIdx > 0 )
//  $petCount = $petHandle->getInformationFromPetDB($refData["PetDB"], $rntData["PetDB"],$uriArray["PetDB"]['curl'],$uriArray["PetDB"]['cname']);
//else
  $petCount=0;

//-------------------------- Get Author and dataset information from SedDB database
//$sedHandle = new OracleDBHandler(SERVERNAME1, PORT3,DBNAME4, DBLOGIN4, DBPASSWORD3);
//if($seddbIdx > 0 )
 // $sedCount = $sedHandle->getInformationFromSedDB($refData["SedDB"], $rntData["SedDB"],$uriArray["SedDB"]['curl'],$uriArray["SedDB"]['cname']);
//else 
  $sedCount=0;

//-------------------------- Get Author and dataset information from VentDB database
//$ventHandle = new OracleDBHandler(SERVERNAME1, PORT3,DBNAME5, DBLOGIN5, DBPASSWORD3);
//if($ventdbIdx > 0 )
 // $ventCount = $ventHandle->getInformationFromVentDB($refData["VentDB"], $rntData["VentDB"],$uriArray["VentDB"]['curl'],$uriArray["VentDB"]['cname']);
//else 
 // $ventCount=0;

//-------------------------- Get dataset information from SESAR database
$sesarHandle = new PostgreSQLDBHandler(SERVERNAME2, PORT4,DBNAME6, DBLOGIN6, DBPASSWORD4);
if($sesardbIdx > 0 )
  $sesarCount = $sesarHandle->getInformationFromSESAR($refData["SESAR"], $rntData["SESAR"],$uriArray["SESAR"]['curl'],$uriArray["SESAR"]['cname']);
else
  $sesarCount=0;

/* output in necessary format */
if($request_format == 'json') {

	header('Content-type: application/json');
    if( $ecCount !=0 || $ventCount !=0 || $sedCount !=0 ||  $petCount !=0 || $sesarCount !=0 )
    {
      echo '[';
    }
    if($ecCount !=0)
    {
      FormatConverter::printJSON($rntData["EarthChem"]);
    }
    if($petCount !=0)
    {
      if($ecCount !=0) echo ",";
      FormatConverter::printJSON($rntData["PetDB"]);
    }
    if($sedCount !=0)
    {
      if( $petCount !=0 || $ecCount !=0 ) echo ",";
      FormatConverter::printJSON($rntData["SedDB"]);
    }
    if($ventCount !=0 )
    {
      if($sedCount !=0 ||  $petCount !=0 || $ecCount !=0) echo ",";
      FormatConverter::printJSON($rntData["VentDB"]);
    }
    if($sesarCount !=0 )
    {
    	if($ventCount !=0 || $sedCount !=0 ||  $petCount !=0 || $ecCount !=0) echo ",";
    	FormatConverter::printJSON($rntData["SESAR"]);
    }
    if( $ecCount !=0 || $ventCount !=0 || $sedCount !=0 ||  $petCount !=0 || $sesarCount !=0 )
    {
      echo ']';
    }
	exit;
}
else if($request_format == 'html') {
	echo '<legend><strong>Sample-Based Data Linked to Award</strong></legend>';
	echo '<table id="remote_file" class="sortable" style="vertical-align: top" preserve_style="row" cellspacing="2">';
	echo '<tbody>';
	echo '<tr><th>Data Collection</th><th>Data Type</th><th>Repository Data Link</th><th>Title/Description</th>';

	FormatConverter::printHTML($rntData["EarthChem"]);
    FormatConverter::printHTML($rntData["PetDB"]);
    FormatConverter::printHTML($rntData["SedDB"]);
    FormatConverter::printHTML($rntData["VentDB"]);
    FormatConverter::printHTML($rntData["SESAR"]);
    
	echo '</tr>';
	echo '</tbody></table>';
	exit;
}


