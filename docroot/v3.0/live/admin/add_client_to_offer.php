<?php
require_once("inx/global.inc.php");

/*
hidden page, for adding additional clients to an offer
*/

if (!$_GET["off_id"]) {
	echo "no off_id v2d";
	exit;
	} else {
	$off_id = $_GET["off_id"];
	}
if (!$_GET["cli_id"]) {
	echo "no cli_id v2d";
	exit;
	} else {
	$cli_id = $_GET["cli_id"];
	}
$carry = $_GET["carry"];

// check the client isnt already associated with this appointment
$sql = "SELECT * FROM cli2off WHERE c2o_cli = '$cli_id' and c2o_off = '$off_id'";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();	
if ($numRows == 0) {	
$db_data_add["c2o_cli"] = $cli_id;
$db_data_add["c2o_off"] = $off_id;
db_query($db_data_add,"INSERT","cli2off","c2o_id");
}
header("Location:offer_edit.php?off_id=$off_id&searchLink=$carry");	
?>