<?PHP
/*
This file accepts a PetDB sample number and
returns an EarthChem-compatible XML sample
document.
*/
header ("content-type: text/xml");


include("db.php");

//$myrow=$db->get_results("select * from datasource where rownum=1");
//print_r($myrow);
//exit();

$itemcodesdesc = array(
  "Age"=>"age",
  "Major Element"=>"major_oxides",
  "Radiogenic Isotope"=>"radiogenic_isotope",
  "Trace Element"=>"te",
  "Porewater Component"=>"porewater_component",
  "Sedimentary Components"=>"sedimentary_components",
  "Stable Isotope"=> "is"
);

$samplenumber=$_GET['sample_num'];

if($samplenumber!=""){

	//get sample level details
	/*
	sample_id
	IGSN
	generic descriptor
	URL
	*/
	$row=$db->get_row("
			select
			q_geoobject.agemin,
			q_geoobject.agemax,
			q_geoobject.systemid,
			q_geoobject.igsn,
			q_geoobject.longstart,
			q_geoobject.latstart,
			q_geoobject.locname,
                        q_geoobject.expedition_num
			from
			observedvalue
			join itemanalysis on itemanalysis.itemanalysis_num =
			observedvalue.itemanalysis_num
			join batch on batch.batch_num = itemanalysis.batch_num
			join q_geoobject on q_geoobject.geoobject_num = batch.geoobject_num
			join datasource on datasource.datasource_num = batch.datasource_num
			join itemquality on itemquality.itemquality_num =
			itemanalysis.itemquality_num
			join item on item.item_num = itemquality.item_num
			join methodquality on methodquality.methodquality_num =
			itemquality.methodquality_num
			join method on method.method_num = methodquality.method_num
			join keyword on keyword.keyword_num = batch.material_num
			left join valueunit on observedvalue.VALUEUNIT_NUM = valueunit.VALUEUNIT_NUM
			where q_geoobject.geoobject_num=$samplenumber and batch.material_num=618
			group by
			q_geoobject.agemin,
			q_geoobject.agemax,
			q_geoobject.systemid,
			q_geoobject.igsn,
			q_geoobject.longstart,
			q_geoobject.latstart,
			q_geoobject.locname,
                        q_geoobject.expedition_num
	");

	if($db->num_rows > 0){
	
		echo "<EarthChemModel firstResultPosition=\"0\" majordateupdated=\"0\" totalResultsAvailable=\"0\" totalResultsReturned=\"0\" xmlns=\"http://www.earthchemportal.org/schema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.earthchemportal.org http://www.earthchemportal.org/schema/earthchem_schema.xsd\">\n";
		
		//get ages *************************************************************************************
		//get age details here
		//(comes from query above)
		$myminage="";
		$mymaxage="";
		$myage="";
		$agemin=$row->AGEMIN;
		$agemax=$row->AGEMAX;
		
		//echo "<br>agemin: $agemin";
		
		if($agemin != "" && $agemax != ""){
			$myagemin=$agemin;
			$myagemax=$agemax;
		}elseif($agemin != ""){
			$myage=$agemin;
		}
	
                //Get cruiseid
                $cruiseid=$db->get_var("select keywordname from keyword where KEYWORD_NUM = $row->EXPEDITION_NUM");	
		
		echo "\t<EarthChemSample age_max=\"$myagemax\" age=\"$myage\" age_min=\"$myagemin\" genericdescriptor=\"".htmlentities($row->SYSTEMID)."; ".htmlentities($row->LOCNAME)."\" igsn=\"$row->IGSN\" sample_id=\"".htmlentities($row->SYSTEMID)."\" samplenumber=\"$samplenumber\" source=\"seddb\" cruiseid=\"$cruiseid\" url=\"http://www.earthchem.org/seddbWeb/search/sampledisplay.do?num=$samplenumber\">\n";

		//get location *************************************************************************************
		//get location details here
		//from above

		echo "\t\t<Geography>\n";
		echo "\t\t\t<Location>\n";
		echo "\t\t\t\t<Point>\n";
		echo "\t\t\t\t\t<coord>\n";
		echo "\t\t\t\t\t\t<X>$row->LONGSTART</X>\n";
		echo "\t\t\t\t\t\t<Y>$row->LATSTART</Y>\n";
		echo "\t\t\t\t\t</coord>\n";
		echo "\t\t\t\t</Point>\n";
		echo "\t\t\t</Location>\n";
		echo "\t\t\t<Location_Precision>$locationrow->LOC_PRECISION</Location_Precision>\n";
		echo "\t\t</Geography>\n";

		
		echo "\t\t<EarthChemData>\n";
		
		//get citations *************************************************************************************
		$citationrows=$db->get_results("
			select
			datasource.datasource_num,
			datasource.title,
			datasource.author,
			datasource.pubyear,
			datasource.citation,
			q_geoobject.rockclassstring,
			keyword.keywordname,
                        datasource.datasourcedoi
			from
			observedvalue
			join itemanalysis on itemanalysis.itemanalysis_num =
			observedvalue.itemanalysis_num
			join batch on batch.batch_num = itemanalysis.batch_num
			join q_geoobject on q_geoobject.geoobject_num = batch.geoobject_num
			join datasource on datasource.datasource_num = batch.datasource_num
			join itemquality on itemquality.itemquality_num =
			itemanalysis.itemquality_num
			join item on item.item_num = itemquality.item_num
			join methodquality on methodquality.methodquality_num =
			itemquality.methodquality_num
			join method on method.method_num = methodquality.method_num
			join keyword on keyword.keyword_num = batch.material_num
			left join valueunit on observedvalue.VALUEUNIT_NUM = valueunit.VALUEUNIT_NUM
			where q_geoobject.geoobject_num=$samplenumber and batch.material_num=618
			group by
			datasource.datasource_num,
			datasource.title,
			datasource.author,
			datasource.pubyear,
			datasource.citation,
			q_geoobject.rockclassstring,
			keyword.keywordname,
                        datasource.datasourcedoi
			");

		
		if($db->num_rows > 0){ //if citation rows found

		
			foreach($citationrows as $citationrow){

//print_r($citationrow);
                                $dataSource=htmlentities($citationrow->DATASOURCEDOI);
				echo "\t\t\t<Citation journal=\"$citationrow->DATASOURCE_NUM\" year=\"$citationrow->PUBYEAR\" pages=\"$temp_pages\" doi=\"$dataSource\">\n";
				
				echo "\t\t\t\t<Title>$citationrow->TITLE</Title>\n";
				
				$authorrows=split("; ",$citationrow->AUTHOR);
				
				foreach($authorrows as $authorrow){
				
					//individual author here
					$currauthor=htmlentities(trim($authorrow));
					//echo "author: $currauthor<br>";
					echo "\t\t\t\t<Author>$currauthor</Author>\n";
				
				}

				echo "\t\t\t\t<Sampletype>\n";

				//sampletype ************************************
				//get sampletype information here
				//from above query
				
				$rockclassstring=$citationrow->ROCKCLASSSTRING;
				$rockclassstring=split("; ",$rockclassstring);
				$rockclassstring=strtolower($rockclassstring[0]);


				$firstclasses=split(' > ',$rockclassstring);
				$myclass1=$firstclasses[0];
				$myclass2="unknown";
				$myclass3="unknown";
				$myclass4=$firstclasses[1];


				//put in phase
				echo "\t\t\t\t\t<Phase>\n";
				echo "\t\t\t\t\t\t<ROCK>\n";
				echo "\t\t\t\t\t\t\t<class1>$myclass1</class1>\n";
				echo "\t\t\t\t\t\t\t<class2>$myclass2</class2>\n";
				echo "\t\t\t\t\t\t\t<class3>$myclass3</class3>\n";
				echo "\t\t\t\t\t\t\t<class4>$myclass4</class4>\n";
				echo "\t\t\t\t\t\t</ROCK>\n";
				echo "\t\t\t\t\t</Phase>\n";

				
				//materials *******************************************
				//get material information here
				$mymaterial="whole rock";
				
				$mymineral="";			
				
				echo "\t\t\t\t\t<Material materialdescription=\"".htmlentities($citationrow->KEYWORDNAME)."\" materialtype=\"$mymaterial\" mineralname=\"$mymineral\">\n";

				//put in material
				
				//methods *****************************************************************************
				//Get methods here (including lab name)
				$methodrows=$db->get_results("
					select
					method.method_num,
					method.methodcode,
					methodquality.lab
					from
					observedvalue
					join itemanalysis on itemanalysis.itemanalysis_num =
					observedvalue.itemanalysis_num
					join batch on batch.batch_num = itemanalysis.batch_num
					join q_geoobject on q_geoobject.geoobject_num = batch.geoobject_num
					join datasource on datasource.datasource_num = batch.datasource_num
					join itemquality on itemquality.itemquality_num =
					itemanalysis.itemquality_num
					join item on item.item_num = itemquality.item_num
					join methodquality on methodquality.methodquality_num =
					itemquality.methodquality_num
					join method on method.method_num = methodquality.method_num
					join keyword on keyword.keyword_num = batch.material_num
					left join valueunit on observedvalue.VALUEUNIT_NUM = valueunit.VALUEUNIT_NUM
					where q_geoobject.geoobject_num=$samplenumber and batch.material_num=618
					group by
					method.method_num,
					method.methodcode,
					methodquality.lab
				");

//print_r($methodrows);

				if($db->num_rows > 0){

					foreach($methodrows as $methodrow){

						$methodnum=$methodrow->METHOD_NUM;

						//items ************************************************************************
						$itemrows=$db->get_results("
							select
							item.itemcode,
							valueunit.valueunitcode,
							observedvalue.ovnumericvaluemin,
                                                        q_itemtypedorder.itemtype
							from
							observedvalue
							join itemanalysis on itemanalysis.itemanalysis_num =
							observedvalue.itemanalysis_num
							join batch on batch.batch_num = itemanalysis.batch_num
							join q_geoobject on q_geoobject.geoobject_num = batch.geoobject_num
							join datasource on datasource.datasource_num = batch.datasource_num
							join itemquality on itemquality.itemquality_num =
							itemanalysis.itemquality_num
							join item on item.item_num = itemquality.item_num
							join methodquality on methodquality.methodquality_num =
							itemquality.methodquality_num
							join method on method.method_num = methodquality.method_num
							join keyword on keyword.keyword_num = batch.material_num
                                                        join q_itemtypedorder on q_itemtypedorder.itemcode=item.itemcode
							left join valueunit on observedvalue.VALUEUNIT_NUM = valueunit.VALUEUNIT_NUM
							where q_geoobject.geoobject_num=$samplenumber and batch.material_num=618
							and method.method_num=$methodnum and item.itemcode!='AGE'
							group by
							item.itemcode,
							valueunit.valueunitcode,
							observedvalue.ovnumericvaluemin,
                                                        q_itemtypedorder.itemtype
						");
						
//print_r($itemrows);
						
						if($db->num_rows > 0){
						
                                                        $mymethodcode=$methodrow->METHODCODE;
                                                        $mymethodlab = htmlspecialchars($methodrow->LAB);
							//put in method here, since we know there are items below it
							echo "\t\t\t\t\t\t<method name=\"$mymethodcode\" lab=\"$mymethodlab\">\n";
							
							foreach($itemrows as $itemrow){
								
							  $usevalue=$itemrow->OVNUMERICVALUEMIN;
							  $useunit=strtoupper($itemrow->VALUEUNITCODE);
							  if($useunit=="PICOGRAM PER GRAM"){$useunit="PPG";}
							  if($useunit==""){$useunit="RATIO";}
							  $myitemcode=$itemrow->ITEMCODE;
							  $useitem=strtolower($myitemcode);
                                                          $myitemtype = $itemcodesdesc[$itemrow->ITEMTYPE];
								
							  echo "\t\t\t\t\t\t\t<item group=\"chemical\" name=\"$useitem\" qualityrank=\"-1\" type=\"$myitemtype\" units=\"$useunit\" value=\"$usevalue\">\n";
                                                          //standards ************************************************************************
                                                          $standardsrows=$db->get_results
                                                                             ("select distinct qi.STD_REFSAMPLENAME,qi.STD_REFSAMPLEVALUE
                                                                               from item i, itemquality iq, Q_IQPVALUE qi,methodquality mq, Q_GEOOBJECT qgo, BATCH b, itemanalysis ia,method m, datasource d
                                                                               where qi.std_refsamplename is not null
                                                                               and qi.ITEMQUALITY_NUM = iq.ITEMQUALITY_NUM
                                                                               and i.ITEM_NUM = iq.ITEM_NUM
                                                                               and iq.METHODQUALITY_NUM = mq.METHODQUALITY_NUM
                                                                               and qgo.GEOOBJECT_NUM = b.GEOOBJECT_NUM
                                                                               and m.METHOD_NUM = mq.METHOD_NUM
                                                                               and d.datasource_num = b.DATASOURCE_NUM
                                                                               and d.DATASOURCE_NUM = mq.DATASOURCE_NUM
                                                                               and b.MATERIAL_NUM = 618
                                                                               and m.METHODCODE='$mymethodcode'
                                                                               and i.itemcode='$myitemcode'
                                                                               and qgo.GEOOBJECT_NUM = $samplenumber 
                                                                            ");
                                                            if($db->num_rows > 0){
                                                              echo "\t\t\t\t\t\t\t<standards>\n";
                                                              foreach( $standardsrows as $standardrow){
                                                                $mystdname=htmlspecialchars($standardrow->STD_REFSAMPLENAME);
                                                                $mystdvalue=$standardrow->STD_REFSAMPLEVALUE;
                                                                echo "\t\t\t\t\t\t\t\t<standard name=\"$mystdname\" value=\"$mystdvalue\"/>\n";
                                                              }
                                                              echo "\t\t\t\t\t\t\t\t</standards>\n";
                                                            } //end of $db->num_rows > 0
  
							
							  echo "\t\t\t\t\t\t\t</item>\n";
							}//end foreach itemrow
							
							echo "\t\t\t\t\t\t</method>\n";
						
						}//end if items > 0								
					
					}//end foreach methodrows

				}//end if methodrows > 0					
			
				echo "\t\t\t\t\t</Material>\n";
					
				
				
				echo "\t\t\t\t</Sampletype>\n";
				echo "\t\t\t</Citation>\n";
			}//end for each citation		
		
		}
		
		echo "\t\t</EarthChemData>\n";
		echo "\t</EarthChemSample>\n";
		echo "</EarthChemModel>\n";
	}else{
		echo "<Error>Sample $samplenumber does not exist in SedDB.</Error>\n";
	}//end if $db->num_rows > 0
}else{
	echo "<Error>No sample number provided.</Error>\n";
}//end if samplenumber!=""

?>
