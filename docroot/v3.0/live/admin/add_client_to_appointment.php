<?php
require_once("inx/global.inc.php");

/*
hidden page, for adding additional clients to an appointment
todo: accept array of clients
*/

if (!$_GET["cli_id"]) {
	echo "no cli_id v2d";
	exit;
	} else {
	$cli_id = $_GET["cli_id"];
	}
if (!$_GET["app_id"]) {
	echo "no app_id v2d";
	exit;
	} else {
	$app_id = $_GET["app_id"];
	}
$carry = $_GET["carry"];

// check the client isnt already associated with this appointment
$sql = "SELECT * FROM cli2app WHERE c2a_cli = '$cli_id' and c2a_app = '$app_id'";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();	
if ($numRows == 0) {	
$db_data_add["c2a_cli"] = $cli_id;
$db_data_add["c2a_app"] = $app_id;
db_query($db_data_add,"INSERT","cli2app","c2a_id");

unset($db_data);
$db_data["app_updated"] = date('Y-m-d H:i:s');
$db_response = db_query($db_data,"UPDATE","appointment","app_id",$app_id,true);
	
$db_response["array"]["add_client"] = array('new'=>"Additional client added");
notify($db_response,'edit');

}

	
	


header("Location:appointment_edit.php?app_id=$app_id&searchLink=$carry");	
?>