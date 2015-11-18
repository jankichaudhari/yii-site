<?php
// retireves data (currently only property) from changelog table
require_once("../inx/global.inc.php");

$action = 'INSERT';
$table = 'property';

$sql = "SELECT * 
FROM `changelog` 
WHERE `cha_datetime` > '2007-04-25 18:01:55'
AND `cha_action` = '$action'
AND `cha_table` = '$table'
";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
	
	// check if property record exists
	$sql2 = "SELECT pro_id FROM property WHERE pro_id = ".$row["cha_row"];	
	$present = $db->getOne($sql2);
	
	// if the row isnt present, insert it
	if ($present == 0) {
		$sql3 = "INSERT INTO `property` ( `pro_id` , `pro_status`  ) 
		VALUES ('".$row["cha_row"]."', 'Current') ";
		echo $sql3."<hr>\n";
		$q3 = $db->query($sql3);
		} 
	
	// now update that row with changelog new value
	$sql4 = "UPDATE property SET
	".$row["cha_field"]." = '".$row["cha_new"]."'
	WHERE pro_id = ".$row["cha_row"];
	echo $sql4."<hr>\n";
	$q4 = $db->query($sql4);
	
	}
?>