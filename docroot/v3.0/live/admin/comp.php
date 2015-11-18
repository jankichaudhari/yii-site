<?php

session_start();
$pageTitle = "Notes";

require_once("inx/global.inc.php");

$sql = "SELECT * FROM ca
LEFT JOIN comp ON ca.ca_compcode = comp.comp_code
LEFT JOIN cq ON ca.ca_question = cq.cq_id
WHERE ca_client_email != ''
";

$q = $db->query($sql);
if (DB::isError($q)) {  die("error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
	$render .= '
	<tr>
	<td>'.$row["ca_datetime"].'</td>
	<td>'.$row["comp_title"].'</td>
	<td>'.$row["ca_client_name"].' '.$row["ca_client_email"].'</td>
	<td>'.$row["cq_question"].'</td>
	<td><font color=gray>'.$row["cq_answer"].'</font></td>
	<td>'.$row["ca_answer"].'</td>
	<td><input type="checkbox" name="correct[]" value="'.$row["cq_id"].'"></td>
	</tr>';
	}

echo '
	<table border="1" cellspacing="0">
	<tr>
	<td>Date</td>
	<td>Comp</td>
	<td>Client</td>
	<td>Question</td>
	<td>Required Answer</td>
	<td>Given Answer</td>
	<td>Correct?</td>
	</tr>
	'.$render.'
	</table>
	';
?>