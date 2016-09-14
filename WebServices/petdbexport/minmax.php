<?PHP
include("db.php");
header ("content-type: text/xml");


$minmaxQry ="select min(sample_num) as minnum, max(sample_num) as maxnum from sample where sample_num not in
(select distinct(b.sample_num) from BATCH b, TABLE_IN_REF tif, REFERENCE r where b.TABLE_IN_REF_NUM = tif.TABLE_IN_REF_NUM and r.REF_NUM = tif.REF_NUM and r.status='COMPLETED' and b.sample_num not in
                               (
                               select unique b.sample_num
                                from BATCH b, TABLE_IN_REF tif, REFERENCE r 
                               where b.TABLE_IN_REF_NUM = tif.TABLE_IN_REF_NUM and r.REF_NUM = tif.REF_NUM and r.status='COMPLETED'
                               
                               )
)
";
$minmax=$db->get_row($minmaxQry);

echo "<Results>";
echo "\t<MinSampleNum>".$minmax->MINNUM."</MinSampleNum>";
echo "\t<MaxSampleNum>".$minmax->MAXNUM."</MaxSampleNum>";
echo "</Results>";
?>
