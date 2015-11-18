<?php
require_once("inx/global.inc.php");

/*
hidden page, for adding additional vendors (clients) to a deal
todo: accept array of clients
*/

if (!$_GET["cli_id"]) {
	echo "no cli_id v2d";
	exit;
	} else {
	$cli_id = $_GET["cli_id"];
	}
if (!$_GET["dea_id"]) {
	echo "no dea_id v2d";
	exit;
	} else {
	$dea_id = $_GET["dea_id"];
	}
$carry = $_GET["carry"];

// check the vendor isnt already associated with this deal
$sql = "SELECT * FROM link_client_to_instruction WHERE clientId = '$cli_id' and dealId = '$dea_id'";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows == 0) {
$db_data_add["clientId"] = $cli_id;
$db_data_add["dealId"] = $dea_id;
$db_data_add["capacity"] = 'Tenant';
db_query($db_data_add,"INSERT","link_client_to_instruction","id");
}
header("Location:/admin4/instruction/summary/id/$dea_id");
?>