<?
/*
This script returns a list of 20 (maximum) samples
that are not yet in the EC database.
*/
header ("content-type: text/xml");

echo "<EarthChemSamples>\n";

include("db.php");

$samplenums=$db->get_results("select samplenumber from sample where inec=false order by samplenumber limit 20");

echo "\t<SampleCount>".$db->num_rows."</SampleCount>\n";

if($db->num_rows > 0){

	echo "\t\t<Samples>\n";

	foreach($samplenums as $samplenum){
		
		echo "\t\t\t<SampleNumber>".$samplenum->samplenumber."</SampleNumber>\n";
		
	}
	
	echo "\t\t</Samples>\n";
	
}

echo "</EarthChemSamples>\n";



?>