<?PHP
include("db.php");

$myrows=$db->get_results("select * from datasource 
							--where rownum<10 
							order by datasource_num");

foreach($myrows as $row){


echo "****************************************************************<br>";
echo $row->DATASOURCE_NUM."<br>";
echo $row->CITATION;
echo "<br><br><br>";

//echo $row->DATASOURCE_NUM."<br>";
}

?>