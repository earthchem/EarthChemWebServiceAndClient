<?php
/* PostgreSQLDBHandler class handle connection to PostgreSQL database and get data from database
 * @Author: Lulin Song created on April 17, 2013
 * $Id: PostgreSQLDBHandler.php 1297 2016-03-22 20:05:07Z song $
 * $LastChangedDate: 2016-03-22 16:05:07 -0400 (Tue, 22 Mar 2016) $
 * $LastChangedBy: song $
 * $LastChangedRevision: 1297 $
 * $HeadURL: http://svn.geoinfogeochem.org/svn/prod/earthchem/trunk/WebServices/PostgreSQLDBHandler.php $
*/
class PostgreSQLDBHandler extends DatabaseHandler{

	//connect to database
	public function connect()
	{
		/* connect to postgres database */
		$connection_string = "host=".$this->dbhostname." port=".$this->dbportnumber." dbname=".$this->dbname." user=".$this->dbloginname." password=".$this->dbpassword." connect_timeout=6";
		$this->conn = pg_connect($connection_string) or die("Cannot connect to database ".$this->dbname);
		
	}
	
	//clost connection to database
	public function closeConnect()
	{

		/* disconnect from the postgresql database */
		@pg_close($this->conn);
		
	}
	
	
	//Get results according passin query
	public function getQueryResults($query)
	{
        $result = pg_query($this->conn,$query) or die('Errant query:  '.$query);
		if(!$result)
		{
				$errorMsg=pg_last_error();
				//   echo $errorMsg;
				return 0;
		}
		return $result;
	}

	//Get sample metadata from database according pass-in submission ids
	//$returnData array will be filled with author name, data type name, release date etc. information
    public function getInformationFromGRL($ids, & $returnData, $uri,$collectioname)
    {
	  if(count($ids)==0) return 0;
	  /* connect to ECL database */
	  $this->connect();

	  /* Step 1. Get information from GRL database according submission_id */
	  $query = $this->getGRLQueryAccordingSubmissionId($ids);
	  $result = $this->getQueryResults($query);
	
	  /* Get submission_id, first_name,last_name,dataset_type_name from result and store them in arrays */
	  $idx=0;
	  $personName="";
	  $types="";
	  while ($row = pg_fetch_array($result)) {
	  	$typeTotal = $row["total_types"]; // Get total number of type name for one id
	  	
	  	if( $typeTotal == 1 )
	  	{
	  		$types .=$row["datatypename"]." ";
	  		$this->assembleGRLDataArray($idx,$row,$collectioname, $types, $uri, $returnData);		    
		    $types="";
	  	}
	  	else // need to aggregate type name into one string.
	  	{
	  		$typeTotal--;
	  		$types .=$row["datatypename"];
	  		while ( $typeTotal-- > 0 )
	  		{
	  			$row = pg_fetch_array($result);
	  			$types .= ", ".$row["datatypename"];	  			
	  		}
	  		$this->assembleGRLDataArray($idx,$row,$collectioname, $types, $uri, $returnData);
	  		$types="";
	  	}
	  	$idx = $idx+1;
	}

	$this->closeConnect();
//	print_r($returnData);
	return 1;
  }
  
  //Get sample metadata from database according pass-in group ids
  //$returnData array will be filled with information
  public function getInformationFromSESAR($ids, & $returnData, $uri,$collectioname)
  {
  	if(count($ids)==0) return 0;
  	/* connect to ECL database */
  	$this->connect();
  
  	/* Step 1. Get information from SESAR database according ids */
  	$query = $this->getSESARQueryFromIds($ids);
  	$result = $this->getQueryResults($query);
  
  	$idx=0;
  	$personName="";
  	while ($row = pg_fetch_array($result)) {
  		$returnData[$idx] = array(
  		                    "Data Collection"      => array("text"=>$collectioname),
  		                    "Data Type"            => array("text"=>"SampleInfo"), //hard-coded
  		                    "Repository Data Link" => array("text"=>"List of Samples",  //hard-coded
  		                                                    "url"=>$uri.$row["id"]),
  		                    "Title/Description"    => array("text"=>"Sample metadata associated with award (".$row["name"].")")
  		);
  		$idx = $idx+1;
  	}
  
  	$this->closeConnect();
  //	print_r($returnData);
  	return 1;
  }
  
  //Assemble returning data array from GRL database
  private function assembleGRLDataArray($idx,$row,$collectioname,$types, $uri, &$returnData)
  {
      $firstNameStr=substr(strtoupper($row["firstname"]),0,1);
      $midNameStr=substr(strtoupper($row["midname"]),0,1);
      $firstNameStr .=" ".$midNameStr;
      if($row["co_author"] !=null && !empty($row["co_author"]) && $row["co_author"] != "{NULL}")
      {
  	    $personName=$row["lastname"].', '.$firstNameStr.' et al.';
      }
      else
      {
  	    $personName=$row["lastname"].', '.$firstNameStr;
      }
      $releaseDate = $row["releasedate"];
      $releaseDataStrs=explode("-",$releaseDate);
      $returnData[$idx] = array(
  		                        "Data Collection"      => array("text"=>$collectioname),
  		                        "Data Type"            => array("text"=>$types),
  		                        "Repository Data Link" => array("text"=> $personName." (".$releaseDataStrs[0].")",
  		                                                        "url"=>$uri.$row["id"]),
  		                        "Title/Description"    => array("text"=>substr($row["title"],0,50).' ...')
  );
}
  //Assemble query according submission id
  private function getGRLQueryAccordingSubmissionId($ids)
  {
    $IdStr="(";
    foreach($ids as $id)
    {
      $IdStr= $IdStr.$id.',';
    }
  	$IdStr=rtrim($IdStr, ','); //remove last ','
    $IdStr .=")";
    $query = "select s.submission_id as id, p.first_name as firstname, p.middle_initial as midname, p.last_name as lastname, array_agg(al2.person_num) as co_author, s.title as title, s.release_date as releasedate,d.data_type_name as datatypename, idcount.total_types, dl.list_order  
              from submissions s
              INNER JOIN ( data_type_list dl INNER JOIN data_type d on dl.data_type_id = d.data_type_id ) ON s.submission_id = dl.submission_id
              INNER JOIN author_list al on al.submission_id = s.submission_id and al.author_order = 1
              INNER JOIN person p ON al.person_num = p.person_num
              LEFT JOIN author_list al2 on al2.submission_id = s.submission_id and al2.submission_id in ".$IdStr." and al2.author_order !=1
              INNER JOIN (
                         select ss.submission_id, count(ss.submission_id) as total_types from data_type_list ss where ss.submission_id in ".$IdStr." group by ss.submission_id order by ss.submission_id
                         ) as idcount 
                         on s.submission_id = idcount.submission_id
              where s.submission_id in ".$IdStr." and s.status_id=2 
              group by dl.list_order, s.submission_id, p.first_name, p.middle_initial, p.last_name, d.data_type_name, idcount.total_types 
              order by s.submission_id,dl.list_order";
    //print_r( $query );
  	return $query;
  }
  
  //Assemble query according submission id
  private function getSESARQueryFromIds($ids)
  {
  	$IdStr="(";
  	foreach($ids as $id)
  	{
  		$IdStr= $IdStr.$id.',';
  	}
  	$IdStr=rtrim($IdStr, ','); //remove last ','
  	$IdStr .=")";
  	$query = "select id,name from groups where id in ".$IdStr;
  	return $query;
  }
}
?>
