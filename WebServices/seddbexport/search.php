<?
session_start();

include_once "ez_sql_core.php";
include_once "ez_sql_postgresql.php";
$db = new ezSQL_postgresql('geochron','upbgeochron','geochron2','localhost');
include("includes/geochron-secondary-header.htm");
?>
<h1>Search Database</h1>
<?

if($_GET['pkey']=="" && $_POST['pkey']==""){
	$pkey=$db->get_var("select nextval('search_query_seq')");
	$db->query("insert into search_query (search_query_pkey) values ($pkey)");
}

if($_GET['pkey']!=""){
	$pkey=$_GET['pkey'];
}

if($_POST['pkey']!=""){
	$pkey=$_POST['pkey'];
}

$runquery="no";

//echo "pkey: $pkey<br><br>";


//<CFINCLUDE template="buildquery.cfm">
include("buildquery.php");

?>

  <table border=0 cellspacing=10 cellpadding=10><!--- leave enough cellpadding so that IE does not obliterate part of the CSS "button" --->

    <tr style="vertical-align:middle">
      <td style="padding:10px 10px 10px 0px;vertical-align:middle">
      <input type=button value="Set" onClick="window.location = 'setlocation.php?pkey=<?=$pkey?>';">
      <input type=button value="Clear" onClick="window.location = 'clearlocation.php?pkey=<?=$pkey?>';">
      </td>
      <td style="padding:10px;vertical-align:middle">Location</td>
      <td style="padding:10px;vertical-align:middle;border:1px;border-style:solid;line-height:130%">
	<?
	if($queryrow->coordinates != ""){
		?>
		<img src="http://matisse.kgs.ku.edu/custompoints/geochronsearchpagepoly.php?pkey=<?=$pkey?>">
		<?
		$runquery="yes";
	}elseif($queryrow->locnorth != "" && $queryrow->loceast != "" && $queryrow->locsouth != "" && $queryrow->locwest != ""){
		echo "North: ".$queryrow->locnorth."<br>";
		echo "East: ".$queryrow->loceast."<br>";
		echo "South: ".$queryrow->locsouth."<br>";
		echo "West: ".$queryrow->locwest."<br>";
		$runquery="yes";
	}else{
		echo "No constraints set";
	}
	?>
      </td>
    </tr>

    <tr style="vertical-align:middle">
      <td style="padding:10px 10px 10px 0px;vertical-align:middle">
      <input type=button value="Set" onClick="window.location = 'setmetadata.php?pkey=<?=$pkey?>';">
      <input type=button value="Clear" onClick="window.location = 'clearmetadata.php?pkey=<?=$pkey?>';">
      </td>
      <td style="padding:10px;vertical-align:middle">Metadata</td>
      <td style="padding:10px;vertical-align:middle;border:1px;border-style:solid;line-height:130%">
			<?
			if($queryrow->igsn != "" ||  $queryrow->parentigsn != "" ||  $queryrow->laboratoryname != "" ||  $queryrow->analystname != "" ||  $queryrow->aliquotreference != "" ||  $queryrow->aliquotinstmethod != "" ||  $queryrow->aliquotmethodref != "" ||  $queryrow->aliquotcomment != ""){
     
          if($queryrow->parentigsn != ""){
          	?>
            Parent IGSN: <?=$queryrow->parentigsn?><br />
			<?
			}
          if($queryrow->igsn != ""){
          	?>
            IGSN: <?=$queryrow->igsn?><br />
            <?
          	}
          if($queryrow->laboratoryname != ""){
          	?>
            Lab Name: <?=$queryrow->laboratoryname?><br />
          	<?
          	}
          if($queryrow->analystname != ""){
          	?>
            Analyst Name: <?=$queryrow->analystname?><br />
            <?
          	}
          if($queryrow->aliquotreference != ""){
          	?>
            Aliquot Reference: <?=$queryrow->aliquotreference?><br />
            <?
          	}
          if($queryrow->aliquotinstmethod != ""){
          	?>
            Aliquot Method: <?=$queryrow->aliquotinstmethod?><br />
            <?
          	}
          if($queryrow->aliquotmethodref != ""){
          	?>
            Aliquot Method Ref: <?=$queryrow->aliquotmethodref?><br />
            <?
          	}
          if($queryrow->aliquotcomment != ""){
          	?>
            Aliquot Comment: <?=$queryrow->aliquotcomment?><br />
            <?
          	}
          	$runquery="yes";
          }else{
          ?>
          No constraints set
          <?
        }
        ?>
      </td>
    </tr>
	
    <tr style="vertical-align:middle">
      <td style="padding:10px 10px 10px 0px;margin:0px 10px 0px 0px;vertical-align:middle">
      <input type=button value="Set" onClick="window.location = 'setage.php?pkey=<?=$pkey?>';">
      <input type=button value="Clear" onClick="window.location = 'clearage.php?pkey=<?=$pkey?>';">
	  </td>
      <td style="padding:10px;vertical-align:middle">Age&nbsp;Data</td>
      <td style="padding:10px;vertical-align:middle;border:1px;border-style:solid;line-height:130%">
      <?
      if($queryrow->sampleagetype != "" || ($queryrow->sampleagevaluemin != "" and $queryrow->sampleagevaluemax != "") || ($queryrow->sampleageerranalmin != "" and $queryrow->sampleageerranalmax != "") || ($queryrow->sampleagemeanmin != "" and $queryrow->sampleagemeanmax != "") || $queryrow->sampleageerrsys != "" || $queryrow->sampleageexpl != "" || $queryrow->sampleagecomment != ""){
          if($queryrow->sampleagetype != ""){
          	?>
            Sample Age Type: <?=$queryrow->sampleagetype?><br />
            <?
          }
          if($queryrow->sampleagevaluemin != "" and getquery.sampleagevaluemax != ""){
          	?>
            Sample Age Value: <?=$queryrow->sampleagevaluemin?> &lt; X &lt; <?=$queryrow->sampleagevaluemax?> Ma<br />
            <?
          }
          if($queryrow->sampleageerranalmin != "" and getquery.sampleageerranalmax != ""){
          	?>
            Error Analysis: <?=$queryrow->sampleageerranalmin?> &lt; X &lt; <?=$queryrow->sampleageerranalmax?><br />
            <?
          }
          if($queryrow->sampleagemeanmin != "" and getquery.sampleagemeanmax != ""){
          	?>
            Mean: <?=$queryrow->sampleagemeanmin?> &lt; X &lt; <?=$queryrow->sampleagemeanmax?><br />
            <?
          }
          if($queryrow->sampleageerrsys != ""){
          	?>
            Error Sys: <?=$queryrow->sampleageerrsys?><br />
            <?
          }
          if($queryrow->sampleageexpl != ""){
          	?>
            Explanation: <?=$queryrow->sampleageexpl?><br />
            <?
          }
          if($queryrow->sampleagecomment != ""){
            ?>
            Comment: <?=$queryrow->sampleagecomment?><br />
            <?
          }
          $runquery="yes";
        }else{
          ?>
          No constraints set
          <?
        }//end if set
        ?>
      </TD>
    </TR>
  </TABLE>
  <?
  if($runquery == "yes"){
	$mycount=$db->get_var("
	select count(*) as count from ( $newquerystring ) foo
	");
	?>
	<DIV style="float:right;text-align:right;margin-top:10px"><?=$mycount?> samples found
	<?
	if($mycount > 0){
	?>
	<BR/>
	<BR/>
	<A href="results.php?pkey=<?=$pkey?>">view search results</A>  
	<?
	}
	?>
	<BR/>
	<BR/>
	<a href="search.php">new search</a>
	<br>
	</DIV>
  <?
  }
  ?>
  <DIV id="debug" style="display: none"><br />
    <br />
    <?=$newquerystring?>
  </DIV>

<?
include("includes/geochron-secondary-footer.htm");
?>