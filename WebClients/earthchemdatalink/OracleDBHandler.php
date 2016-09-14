<?php
/* DatabaseHandler class handles interaction to Oracle database. 
 * @Author: Lulin Song created on April 17, 2013
 * $Id: OracleDBHandler.php 1297 2016-03-22 20:05:07Z song $
 * $LastChangedDate: 2016-03-22 16:05:07 -0400 (Tue, 22 Mar 2016) $
 * $LastChangedBy: song $
 * $LastChangedRevision: 1297 $
 * $HeadURL: http://svn.geoinfogeochem.org/svn/prod/earthchem/trunk/WebServices/OracleDBHandler.php $
*/
class OracleDBHandler extends DatabaseHandler{
	
	//connect to database
	public function connect()
	{
	  if( $this->conn == null ) 
	  {
	      /* connect to Oracle database */
	      $db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$this->dbhostname.")(PORT = ".$this->dbportnumber.")))(CONNECT_DATA=(SID=".$this->dbname.")))";
	    	  
	      $this->conn = oci_connect($this->dbloginname,$this->dbpassword,$db);
	  }
	 
	  if (!$this->conn) {
	    $m = oci_error();
	    echo $m['message'], "\n";
	    return 0;
	  }
	}
	
	//clost connection to database
	public function closeConnect()
	{
	    // Close the Oracle connection
		oci_close($this->conn);
	}
	
	
	//Get results according passin query
	public function getQueryResults($query)
	{
	  $stid = oci_parse($this->conn, $query);
	  $result = oci_execute($stid);
	  if(!$result)
	  {
		        $errorMsg=oci_error();
	            //	echo $errorMsg;
		        return 0;
	  }
	  return $stid;
	}
	
	public function getInformationFromSedDB($ids,&$returnData, $uri,$collectioname)
	{		
		if(count($ids)==0) return 0;
		/* connect to Oracle database */
		$this->connect();
		$query = $this->getSedDBQueryAccordingIds($ids);
		$results = $this->getQueryResults($query);		

		$idx=0;
	    $personName="";
		while ($row = oci_fetch_array($results, OCI_RETURN_NULLS+OCI_ASSOC)) {
			$authorlist = $row['AUTHOR'];
			$authors = explode(";", $authorlist);
			if(count($authors) > 1)
			{
				$personName=ucwords(strtolower($authors[0])).' et al.';
			}
			else
			{
				$personName=ucwords(strtolower($authors[0]));
			}
		    $returnData[$idx] = array(
		                         "Data Collection"      => array("text"=>$collectioname),
		                         "Data Type"            => array("text"=>"Geochemistry"),
		                         "Repository Data Link" => array("text"=> $personName." (".$row["PUBYEAR"].")",
		                                                         "url"=>$uri.$row["DATASOURCE_NUM"]),
		                         "Title/Description"    => array("text"=>ucfirst(strtolower(substr($row["TITLE"],0,50))).' ...')
		                        ); //end of array
		    $idx = $idx+1;
		}
		
		$this->closeConnect();
	//	print_r($returnData);
		return $idx;
	}
	
	public function getInformationFromPetDB($ids,&$returnData, $uri,$collectioname)
	{
		if(count($ids)==0) return 0;
		/* connect to Oracle database */
		$this->connect();
		$query = $this->getPetDBQueryAccordingIds($ids);
		$results = $this->getQueryResults($query);		

		$idx=0;
	    $personName="";
		while ($row = oci_fetch_array($results, OCI_RETURN_NULLS+OCI_ASSOC)) {
			if($row['AUTHORCOUNT']!= 1)
			{
				$personName=ucwords(strtolower($row['LAST_NAME'])).', '.$row['FIRST_NAME'].' et al.';
			}
			else
			{
				$personName=ucwords(strtolower($row['LAST_NAME'])).', '.$row['FIRST_NAME'];
			}
			$returnData[$idx] = array(
					                         "Data Collection"      => array("text"=>$collectioname),
					                         "Data Type"            => array("text"=>"Geochemistry"),
					                         "Repository Data Link" => array("text"=> $personName." (".$row["PUB_YEAR"].")",
					                                                         "url"=>$uri.$row["REF_NUM"]),
					                         "Title/Description"    => array("text"=>ucfirst(strtolower(substr($row["TITLE"],0,50))).' ...')
			); //end of array
			$idx = $idx+1;
		}
	
		$this->closeConnect();
	//	print_r($returnData);
		return $idx;

	}
	
	public function getInformationFromVentDB($ids,&$returnData, $uri, $collectioname)
	{
		if(count($ids)==0) return 0;
		/* connect to Oracle database */
		$this->connect();
		$query = $this->getVentDBQueryAccordingIds($ids);
		$results = $this->getQueryResults($query);
		
		$idx=0;
		$personName="";
		while ($row = oci_fetch_array($results, OCI_RETURN_NULLS+OCI_ASSOC)) {
			if($row['AUTHORCOUNT']!= 1)
			{
				$personName=ucwords(strtolower($row['LAST_NAME'])).', '.$row['FIRST_NAME'].' et al.';
			}
			else
			{
				$personName=ucwords(strtolower($row['LAST_NAME'])).', '.$row['FIRST_NAME'];
			}
			$titles[$row['REF_NUM']] = substr($row['TITLE'],0,50).' ...';
			$releaseDates[$row['REF_NUM']] = $row['PUB_YEAR'];
			$datasetTypes[$row['REF_NUM']] = 'Geochemistry';
			
			$returnData[$idx] = array(
					                         "Data Collection"      => array("text"=>$collectioname),
					                         "Data Type"            => array("text"=>"Geochemistry"),
					                         "Repository Data Link" => array("text"=> $personName." (".$row["PUB_YEAR"].")",
					                                                         "url"=>$uri.$row["REF_NUM"]),
					                         "Title/Description"    => array("text"=>ucfirst(strtolower(substr($row["TITLE"],0,50))).' ...')
			); //end of array
			$idx = $idx+1;
		}
	
	
		$this->closeConnect();
	//	print_r($returnData);
		return $idx;
	}
	
	private function getPetDBQueryAccordingIds($ids)
	{
	    $IdStr="(";
		foreach($ids as $id)
		{
			$IdStr= $IdStr.$id.',';
		}
		$IdStr=rtrim($IdStr, ','); //remove last ','
		$IdStr .=")";
		$query = "select r.ref_num, p.first_name,p.last_name,pcount.authorCount,r.title,r.pub_year
	              from person p, author_list al, reference r,
	                   ( select ref_num, count(person_num) authorCount from author_list where REF_NUM in ".$IdStr." group by ref_num ) pcount 
	              where p.person_num  	= al.person_num
	                    and pcount.ref_num = r.ref_num
	                    and al.ref_num  (+)		= r.ref_num
	                    and al.AUTHOR_ORDER = 1";
		return $query;
	}
	
	private function getVentDBQueryAccordingIds($ids)
	{
		$IdStr="(";
		foreach($ids as $id)
		{
			$IdStr= $IdStr.$id.',';
		}
		$IdStr=rtrim($IdStr, ','); //remove last ','
		$IdStr .=")";
		$query = "select r.ref_num ref_num, p.first_name,p.last_name,pcount.authorCount,r.title,r.pub_year
			              from person p, author_list al, reference r,
			                   ( select ref_num, count(person_num) authorCount from author_list where REF_NUM in ".$IdStr." group by ref_num ) pcount 
			              where p.person_num  	= al.person_num
			                    and pcount.ref_num = r.ref_num
			                    and al.ref_num  (+)		= r.ref_num
			                    and al.AUTHOR_ORDER = 1";
		return $query;
	}
	
	private function getSedDBQueryAccordingIds($ids)
	{
		$IdStr="(";
		foreach($ids as $id)
		{
			$IdStr= $IdStr.$id.',';
		}
		$IdStr=rtrim($IdStr, ','); //remove last ','
		$IdStr .=")";
		$query = "select datasource_num, author , title , pubyear from DATASOURCE where datasource_num in ".$IdStr;
		
		return $query;
	}
}
?>
