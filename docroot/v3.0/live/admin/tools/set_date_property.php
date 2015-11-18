<?php
require_once("../inx/global.inc.php");

//$date = '2007-04-26 00:00:00';
//$db_data["sot_date"] = $date;
$sql = "SELECT *
FROM deal
LEFT JOIN property ON deal.dea_prop = property.pro_id
LEFT JOIN sot ON sot.sot_deal = dea_id
WHERE dea_status != 'Archived'";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
$count++;
	echo $row["dea_id"]." ".$row["pro_addr1"]." ".$row["pro_addr3"]." ".$row["pro_postcode"]."<br>
	sot: ".$row["sot_status"]." / ".$row["sot_date"]."<br>
	launch: ".$row["dea_launchdate"]."
	"
	;
	/*
	if (!$row["sot_status"]) {
		$db_data["sot_status"] = $row["dea_status"];
		$db_data["sot_date"] = $date;
		$db_data["sot_user"] = 1;
		$db_data["sot_deal"] = $row["dea_id"];
		db_query($db_data,"INSERT","sot","sot_id");
		unset($db_data);
		}
	*/
	//$db_data["dea_launchdate"] = $row["sot_date"];
	//print_r($db_data);
	//db_query($db_data,"UPDATE","deal","dea_id",$row["dea_id"]);
	// insert available in sot table as there have none
	unset($db_data);
	echo "<hr>";
	}

echo $count;


?>