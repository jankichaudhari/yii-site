<?php
require_once("../inx/global.inc.php");
exit;
$action = 'UPDATE';
$table = 'sot';

$sql = "SELECT * 
FROM `changelog` 
WHERE `cha_datetime` = '2007-06-18 20:48:48'
AND `cha_action` = '$action'
AND `cha_table` = '$table'
";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {

	$sql2 = "UPDATE sot SET sotdate = ".$row["cha_old"]." WHERE sot_id = ".$row["cha_row"];
	$q2 = $db->query($sql2);
	}
?>