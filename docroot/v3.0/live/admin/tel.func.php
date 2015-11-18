<?php
require_once("inx/global.inc.php");
// tel.func.php
// various tel related functions and actions

$tel_id = $_GET["tel_id"];

if ($_GET["action"] =="delete" && $tel_id) {
	
	// get all current info
	$sql = "SELECT * FROM tel WHERE tel_id = $tel_id";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		$tel_cli = $row["tel_cli"];
		$tel_con = $row["tel_con"];
		$tel_com = $row["tel_com"];		
		}		
	
	// delete current number
	$sql = "DELETE FROM tel WHERE tel_id = $tel_id";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	
	// now re-order remaining numbers
	// re-number the order of all remaining deals in this appointment
	if ($tel_cli > 0) {
		$sql = " tel_cli = $tel_cli";
		}
	elseif ($tel_con > 0) {
		$sql = " tel_con = $tel_con";
		}
	elseif ($tel_com > 0) {
		$sql = " tel_com = $tel_com";
		}
	$sql = "SELECT * FROM tel WHERE $sql ORDER BY tel_ord ASC";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	$count = 1;
	while ($row = $q->fetchRow()) {
		$sql2 = "UPDATE tel SET tel_ord = $count WHERE tel_id = ".$row["tel_id"];
		$q2 = $db->query($sql2);
		$count++;
		}		
	
	header("Location:".$_SERVER['HTTP_REFERER']);
	exit;
	}

if ($_GET["action"] == "reorder") {

	$tel_id = $_GET["tel_id"];
	$cur = $_GET["cur"]; // current position 
	$new = $_GET["new"]; // new position (position to move the deal to, we need to update this position with the postiion it replaces)
	
	
	// get cli_id
	$sql = "SELECT tel_cli,tel_con,tel_com FROM tel WHERE tel_id = $tel_id";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		$tel_cli = $row["tel_cli"];
		$tel_con = $row["tel_con"];
		$tel_com = $row["tel_com"];		
		}
	
	
	if ($tel_cli > 0) {
		$sql = " tel_cli = $tel_cli";
		}
	elseif ($tel_con > 0) {
		$sql = " tel_con = $tel_con";
		}
	elseif ($tel_com > 0) {
		$sql = " tel_com = $tel_com";
		}
	// get id of deal in position we want to move our deal to
	$sql = "SELECT * FROM tel WHERE $sql AND tel_ord = $new";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		$other_id = $row["tel_id"];
		$other_order = $row["tel_ord"];
		}
		
	// update this row with new position
	$db_data["tel_ord"] = $new;
	db_query($db_data,"UPDATE","tel","tel_id",$tel_id);
	unset($db_data);
	
	// update other row with new position
	$db_data["tel_ord"] = $cur;
	db_query($db_data,"UPDATE","tel","tel_id",$other_id);
	unset($db_data);	
	
	header("Location:".$_SERVER['HTTP_REFERER']);
	exit;
	}


/*
$sql = "SELECT * FROM client";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	
	$db_data["tel_cli"] = $row["cli_id"];
	$db_data["tel_number"] = $row["cli_tel1"];
	$db_data["tel_type"] = $row["cli_tel1type"];
	$db_data["tel_ord"] = 1;
	db_query($db_data,"INSERT","tel","tel_id");
	unset($db_data);
	
	if ($row["cli_tel2"]) {
		$db_data["tel_cli"] = $row["cli_id"];
		$db_data["tel_number"] = $row["cli_tel2"];
		$db_data["tel_type"] = $row["cli_tel2type"];
		$db_data["tel_ord"] = 2;
		db_query($db_data,"INSERT","tel","tel_id");
		unset($db_data);
		}
	if ($row["cli_tel3"]) {
		$db_data["tel_cli"] = $row["cli_id"];
		$db_data["tel_number"] = $row["cli_tel3"];
		$db_data["tel_type"] = $row["cli_tel3type"];
		$db_data["tel_ord"] = 3;
		db_query($db_data,"INSERT","tel","tel_id");
		unset($db_data);
		}
	}
*/
?>