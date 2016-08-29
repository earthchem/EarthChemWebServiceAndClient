<?PHP
/*
This file accepts a PetDB sample number and
returns an EarthChem-compatible XML sample
document.
*/
header ("content-type: text/xml");
$itemcodesdesc = array(
  "AGE"=>"age",
  "MAJ"=>"major_oxides",
  "EM"=>"end_member",
  "IR"=>"radiogenic_isotope",
  "SPEC"=>"speciation_ratio",
  "NGAS"=>"noble_gas",
  "REE"=>"ree",
  "US"=>"u_series",
  "VO"=>"volatile",
  "TE"=>"te",
  "IS"=>"is",
  "RT"=>"ratio",
  "MODE"=>"mode_calculated",
  "MD"=>"model_data"
);

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
        $samplesquery = "select s.*, ii.igsn from sample s left join IGSN_INFO ii on ii.sample_num = s.sample_num where s.sample_num=$samplenumber and s.sample_num in
                               (
                               select unique b.sample_num
                                from BATCH b, TABLE_IN_REF tif, REFERENCE r 
                               where b.TABLE_IN_REF_NUM = tif.TABLE_IN_REF_NUM and r.REF_NUM = tif.REF_NUM and r.status='COMPLETED'
                               
                               )";
        //echo $samplesquery;
	$row=$db->get_row($samplesquery);

	if($db->num_rows > 0){
	
		echo "<EarthChemModel firstResultPosition=\"0\" majordateupdated=\"0\" totalResultsAvailable=\"0\" totalResultsReturned=\"0\" xmlns=\"http://www.earthchemportal.org/schema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.earthchemportal.org http://www.earthchemportal.org/schema/earthchem_v26.xsd\">\n";
		
		//get ages *************************************************************************************
		//get age details here
		$agerow=$db->get_row("select * from sample_age where sample_num=$samplenumber");
		
		
		//get location *************************************************************************************
		//get location details here
		$locationrow=$db->get_row("select 
									loc.*
									from location loc, 
									station_by_location sbl,
									sample samp
									where 
									loc.location_num = sbl.location_num and
									sbl.station_num = samp.station_num and
									samp.sample_num=$samplenumber
									
									");
									
		$locname=$db->get_var("select LOCATION_NAME from GEOGRAPH_LOC where location_num=$locationrow->LOCATION_NUM");
                //Get cruiseid
		$cruiseid=$db->get_var("select e.EXPEDITION_CODE 
                                        from sample s, station st, expedition e 
                                        where s.sample_num=$samplenumber and s.STATION_NUM = st.STATION_NUM and st.EXPEDITION_NUM=e.EXPEDITION_NUM"
                                      );
		
		if($locname!=""){
			$locname="; $locname";
		}

		echo "\t<EarthChemSample age_max=\"$agerow->AGE_MAX\" age_min=\"$agerow->AGE_MIN\" genericdescriptor=\"".htmlentities($row->SAMPLE_ID).htmlentities($locname)."\" igsn=\"$row->IGSN\" sample_id=\"".htmlentities($row->SAMPLE_ID)."\" samplenumber=\"$samplenumber\" source=\"petdb\" cruiseid=\"$cruiseid\" url=\"http://www.earthchem.org/petdbWeb/search/sample_info.jsp?singlenum=$samplenumber\">\n";

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
							ref.ref_num,
							ref.journal,
							ref.title,
							ref.first_page,
							ref.last_page,
							ref.pub_year,
                                                        ref.publication_doi
						from 
						sample samp,
						batch bat,
						analysis anal,
						data_quality dq,
						reference ref
						where samp.sample_num=bat.sample_num and
						bat.batch_num = anal.batch_num and
						anal.data_quality_num = dq.data_quality_num and
						dq.ref_num = ref.ref_num and
                                                ref.status='COMPLETED' and
						samp.sample_num = $samplenumber
						group by ref.first_page, ref.last_page, ref.ref_num, ref.journal, ref.title, ref.pub_year,ref.publication_doi
		");


		
		if($db->num_rows > 0){ //if citation rows found

		
			foreach($citationrows as $citationrow){
				
				$temp_journal=htmlentities($citationrow->JOURNAL);
				$year=$citationrow->PUB_YEAR;
				$doi=htmlentities($citationrow->PUBLICATION_DOI);
				if($citationrow->FIRST_PAGE != "" and $citationrow->LAST_PAGE != ""){
					$temp_pages=$citationrow->FIRST_PAGE." - ".$citationrow->LAST_PAGE;
				}else{
					$temp_pages="";
				}
				$temp_title=htmlentities($citationrow->TITLE);
				
				
				echo "\t\t\t<Citation journal=\"$temp_journal\" year=\"$year\" pages=\"$temp_pages\" doi=\"$doi\">\n";
				
				echo "\t\t\t\t<Title>$temp_title</Title>\n";
				
				$authorrows=$db->get_results("
								select per.first_name,per.last_name
								from author_list al, person per
								where al.person_num = per.person_num
								and al.ref_num = $citationrow->REF_NUM
                                                                order by al.author_order
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
				$myclass1=htmlspecialchars($firstclasses[0]);
				$myclass2=htmlspecialchars($firstclasses[1]);
				$myclass3=htmlspecialchars($firstclasses[2]);
				$myclass4=htmlspecialchars(strtolower($sampletyperow->ROCKCLASS));


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
                                $materialQry =" select distinct mat.material_num,
                                                                mat.material_name
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
                                                                 ref.status='COMPLETED' and
                                                                 ref.ref_num = $citationrow->REF_NUM and 
                                                                 samp.sample_num = ".$samplenumber;
				$materialrows=$db->get_results($materialQry);
			        foreach($materialrows as $materialrow){
				  $material_num=$materialrow->MATERIAL_NUM;
				  $mymaterial="unspecified";
				  if($material_num==3){$mymaterial="glass";}
				  if($material_num==4){$mymaterial="groundmass";}
				  if($material_num==5){$mymaterial="inclusion";}
				  if($material_num==6){$mymaterial="mineral";}
				  if($material_num==7){$mymaterial="unspecified";}
				  if($material_num==8){$mymaterial="whole rock";}
				  $mymaterialdescription=$materialrow->MATERIAL_NAME;

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
                                                                bat.material_num = $material_num and
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
				
                                if( $material_num != 6)
				  echo "\t\t\t\t\t<Material materialdescription=\"".htmlentities($mymaterialdescription)."\" materialtype=\"$mymaterial\">\n";
                                else
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
                                                                bat.material_num = $material_num and
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
									im.item_code,
                                                                        itp.item_type_code
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
								item_measured im,
                                                                itemtype_list it,
                                                                item_type itp
								where samp.sample_num=bat.sample_num and
								bat.batch_num = anal.batch_num and
								bat.material_num = mat.material_num and
								anal.data_quality_num = dq.data_quality_num and
								dq.ref_num = ref.ref_num and
								dq.method_num = meth.method_num and
								dq.institution_num = inst.institution_num and
								anal.analysis_num=chem.analysis_num and
								chem.item_measured_num = im.item_measured_num and
                                                                chem.itemtypelist_num = it.itemtypelist_num and
                                                                im.item_measured_num = it.item_measured_num and
                                                                it.item_type_num = itp.item_type_num and
                                                                bat.material_num = $material_num and
								samp.sample_num = $samplenumber and ref.ref_num=$ref_num and
								dq.data_quality_num=$dqnum and
								anal.analysis_num=$analnum
						");
						
//print_r($itemrows);
						
						if($db->num_rows > 0){
						
							//put in method here, since we know there are items below it
                                                        $mymethodcode=$methodrow->METHOD_CODE;
                                                        $mylabForQuery=str_replace("'","''",$methodrow->INSTITUTION);
                                                        $mylab=htmlspecialchars( $methodrow->INSTITUTION);
							echo "\t\t\t\t\t\t<method name=\"$mymethodcode\" lab=\"$mylab\">\n";
							
							foreach($itemrows as $itemrow){
								
								$usevalue=$itemrow->VALUE_MEAS;
								$useunit=$itemrow->UNIT;
								if($useunit==""){$useunit="RATIO";}
                                                                $myitem_code = $itemrow->ITEM_CODE;
								$useitem=strtolower($myitem_code);
                                                                $myitem_type = $itemcodesdesc[$itemrow->ITEM_TYPE_CODE];
								
								echo "\t\t\t\t\t\t\t<item group=\"chemical\" name=\"$useitem\" qualityrank=\"-1\" type=\"$myitem_type\" units=\"$useunit\" value=\"$usevalue\">\n";
                                                           
						                //standards ************************************************************************
                                                              $standardsrows=$db->get_results
                                                                             (" select distinct d.standard_name, d.standard_value 
                                                                                from standard d, DATA_QUALITY q, SAMPLE s, batch b, table_in_ref t, institution i, item_measured im, itemmethod_list ml, method m
                                                                                where d.DATA_QUALITY_NUM = q.DATA_QUALITY_NUM 
                                                                                      and b.SAMPLE_NUM = s.SAMPLE_NUM
                                                                                      and t.REF_NUM = q.REF_NUM
                                                                                      and t.TABLE_IN_REF_NUM = b.TABLE_IN_REF_NUM
                                                                                      and q.institution_num = i.INSTITUTION_NUM
                                                                                      and i.INSTITUTION = '$mylabForQuery'
                                                                                      and s.sample_num=$samplenumber
                                                                                      and im.ITEM_MEASURED_NUM = d.ITEM_MEASURED_NUM
                                                                                      and im.ITEM_CODE = '$myitem_code'
                                                                                      and b.material_num = $material_num
                                                                                      and ml.item_measured_num = d.item_measured_num
                                                                                      and ml.METHOD_NUM = m.METHOD_NUM
                                                                                      and m.METHOD_CODE='$mymethodcode'
                                                                            ");


//print_r($standardsrows);
						if($db->num_rows > 0){
							    echo "\t\t\t\t\t\t\t<standards>\n";
							    foreach( $standardsrows as $standardrow){
                                                              $mystdname=htmlspecialchars($standardrow->STANDARD_NAME);
                                                              $mystdvalue=$standardrow->STANDARD_VALUE;
							      echo "\t\t\t\t\t\t\t\t<standard name=\"$mystdname\" value=\"$mystdvalue\"/>\n";
                                                            }
						   	    echo "\t\t\t\t\t\t\t\t</standards>\n";
}
							  echo "\t\t\t\t\t\t\t</item>\n";
							}//end foreach itemrow
							
							echo "\t\t\t\t\t\t</method>\n";
						
						}//end if items > 0								
					
					}//end foreach methodrows

				}//end if methodrows > 0					
			
				echo "\t\t\t\t\t</Material>\n";
                                }
				
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
