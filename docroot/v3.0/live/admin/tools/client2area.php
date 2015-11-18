<?php
require_once("../inx/global.inc.php");

// converting old style pipe delimited values into link table

$sql = "SELECT cli_id,cli_area FROM client WHERE LENGTH(cli_area) > 1";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }

while ($row = $q->fetchRow()) {	

	$areas = explode("|",$row["cli_area"]);

	foreach($areas AS $area) {
		$sql_i = "INSERT INTO are2cli
		(a2c_cli,a2c_are)
		VALUES
		(".$row["cli_id"].",$area)";
		$q2 = $db->query($sql_i);
		}
	}


?>