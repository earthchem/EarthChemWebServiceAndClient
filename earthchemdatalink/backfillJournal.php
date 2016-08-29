<?php
/* 
 * @Author: Lulin Song (Created on Oct. 1, 2013)
 * $Id: backfillJournal.php 1120 2013-10-02 15:01:29Z song $
 * $LastChangedDate: 2013-10-02 11:01:29 -0400 (Wed, 02 Oct 2013) $
 * $LastChangedBy: song $
 * $LastChangedRevision: 1120 $
 * $HeadURL:$
*/

require_once 'DatabaseHandler.php';
require_once 'OracleDBHandler.php';
include("inc/DBCreds.php");

//-------------------------- Get Author and dataset information from SedDB database
$sedHandle = new OracleDBHandler(SERVERNAME1, PORT3,DBNAME4, DBLOGIN4, DBPASSWORD3);
  $sedCount = $sedHandle->backfillJournalToSedDB('SedDB_References_LoadedTab.txt');
?>

