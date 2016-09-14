<?PHP
/*
This file accepts a PetDB sample number and
returns an EarthChem-compatible XML sample
document.
*/
header ("content-type: text/xml");


include("db.php");



					/*
					$myrows=$db->get_results("select * from sample_comment sc,
											rockclass rc,
											rocktype rt
											where sc.rockclass_num = rc.rockclass_num and
											rc.rocktype_num = rt.rocktype_num and 
											sc.sample_num = 161
											--and sc.ref_num = 40
											");

					print_r($myrows);
					*/
					















$samplenumber=$_GET['sample_num'];

if($samplenumber!=""){

	//get sample level details
	/*
	sample_id
	IGSN
	generic descriptor
	URL
	*/
	$row=$db->get_row("select * from sample where sample_num=$samplenumber");

	if($db->num_rows > 0){
	
		echo "<EarthChemModel firstResultPosition=\"0\" majordateupdated=\"0\" totalResultsAvailable=\"0\" totalResultsReturned=\"0\" xmlns=\"http://www.earthchemportal.org/schema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.earthchemportal.org http://www.earthchemportal.org/schema/earthchem_v26.xsd\">\n";
		
		//get ages *************************************************************************************
		//get age details here
		$agerow=$db->get_row("select * from sample_age where sample_num=$samplenumber");
		
		echo "\t<EarthChemSample age_max=\"$agerow->AGE_MAX\" age_min=\"$agerow->AGE_MIN\" genericdescriptor=\"".htmlentities($row->SAMPLE_ID)."\" igsn=\"$row->igsn\" sample_id=\"".htmlentities($row->SAMPLE_ID)."\" samplenumber=\"$samplenumber\" source=\"petdb\" url=\"http://isotope.ldeo.columbia.edu:7001/petdbWeb/search/sample_info.jsp?singlenum=$samplenumber\">\n";
		
		//get location *************************************************************************************
		//get location details here
		$locationrow=$db->get_row("select loc.* 
									from location loc, 
									station_by_location sbl,
									sample samp
									where 
									loc.location_num = sbl.location_num and
									sbl.station_num = samp.station_num and
									samp.sample_num=$samplenumber
									
									");

		echo "\t\t<Geography>\n";
		echo "\t\t\t<Location>\n";
		echo "\t\t\t\t<Point>\n";
		echo "\t\t\t\t\t<coord>\n";
		echo "\t\t\t\t\t\t<X>$locationrow->LONGITUDE</X>\n";
		echo "\t\t\t\t\t\t<Y>$locationrow->LATITUDE</Y>\n";
		echo "\t\t\t\t\t</coord>\n";
		echo "\t\t\t\t</Point>\n";
		echo "\t\t\t</Location>\n";
		echo "\t\t\t<Location_Precision>$locationrow->LOC_PRECISION</Location_Precision>\n";
		echo "\t\t</Geography>\n";
		
		
		echo "\t\t<EarthChemData>\n";
		
		//get citations *************************************************************************************
		$citationrows=$db->get_results("
						select 
							mat.material_num,
							ref.ref_num,
							ref.journal,
							ref.title,
							ref.first_page,
							ref.last_page,
							ref.pub_year
						from 
						sample samp,
						batch bat,
						material mat,
						analysis anal,
						data_quality dq,
						reference ref
						where samp.sample_num=bat.sample_num and
						bat.batch_num = anal.batch_num and
						bat.material_num = mat.material_num and
						anal.data_quality_num = dq.data_quality_num and
						dq.ref_num = ref.ref_num and
						samp.sample_num = $samplenumber
						group by ref.first_page, ref.last_page, mat.material_num, ref.ref_num, ref.journal, ref.title, ref.pub_year
		");


		
		if($db->num_rows > 0){ //if citation rows found

		
			foreach($citationrows as $citationrow){
				
				$temp_journal=htmlentities($citationrow->JOURNAL);
				$year=$citationrow->PUB_YEAR;
				if($citationrow->FIRST_PAGE != "" and $citationrow->LAST_PAGE != ""){
					$temp_pages=$citationrow->FIRST_PAGE." - ".$citationrow->LAST_PAGE;
				}else{
					$temp_pages="";
				}
				$temp_title=htmlentities($citationrow->TITLE);
				
				
				echo "\t\t\t<Citation journal=\"$temp_journal\" year=\"$year\" pages=\"$temp_pages\">\n";
				
				echo "\t\t\t\t<Title>$temp_title</Title>\n";
				
				$authorrows=$db->get_results("
								select per.first_name,per.last_name
								from author_list al, person per
								where al.person_num = per.person_num
								and al.ref_num = $citationrow->REF_NUM
				");
				
				foreach($authorrows as $authorrow){
				
					//individual author here
					$currauthor=htmlentities(trim($authorrow->LAST_NAME.", ".$authorrow->FIRST_NAME));
					//echo "author: $currauthor<br>";
					echo "\t\t\t\t<Author>$currauthor</Author>\n";
				
				}

				echo "\t\t\t\t<Sampletype>\n";

				//sampletype ************************************
				//get sampletype information here
				$sampletyperow=$db->get_row("
								select * from sample_comment sc,
												rockclass rc,
												rocktype rt
											where sc.rockclass_num = rc.rockclass_num and
											rc.rocktype_num = rt.rocktype_num and 
											sc.sample_num = $samplenumber and
											sc.ref_num = $citationrow->REF_NUM
				");


				//exit();

				$firstclasses=split(":",$sampletyperow->ROCKTYPE_NAME);
				$myclass1=$firstclasses[0];
				$myclass2=$firstclasses[1];
				$myclass3=$firstclasses[2];
				$myclass4=strtolower($sampletyperow->ROCKCLASS);


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
				$material_num=$citationrow->MATERIAL_NUM;
				$mymaterial="unspecified";
				if($material_num==3){$mymaterial="glass";}
				if($material_num==4){$mymaterial="groundmass";}
				if($material_num==5){$mymaterial="inclusion";}
				if($material_num==6){$mymaterial="mineral";}
				if($material_num==7){$mymaterial="unspecified";}
				if($material_num==8){$mymaterial="whole rock";}

				$ref_num=$citationrow->REF_NUM;

				$mineralrow=$db->get_row("
								select 
									bat.batch_num
								from 
								sample samp,
								batch bat,
								material mat,
								analysis anal,
								data_quality dq,
								reference ref
								where samp.sample_num=bat.sample_num and
								bat.batch_num = anal.batch_num and
								bat.material_num = mat.material_num and
								anal.data_quality_num = dq.data_quality_num and
								dq.ref_num = ref.ref_num and
								samp.sample_num = $samplenumber and ref.ref_num=$ref_num
								group by bat.batch_num
				");
				
				$batchnum=$mineralrow->BATCH_NUM;
				$mineralname=$db->get_var("
							select ml.mineral_name
							from
							mineral min,
							mineral_list ml
							where
							min.batch_num = $batchnum
							and min.mineral_num = ml.mineral_num
				");
				
				//echo "batch_num to get mineral name: $mineralrow->BATCH_NUM<br>";
				
				$mymineral=htmlentities(strtolower($mineralname));			
				
				echo "\t\t\t\t\t<Material materialdescription=\"".htmlentities($mymaterialdescription)."\" materialtype=\"$mymaterial\" mineralname=\"$mymineral\">\n";
				
				//put in material
				
				//methods *****************************************************************************
				//Get methods here (including lab name)
				$methodrows=$db->get_results("
								select 
									dq.data_quality_num,
									meth.method_code,
									inst.institution,
									--meth.*,
									--bat.*,
									--dq.*
									anal.analysis_num
								from 
								sample samp,
								batch bat,
								material mat,
								analysis anal,
								data_quality dq,
								reference ref,
								method meth,
								institution inst
								where samp.sample_num=bat.sample_num and
								bat.batch_num = anal.batch_num and
								bat.material_num = mat.material_num and
								anal.data_quality_num = dq.data_quality_num and
								dq.ref_num = ref.ref_num and
								dq.method_num = meth.method_num and
								dq.institution_num = inst.institution_num and
								samp.sample_num = $samplenumber and ref.ref_num=$ref_num
								

				");

//print_r($methodrows);


				if($db->num_rows > 0){

					foreach($methodrows as $methodrow){
					
						$dqnum=$methodrow->DATA_QUALITY_NUM;
						$analnum=$methodrow->ANALYSIS_NUM;

						//items ************************************************************************
						$itemrows=$db->get_results("
								select 
									chem.value_meas,
									chem.unit,
									im.item_code
								from 
								sample samp,
								batch bat,
								material mat,
								analysis anal,
								data_quality dq,
								reference ref,
								method meth,
								institution inst,
								chemistry chem,
								item_measured im
								where samp.sample_num=bat.sample_num and
								bat.batch_num = anal.batch_num and
								bat.material_num = mat.material_num and
								anal.data_quality_num = dq.data_quality_num and
								dq.ref_num = ref.ref_num and
								dq.method_num = meth.method_num and
								dq.institution_num = inst.institution_num and
								anal.analysis_num=chem.analysis_num and
								chem.item_measured_num = im.item_measured_num and
								samp.sample_num = $samplenumber and ref.ref_num=$ref_num and
								dq.data_quality_num=$dqnum and
								anal.analysis_num=$analnum
						");
						
//print_r($itemrows);
						
						if($db->num_rows > 0){
						
							//put in method here, since we know there are items below it
							echo "\t\t\t\t\t\t<method name=\"$methodrow->METHOD_CODE\" lab=\"$methodrow->INSTITUTION\">\n";
							
							foreach($itemrows as $itemrow){
								
								$usevalue=$itemrow->VALUE_MEAS;
								$useunit=$itemrow->UNIT;
								if($useunit==""){$useunit="RATIO";}
								$useitem=strtolower($itemrow->ITEM_CODE);
								
								echo "\t\t\t\t\t\t\t<item group=\"chemical\" name=\"$useitem\" qualityrank=\"-1\" type=\"major_oxides\" units=\"$useunit\" value=\"$usevalue\"/>\n";
							
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
		echo "<Error>Sample $samplenumber does not exist in Petdb.</Error>\n";
	}//end if $db->num_rows > 0
}else{
	echo "<Error>No sample number provided.</Error>\n";
}//end if samplenumber!=""

?>