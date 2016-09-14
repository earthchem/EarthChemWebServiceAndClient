<?
/*
queries.php -- builds the queries for search
s=searchquery row
q=querystring being built
*/

include_once("db.php");
include("pkey.php");

$s=$db->get_row("select * from search_query where search_query_pkey=$pkey");

$q="";
$delim="";

if($s->ownername !=""){
	$q.=$delim."owner.owner_pkey = ".$s->ownername;
	$delim="\nAND ";
}

if($s->projectname !=""){
	$q.=$delim."lower(projectname) like '%".strtolower($s->projectname)."%'";
	$delim="\nAND ";
}

if($s->samplename !=""){
	$q.=$delim."lower(samplename) like '%".strtolower($s->samplename)."%'";
	$delim="\nAND ";
}

if($s->labsamplename !=""){
	$q.=$delim."lower(labsamplename) like '%".strtolower($s->labsamplename)."%'";
	$delim="\nAND ";
}

if($s->aliquotname !=""){
	$q.=$delim."lower(aliquotname) like '%".strtolower($s->aliquotname)."%'";
	$delim="\nAND ";
}

if($s->mineral !=""){
	$q.=$delim."lower(mineral) like '%".strtolower($s->mineral)."%'";
	$delim="\nAND ";
}

if($s->spikename !=""){
	$q.=$delim."lower(spikename) like '%".strtolower($s->spikename)."%'";
	$delim="\nAND ";
}

if($s->comments !=""){
	$q.=$delim."lower(comments) like '%".strtolower($s->comments)."%'";
	$delim="\nAND ";
}



//****************************

if($s->uft_min != "" and $s->uft_max != ""){
	$q.=$delim."uft >= ".$s->uft_min." AND uft <= ".$s->uft_max;
	$delim="\nAND ";
}

if($s->thft_min != "" and $s->thft_max != ""){
	$q.=$delim."thft >= ".$s->thft_min." AND thft <= ".$s->thft_max;
	$delim="\nAND ";
}

if($s->mass_min != "" and $s->mass_max != ""){
	$q.=$delim."mass >= ".$s->mass_min." AND mass <= ".$s->mass_max;
	$delim="\nAND ";
}

if($s->meanl_min != "" and $s->meanl_max != ""){
	$q.=$delim."meanl >= ".$s->meanl_min." AND meanl <= ".$s->meanl_max;
	$delim="\nAND ";
}

if($s->meanw_min != "" and $s->meanw_max != ""){
	$q.=$delim."meanw >= ".$s->meanw_min." AND meanw <= ".$s->meanw_max;
	$delim="\nAND ";
}

if($s->meanw2_min != "" and $s->meanw2_max != ""){
	$q.=$delim."meanw2 >= ".$s->meanw2_min." AND meanw2 <= ".$s->meanw2_max;
	$delim="\nAND ";
}

if($s->meanesr_min != "" and $s->meanesr_max != ""){
	$q.=$delim."meanesr >= ".$s->meanesr_min." AND meanesr <= ".$s->meanesr_max;
	$delim="\nAND ";
}

//******** He Line Data ********************************************

if($s->loadingdate_min != "" and $s->loadingdate_max != ""){
	$q.=$delim."loadingdate >= '".$s->loadingdate_min."' AND loadingdate <= '".$s->loadingdate_max."'";
	$delim="\nAND ";
}elseif($s->loadingdate_min != ""){
	$q.=$delim."loadingdate >= '".$s->loadingdate_min."'";
	$delim="\nAND ";
}elseif($s->loadingdate_max != ""){
	$q.=$delim."loadingdate <= '".$s->loadingdate_max."'";
	$delim="\nAND ";
}

if($s->totalhe4ncc_min != "" and $s->totalhe4ncc_max != ""){
	$q.=$delim."totalhe4ncc >= ".$s->totalhe4ncc_min." AND totalhe4ncc <= ".$s->totalhe4ncc_max;
	$delim="\nAND ";
}

if($s->totalabserr_min != "" and $s->totalabserr_max != ""){
	$q.=$delim."totalabserr >= ".$s->totalabserr_min." AND totalabserr <= ".$s->totalabserr_max;
	$delim="\nAND ";
}

if($s->rsd_min != "" and $s->rsd_max != ""){
	$q.=$delim."rsd >= ".$s->rsd_min." AND rsd <= ".$s->rsd_max;
	$delim="\nAND ";
}

//***** ICP Data****************************************************

if($s->rundate_min != "" and $s->rundate_max != ""){
	$q.=$delim."rundate >= '".$s->rundate_min."' AND rundate <= '".$s->rundate_max."'";
	$delim="\nAND ";
}elseif($s->rundate_min != ""){
	$q.=$delim."rundate >= '".$s->rundate_min."'";
	$delim="\nAND ";
}elseif($s->rundate_max != ""){
	$q.=$delim."rundate <= '".$s->rundate_max."'";
	$delim="\nAND ";
}


if($s->sm147sm149_mean_min != "" and $s->sm147sm149_mean_max != ""){
	$q.=$delim."sm147sm149_mean >= ".$s->sm147sm149_mean_min." AND sm147sm149_mean <= ".$s->sm147sm149_mean_max;
	$delim="\nAND ";
}

if($s->sm147sm149_sd_min != "" and $s->sm147sm149_sd_max != ""){
	$q.=$delim."sm147sm149_sd >= ".$s->sm147sm149_sd_min." AND sm147sm149_sd <= ".$s->sm147sm149_sd_max;
	$delim="\nAND ";
}

if($s->sm147sm149_rsd_min != "" and $s->sm147sm149_rsd_max != ""){
	$q.=$delim."sm147sm149_rsd >= ".$s->sm147sm149_rsd_min." AND sm147sm149_rsd <= ".$s->sm147sm149_rsd_max;
	$delim="\nAND ";
}

if($s->th230th232_mean_min != "" and $s->th230th232_mean_max != ""){
	$q.=$delim."th230th232_mean >= ".$s->th230th232_mean_min." AND th230th232_mean <= ".$s->th230th232_mean_max;
	$delim="\nAND ";
}

if($s->th230th232_sd_min != "" and $s->th230th232_sd_max != ""){
	$q.=$delim."th230th232_sd >= ".$s->th230th232_sd_min." AND th230th232_sd <= ".$s->th230th232_sd_max;
	$delim="\nAND ";
}

if($s->th230th232_rsd_min != "" and $s->th230th232_rsd_max != ""){
	$q.=$delim."th230th232_rsd >= ".$s->th230th232_rsd_min." AND th230th232_rsd <= ".$s->th230th232_rsd_max;
	$delim="\nAND ";
}

if($s->u235u238_mean_min != "" and $s->u235u238_mean_max != ""){
	$q.=$delim."u235u238_mean >= ".$s->u235u238_mean_min." AND u235u238_mean <= ".$s->u235u238_mean_max;
	$delim="\nAND ";
}

if($s->u235u238_sd_min != "" and $s->u235u238_sd_max != ""){
	$q.=$delim."u235u238_sd >= ".$s->u235u238_sd_min." AND u235u238_sd <= ".$s->u235u238_sd_max;
	$delim="\nAND ";
}

if($s->u235u238_rsd_min != "" and $s->u235u238_rsd_max != ""){
	$q.=$delim."u235u238_rsd >= ".$s->u235u238_rsd_min." AND u235u238_rsd <= ".$s->u235u238_rsd_max;
	$delim="\nAND ";
}

if($s->sm147_mean_min != "" and $s->sm147_mean_max != ""){
	$q.=$delim."sm147_mean >= ".$s->sm147_mean_min." AND sm147_mean <= ".$s->sm147_mean_max;
	$delim="\nAND ";
}

if($s->sm147_sd_min != "" and $s->sm147_sd_max != ""){
	$q.=$delim."sm147_sd >= ".$s->sm147_sd_min." AND sm147_sd <= ".$s->sm147_sd_max;
	$delim="\nAND ";
}

if($s->sm147_rsd_min != "" and $s->sm147_rsd_max != ""){
	$q.=$delim."sm147_rsd >= ".$s->sm147_rsd_min." AND sm147_rsd <= ".$s->sm147_rsd_max;
	$delim="\nAND ";
}

if($s->sm149_mean_min != "" and $s->sm149_mean_max != ""){
	$q.=$delim."sm149_mean >= ".$s->sm149_mean_min." AND sm149_mean <= ".$s->sm149_mean_max;
	$delim="\nAND ";
}

if($s->sm149_sd_min != "" and $s->sm149_sd_max != ""){
	$q.=$delim."sm149_sd >= ".$s->sm149_sd_min." AND sm149_sd <= ".$s->sm149_sd_max;
	$delim="\nAND ";
}

if($s->sm149_rsd_min != "" and $s->sm149_rsd_max != ""){
	$q.=$delim."sm149_rsd >= ".$s->sm149_rsd_min." AND sm149_rsd <= ".$s->sm149_rsd_max;
	$delim="\nAND ";
}

if($s->bkg220_mean_min != "" and $s->bkg220_mean_max != ""){
	$q.=$delim."bkg220_mean >= ".$s->bkg220_mean_min." AND bkg220_mean <= ".$s->bkg220_mean_max;
	$delim="\nAND ";
}

if($s->bkg220_sd_min != "" and $s->bkg220_sd_max != ""){
	$q.=$delim."bkg220_sd >= ".$s->bkg220_sd_min." AND bkg220_sd <= ".$s->bkg220_sd_max;
	$delim="\nAND ";
}

if($s->bkg220_rsd_min != "" and $s->bkg220_rsd_max != ""){
	$q.=$delim."bkg220_rsd >= ".$s->bkg220_rsd_min." AND bkg220_rsd <= ".$s->bkg220_rsd_max;
	$delim="\nAND ";
}

if($s->th230_mean_min != "" and $s->th230_mean_max != ""){
	$q.=$delim."th230_mean >= ".$s->th230_mean_min." AND th230_mean <= ".$s->th230_mean_max;
	$delim="\nAND ";
}

if($s->th230_sd_min != "" and $s->th230_sd_max != ""){
	$q.=$delim."th230_sd >= ".$s->th230_sd_min." AND th230_sd <= ".$s->th230_sd_max;
	$delim="\nAND ";
}

if($s->th230_rsd_min != "" and $s->th230_rsd_max != ""){
	$q.=$delim."th230_rsd >= ".$s->th230_rsd_min." AND th230_rsd <= ".$s->th230_rsd_max;
	$delim="\nAND ";
}

if($s->th232_mean_min != "" and $s->th232_mean_max != ""){
	$q.=$delim."th232_mean >= ".$s->th232_mean_min." AND th232_mean <= ".$s->th232_mean_max;
	$delim="\nAND ";
}

if($s->th232_sd_min != "" and $s->th232_sd_max != ""){
	$q.=$delim."th232_sd >= ".$s->th232_sd_min." AND th232_sd <= ".$s->th232_sd_max;
	$delim="\nAND ";
}

if($s->th232_rsd_min != "" and $s->th232_rsd_max != ""){
	$q.=$delim."th232_rsd >= ".$s->th232_rsd_min." AND th232_rsd <= ".$s->th232_rsd_max;
	$delim="\nAND ";
}

if($s->u234_mean_min != "" and $s->u234_mean_max != ""){
	$q.=$delim."u234_mean >= ".$s->u234_mean_min." AND u234_mean <= ".$s->u234_mean_max;
	$delim="\nAND ";
}

if($s->u234_sd_min != "" and $s->u234_sd_max != ""){
	$q.=$delim."u234_sd >= ".$s->u234_sd_min." AND u234_sd <= ".$s->u234_sd_max;
	$delim="\nAND ";
}

if($s->u234_rsd_min != "" and $s->u234_rsd_max != ""){
	$q.=$delim."u234_rsd >= ".$s->u234_rsd_min." AND u234_rsd <= ".$s->u234_rsd_max;
	$delim="\nAND ";
}

if($s->u235_mean_min != "" and $s->u235_mean_max != ""){
	$q.=$delim."u235_mean >= ".$s->u235_mean_min." AND u235_mean <= ".$s->u235_mean_max;
	$delim="\nAND ";
}

if($s->u235_sd_min != "" and $s->u235_sd_max != ""){
	$q.=$delim."u235_sd >= ".$s->u235_sd_min." AND u235_sd <= ".$s->u235_sd_max;
	$delim="\nAND ";
}

if($s->u235_rsd_min != "" and $s->u235_rsd_max != ""){
	$q.=$delim."u235_rsd >= ".$s->u235_rsd_min." AND u235_rsd <= ".$s->u235_rsd_max;
	$delim="\nAND ";
}

if($s->u238_mean_min != "" and $s->u238_mean_max != ""){
	$q.=$delim."u238_mean >= ".$s->u238_mean_min." AND u238_mean <= ".$s->u238_mean_max;
	$delim="\nAND ";
}

if($s->u238_sd_min != "" and $s->u238_sd_max != ""){
	$q.=$delim."u238_sd >= ".$s->u238_sd_min." AND u238_sd <= ".$s->u238_sd_max;
	$delim="\nAND ";
}

if($s->u238_rsd_min != "" and $s->u238_rsd_max != ""){
	$q.=$delim."u238_rsd >= ".$s->u238_rsd_min." AND u238_rsd <= ".$s->u238_rsd_max;
	$delim="\nAND ";
}


//******************************************************************************


if($s->agema_min != "" and $s->agema_max != ""){
	$q.=$delim."agema >= ".$s->agema_min." AND agema <= ".$s->agema_max;
	$delim="\nAND ";
}

if($s->errma_min != "" and $s->errma_max != ""){
	$q.=$delim."errma >= ".$s->errma_min." AND errma <= ".$s->errma_max;
	$delim="\nAND ";
}

if($s->errpct_min != "" and $s->errpct_max != ""){
	$q.=$delim."errpct >= ".$s->errpct_min." AND errpct <= ".$s->errpct_max;
	$delim="\nAND ";
}

if($s->uppm_min != "" and $s->uppm_max != ""){
	$q.=$delim."uppm >= ".$s->uppm_min." AND uppm <= ".$s->uppm_max;
	$delim="\nAND ";
}

if($s->thppm_min != "" and $s->thppm_max != ""){
	$q.=$delim."thppm >= ".$s->thppm_min." AND thppm <= ".$s->thppm_max;
	$delim="\nAND ";
}

if($s->sm147ppm_min != "" and $s->sm147ppm_max != ""){
	$q.=$delim."sm147ppm >= ".$s->sm147ppm_min." AND sm147ppm <= ".$s->sm147ppm_max;
	$delim="\nAND ";
}

if($s->ue_min != "" and $s->ue_max != ""){
	$q.=$delim."ue >= ".$s->ue_min." AND ue <= ".$s->ue_max;
	$delim="\nAND ";
}

if($s->thu_min != "" and $s->thu_max != ""){
	$q.=$delim."thu >= ".$s->thu_min." AND thu <= ".$s->thu_max;
	$delim="\nAND ";
}

if($s->henmolg_min != "" and $s->henmolg_max != ""){
	$q.=$delim."henmolg >= ".$s->henmolg_min." AND henmolg <= ".$s->henmolg_max;
	$delim="\nAND ";
}

if($s->ft_min != "" and $s->ft_max != ""){
	$q.=$delim."ft >= ".$s->ft_min." AND ft <= ".$s->ft_max;
	$delim="\nAND ";
}

if($s->spikeblkrunused !=""){
	$q.=$delim."lower(spikeblkrunused) like '%".strtolower($s->spikeblkrunused)."%'";
	$delim="\nAND ";
}

if($s->newnormalrunused !=""){
	$q.=$delim."lower(newnormalrunused) like '%".strtolower($s->newnormalrunused)."%'";
	$delim="\nAND ";
}



//*********************************************************************************

if($s->aliquotanalyst != ""){
	$q.=$delim."aliquot.analyst = ".$s->aliquotanalyst;
	$delim="\nAND ";
}

if($s->ftdataanalyst != ""){
	$q.=$delim."ftdata.analyst = ".$s->aliquotanalyst;
	$delim="\nAND ";
}

if($s->helinedataanalyst != ""){
	$q.=$delim."helinedata.analyst = ".$s->helinedataanalyst;
	$delim="\nAND ";
}

if($s->icpdataanalyst != ""){
	$q.=$delim."icpdata.analyst = ".$s->icpdataanalyst;
	$delim="\nAND ";
}

if($s->resultanalyst != ""){
	$q.=$delim."result.analyst = ".$s->resultanalyst;
	$delim="\nAND ";
}










//*********************************************************************************



if($q!=""){
	$countstring="select count(*) from aliquot
left join ftdata on aliquot.aliquot_pkey = ftdata.aliquot_pkey
left join helinedata on aliquot.aliquot_pkey = helinedata.aliquot_pkey
left join icpdata on aliquot.aliquot_pkey = icpdata.aliquot_pkey
left join result on aliquot.aliquot_pkey = result.aliquot_pkey
left join sample on aliquot.sample_pkey = sample.sample_pkey
left join project on sample.project_pkey = project.project_pkey
left join owner on project.owner_pkey = owner.owner_pkey
where $q";

	$querystring="select
aliquot.aliquot_pkey,
ownername,
samplename,
labsamplename,
aliquotname,
(select firstname||' '||lastname from users where user_pkey=aliquot.analyst) as analyst
from aliquot
left join ftdata on aliquot.aliquot_pkey = ftdata.aliquot_pkey
left join helinedata on aliquot.aliquot_pkey = helinedata.aliquot_pkey
left join icpdata on aliquot.aliquot_pkey = icpdata.aliquot_pkey
left join result on aliquot.aliquot_pkey = result.aliquot_pkey
left join sample on aliquot.sample_pkey = sample.sample_pkey
left join project on sample.project_pkey = project.project_pkey
left join owner on project.owner_pkey = owner.owner_pkey
where $q";

$doquery="yes";

}



//echo nl2br($querystring);










//echo nl2br($countstring);
//echo "<br><br>";
//secho nl2br($querystring);


?>