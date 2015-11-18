<?php
require_once("inx/global.inc.php");

// force download of document without revelaing location
// only works with pdf

if (!$_GET["doc_id"]) {
	echo error_message(array('No file selected'));
	exit;
	} else {
	$doc_id = $_GET["doc_id"];
	}


$sql = "SELECT * FROM document WHERE doc_id = $doc_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {		
	$filename = $row["doc_file"];
	}
	
// quickfix for sydenham sales peeps
if ($_SESSION["auth"]["use_branch"] == 2) {
	$filename = str_replace("_cam.pdf","_syd.pdf",$filename);
	}

$filepath = GLOBAL_PATH.'comp_docs/'.$filename;

header('Cache-Control: maxage=3600'); //Adjust maxage appropriately
header('Pragma: public');
header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filepath);


?>