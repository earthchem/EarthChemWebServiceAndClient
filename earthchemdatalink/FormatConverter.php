<?php

/* FormatConverter class handles format convertion from array to HMTL or JSON. 
 * @Author: Lulin Song created on April 17, 2013
 * $Id: FormatConverter.php 1297 2016-03-22 20:05:07Z song $
 * $LastChangedDate: 2016-03-22 16:05:07 -0400 (Tue, 22 Mar 2016) $
 * $LastChangedBy: song $
 * $LastChangedRevision: 1297 $
 * $HeadURL: http://svn.geoinfogeochem.org/svn/prod/earthchem/trunk/WebServices/FormatConverter.php $
*/
 class FormatConverter 
 {
   //It will take two dimentional array and convert to JSON string
   public static function printJSON( $dataArr)
   {
   	  $cnt = count($dataArr);
   	  if( $cnt == 0 ) return "";
   	  $jrowNum=0;
   	  foreach ( $dataArr as $key => $value )
   	  {
   	      echo json_encode($value);
   	      if( $jrowNum != $cnt -1 ) //last row, don't need comma
   	      {
   	          	echo ",";
   	      }
   	      $jrowNum++;
   	  }
   } 

   //It will take two dimentional array and convert to JSON string
   public static function printHTML( $dataArr)
   {
   	if( count($dataArr) == 0 ) return "";
   	foreach ( $dataArr as $key => $value )
   	{
   	//	echo "key=".$key." value=".$value."\n";
   	   echo '<tr bgcolor="#FFFFFF"><td>'.$value["Data Collection"]["text"].'</td>';
       echo '<td>'.$value["Data Type"]["text"].'</a></td>';
	   echo '<td><a href="'.$value["Repository Data Link"]["url"].'" target="_blank">'.$value["Repository Data Link"]["text"].'</a></td>';
	   echo '<td>'.$value["Title/Description"]["text"].'</a></td>';
   	}
   }
   
 }
?>
