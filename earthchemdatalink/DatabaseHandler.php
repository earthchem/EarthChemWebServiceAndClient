<?php
/* DatabaseHandler class is parent class of PostgreSQLDBHandler, OracleDBHandler database.
 * @Author: Lulin Song created on April 17, 2013
 * $Id: DatabaseHandler.php 854 2013-04-17 21:12:03Z song $
 * $LastChangedDate: 2013-04-17 17:12:03 -0400 (Wed, 17 Apr 2013) $
 * $LastChangedBy: song $
 * $LastChangedRevision: 854 $
 * $HeadURL: http://svn.geoinfogeochem.org/svn/prod/earthchem/trunk/WebServices/DatabaseHandler.php $
*/
class DatabaseHandler {

	protected $dbhostname=null;
	protected $dbportnumber=null;
	protected $dbname=null;
	protected $dbloginname=null;
	protected $dbpassword=null;
	protected $conn=null;
	
	function __construct( $hostname, $portnumber, $dbname, $loginname,$password)
	{
		$this->dbhostname = $hostname;
		$this->dbportnumber = $portnumber;
		$this->dbname = $dbname;
		$this->dbloginname = $loginname;
		$this->dbpassword = $password;
	}
	
	
}
?>