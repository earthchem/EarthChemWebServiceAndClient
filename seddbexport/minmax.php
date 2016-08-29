<?PHP
include("db.php");
header ("content-type: text/xml");

$mycount=$db->get_row("select count(*) as mycount from (select
			q_geoobject.agemin,
			q_geoobject.agemax,
			q_geoobject.systemid,
			q_geoobject.igsn,
			q_geoobject.longstart,
			q_geoobject.latstart,
			q_geoobject.locname
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
			where batch.material_num=618
			group by
			q_geoobject.agemin,
			q_geoobject.agemax,
			q_geoobject.systemid,
			q_geoobject.igsn,
			q_geoobject.longstart,
			q_geoobject.latstart,
			q_geoobject.locname)");

$minmax=$db->get_row("select 	min(q_geoobject.geoobject_num) as minnum, 
								max(q_geoobject.geoobject_num) as maxnum 
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
								where batch.material_num=618
								");

echo "<Results>";
echo "\t<Count>".$mycount->MYCOUNT."</Count>";
echo "\t<MinGeoobjectNum>".$minmax->MINNUM."</MinGeoobjectNum>";
echo "\t<MaxGeoobjectNum>".$minmax->MAXNUM."</MaxGeoobjectNum>";
echo "</Results>";
?>