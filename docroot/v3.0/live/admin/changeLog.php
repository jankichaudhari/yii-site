<?php
require_once("inx/global.inc.php");

// view changeLog table

/*
by user
by session
by date

by table (with some JOINS where appropriate)
 appointment (cli2app,link_deal_to_appointment,con2app,use2app)
 area
 client (pro2cli)
 deal (link_client_to_instruction
 directory
 login
 media
 note
 offer (cli2off)
 pro2use
 property
 sot
 tel
 user
*/

// browse
if (!$_GET["scope"]) {

// activity in past n days
$days = 31;
$daterange = date(MYSQL_DATE_FORMAT,(strtotime($date_mysql)-($days*24*60*60)));
$sql = "SELECT use_id,COUNT(*) AS changes,
CONCAT( use_fname, ' ', use_sname ) AS User
FROM
changelog,user

WHERE
cha_datetime > '$daterange'
AND changelog.cha_user = user.use_id
GROUP BY cha_user
ORDER BY changes DESC";

$sql = "SELECT use_id,COUNT(*) AS changes,
CONCAT( use_fname, ' ', use_sname ) AS User
FROM
changelog,user

WHERE
changelog.cha_user = user.use_id
GROUP BY cha_user
ORDER BY changes DESC";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$render .= '
	<tr>
	<td><a href="?scope=user&use_id='.$row["use_id"].'">'.$row["User"].'</a> ('.$row["changes"].')</td>
	</tr>
	';
	}
$render = '
<h1>Activity</h1>
<table border="1">
'.$render.'
</table>';


} elseif ($_GET["scope"] == "user" && $_GET["use_id"]) {

$use_id = $_GET["use_id"];
// all user's changes
$days = 7;
$daterange = date(MYSQL_DATE_FORMAT,(strtotime($date_mysql)-($days*24*60*60)));
$sql = "SELECT *,
DATE_FORMAT(cha_datetime,'%d/%m/%y %H:%i') AS Date,
CONCAT( use_fname, ' ', use_sname ) AS User,
SUBSTRING( cha_field FROM 5 ) AS Changed,
cha_table,cha_row,
cha_action AS Action,
cha_old AS Old,
cha_new AS New,
cha_session AS Session
FROM
changelog
LEFT JOIN user ON cha_user = use_id
WHERE
cha_datetime > '$daterange' AND cha_user = $use_id
ORDER BY cha_datetime DESC";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$use_name = $row["User"];
	$render .= '
	<tr>
	<td>'.$row["Date"].'<br /><a href="?scope=session&cha_session='.$row["Session"].'">Track Session</a></td>
	<td>'.$row["User"].'</td>
	<td>'.$row["cha_table"].'</td>
	<td>'.$row["cha_row"].'</td>
	<td>'.$row["Action"].'</td>
	<td>'.$row["Changed"].'</td>
	<td><div class="wrap">'.$row["Old"].'</div></td>
	<td><div class="wrap">'.$row["New"].'</div></td>
	</tr>
	';
	}
$render = '
<h1>User ('.$use_name.') activity in past '.$days.' days ('.$numRows.' changes made)</h1>
<table border="1">
  <tr>
	<th>Date</th>
	<th>User</th>
	<th>Table</th>
	<th>Row</th>
	<th>Action</th>
	<th>Field</th>
	<th>Old Value</th>
	<th>New Value</th>
  </tr>
'.$render.'
</table>';




} elseif ($_GET["scope"] == "session" && $_GET["cha_session"]) {

$cha_session = $_GET["cha_session"];
// all user's changes
$days = 7;
$daterange = date(MYSQL_DATE_FORMAT,(strtotime($date_mysql)-($days*24*60*60)));
$sql = "SELECT *,
DATE_FORMAT(cha_datetime,'%d/%m/%y %H:%i') AS Date,
CONCAT( use_fname, ' ', use_sname ) AS User,
SUBSTRING( cha_field FROM 5 ) AS Changed,
cha_table,cha_row,
cha_action AS Action,
cha_old AS Old,
cha_new AS New,
cha_session AS Session
FROM
changelog
LEFT JOIN user ON cha_user = use_id
WHERE
cha_datetime > '$daterange' AND cha_session = '$cha_session'";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$use_name = $row["User"];
	$render .= '
	<tr>
	<td>'.$row["Date"].'<br /><a href="?scope=session&cha_session='.$row["Session"].'">Track Session</a></td>
	<td>'.$row["User"].'</td>
	<td>'.$row["cha_table"].'</td>
	<td>'.$row["cha_row"].'</td>
	<td>'.$row["Action"].'</td>
	<td>'.$row["Changed"].'</td>
	<td><div class="wrap">'.$row["Old"].'</div></td>
	<td><div class="wrap">'.$row["New"].'</div></td>
	</tr>
	';
	}
$render = '
<h1>Track Session activity in past '.$days.' days ('.$numRows.' changes made)</h1>
<table border="1">
  <tr>
	<th>Date</th>
	<th>User</th>
	<th>Table</th>
	<th>Row</th>
	<th>Action</th>
	<th>Field</th>
	<th>Old Value</th>
	<th>New Value</th>
  </tr>
'.$render.'
</table>';




} elseif ($_GET["scope"] == "deal" && $_GET["dea_id"]) {

// showing changes to deal table
$sql = "SELECT
dea_id,
DATE_FORMAT(cha_datetime,'%d/%m/%y %H:%i') AS Date,
CONCAT( use_fname, ' ', use_sname ) AS User,
CONCAT( pro_addr1, ' ', pro_addr2, ' ', pro_addr3, ' ', pro_postcode) AS Deal,
SUBSTRING( cha_field FROM 5 ) AS Changed,
cha_action AS Action,
cha_old AS Old,
cha_new AS New
FROM changelog
LEFT JOIN user ON cha_user = use_id
LEFT JOIN deal ON cha_row = dea_id
LEFT JOIN property ON deal.dea_prop = pro_id
WHERE cha_table = 'deal' AND cha_row = ".$_GET["dea_id"]."
ORDER BY cha_datetime DESC
";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$title = $row["Deal"];
	$render .= '
	<tr>
	<td>'.$row["Date"].'</td>
	<td>'.$row["User"].'</td>
	<td>'.$row["Action"].'</td>
	<td>'.$row["Changed"].'</td>
	<td><div class="wrap">'.$row["Old"].'</div></td>
	<td><div class="wrap">'.$row["New"].'</div></td>
	</tr>
	';
	}

$render = '
<h1>ChangeLog for: '.$title.'</h1>
<table border="1">
	<tr>
	<th>Date</th>
	<th>User</th>
	<th>Action</th>
	<th>Field</th>
	<th>Old Value</th>
	<th>New Value</th>
	</tr>
	'.$render.'
	</table>';


} elseif ($_GET["scope"] == "board" && $_GET["dea_id"]) {

// showing changes to deal table
$sql = "SELECT
dea_id,
DATE_FORMAT(cha_datetime,'%d/%m/%y %H:%i') AS Date,
CONCAT( use_fname, ' ', use_sname ) AS User,
CONCAT( pro_addr1, ' ', pro_addr2, ' ', pro_addr3, ' ', LEFT(pro_postcode,4)) AS Deal,
SUBSTRING( cha_field FROM 5 ) AS Changed,
cha_action AS Action,
cha_old AS Old,
cha_new AS New
FROM changelog
LEFT JOIN user ON cha_user = use_id
LEFT JOIN deal ON cha_row = dea_id
LEFT JOIN property ON deal.dea_prop = pro_id
WHERE cha_table = 'deal' AND cha_field = 'dea_board' AND cha_row = ".$_GET["dea_id"]."
ORDER BY cha_datetime DESC
";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$title = $row["Deal"];
	$render .= '
	<tr>
	<td>'.$row["Date"].'</td>
	<td>'.$row["User"].'</td>
	<td><div class="wrap" style="width:100px">'.$row["Old"].'</div></td>
	<td><div class="wrap" style="width:100px">'.$row["New"].'</div></td>
	</tr>
	';
	}

$render = '
<h1>Board Log for '.$title.'</h1>
<table border="1" width="100%">
	<tr>
	<th>Date</th>
	<th>User</th>
	<th>Old Value</th>
	<th>New Value</th>
	</tr>
	'.$render.'
	</table>';

	}


/* added 07/09/08 */
elseif ($_GET["scope"] == "contract" && $_GET["dea_id"]) {
$sql = "SELECT
dea_id,
DATE_FORMAT(cha_datetime,'%d/%m/%y %H:%i') AS Date,
CONCAT( use_fname, ' ', use_sname ) AS User,
CONCAT( pro_addr1, ' ', pro_addr2, ' ', pro_addr3, ' ', LEFT(pro_postcode,4)) AS Deal,
SUBSTRING( cha_field FROM 5 ) AS Changed,
cha_action AS Action,
cha_old AS Old,
cha_new AS New
FROM changelog
LEFT JOIN user ON cha_user = use_id
LEFT JOIN deal ON cha_row = dea_id
LEFT JOIN property ON deal.dea_prop = pro_id
WHERE cha_table = 'deal' AND cha_field = 'dea_contract' AND cha_row = ".$_GET["dea_id"]."
ORDER BY cha_datetime DESC
";

$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$title = ' for '.$row["Deal"];
	$render .= '
	<tr>
	<td>'.$row["Date"].'</td>
	<td>'.$row["User"].'</td>
	<td><div class="wrap" style="width:100px">'.$row["Old"].'</div></td>
	<td><div class="wrap" style="width:100px">'.$row["New"].'</div></td>
	</tr>
	';
	}

$render = '
<h1>Contract Log '.$title.'</h1>
<table border="1" width="100%">
	<tr>
	<th>Date</th>
	<th>User</th>
	<th>Old Value</th>
	<th>New Value</th>
	</tr>
	'.$render.'
	</table>';

	}

$page = new HTML_Page2($page_defaults);
$page->setTitle("ChangeLog");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
//$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="changelog">');
$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->display();
?>