<?php
require_once("inx/global.inc.php");

/*
hidden page, for adding additional users to an appointment

*/

if (!$_GET["use_id"]) {
	echo "no use_id";
	exit;
	} else {
	$use_id = $_GET["use_id"];
	}
if (!$_GET["app_id"]) {
	echo "no app_id";
	exit;
	} else {
	$app_id = $_GET["app_id"];
	}
$carry = urlencode($_GET["carry"]);

// check the client isnt already associated with this appointment
$sql = "SELECT * FROM use2app WHERE u2a_use = '$use_id' and u2a_app = '$app_id'";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();	
if ($numRows == 0) {	
$db_data_add["u2a_use"] = $use_id;
$db_data_add["u2a_app"] = $app_id;
db_query($db_data_add,"INSERT","use2app","u2a_id");

unset($db_data);
$db_data["app_updated"] = date('Y-m-d H:i:s');
$db_response = db_query($db_data,"UPDATE","appointment","app_id",$app_id,true);
	
$db_response["array"]["add_attendee"] = array('new'=>"Attendee added");
notify($db_response,'edit');

}

header("Location:appointment_edit.php?app_id=$app_id&searchLink=$carry");	
?>