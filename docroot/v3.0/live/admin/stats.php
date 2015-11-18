<?php
require_once("inx/global.inc.php");
/*
stats

viewings per neg & branch total week by week

*/


$render .= "<h1>Basic Stats - System was launched 18th June 2007</h1>\n";

$sql = "SELECT COUNT( * )
FROM `client` WHERE cli_sales = 'Yes' OR cli_lettings = 'Yes'";
$count = $db->getOne($sql);
$render .= "
<h2>Live Clients: $count</h2>
";

$sql = "SELECT COUNT( * ) AS `Rows` , `cli_method`
FROM `client`
GROUP BY `cli_method`
ORDER BY `Rows` DESC ";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {

	$source_description = array(
		'Unspecified'=>'Either was not entered or unknown',
		'Telephone'=>'Client has phoned in',
		'Email'=>'An email lead, from a portal or direct email',
		'Website'=>'Client self-registered via our website',
		'Internet'=>'Lead from search engine, or perhaps portal',
		'Walk-in'=>'Walk-in to one of the branches',
		'Import'=>'Imported from old system (admin and fig)')
		;

	while ($row = $q->fetchRow()) {
		if (!$row["cli_method"]) {
			$row["cli_method"] = "Unspecified";
			}
		$method .= "<tr><td>$row[cli_method]</td><td>$row[Rows]</td><td>".$source_description[$row["cli_method"]]."</td></tr>\n";
		}
	}


$render .= "
<h2>Client Contact Method</h2>
<table>$method</table>
";








$sql = "SELECT COUNT( * ) AS `Rows` , concat(s1.sou_title,' (',s2.sou_title,')') AS sou_title
FROM `client`,source AS s1,source as s2
where s1.sou_type = s2.sou_id and cli_source = s1.sou_id
GROUP BY sou_title
ORDER BY  `Rows` DESC  ";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		if (!$row["cli_method"]) {
			$row["cli_method"] = "Unspecified";
			}
		$source .= "<tr><td>$row[sou_title]&nbsp;</td><td>$row[Rows]</td></tr>\n";
		}
	}

$render .= "
<h2>Client Lead Source</h2>
<table>$source</table>
";




$render .= "<hr>";



$sql = "SELECT COUNT( * ) AS `Rows` , `app_type`
FROM `appointment`
WHERE app_status != 'Deleted'
GROUP BY `app_type`
ORDER BY `app_type` ";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		$apps .= "<tr><td>$row[app_type]</td><td>$row[Rows]</td></tr>\n";
		}
	}
$render .= "
<h2>Appointments</h2>
<table>$apps</table>
";


$sql = "SELECT COUNT( * ) AS `Rows` , `hit_action`
FROM `hit`
GROUP BY `hit_action` ";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		$hits .= "<tr><td>$row[hit_action]</td><td>$row[Rows]</td></tr>\n";
		}
	}

$sql = "SELECT COUNT( * )
FROM `mailshot`";
$count = $db->getOne($sql);

$render .= "
<h2>Hits from Mailshots ($count mailshots sent)</h2>
<table>$hits</table>
";




$sql = "SELECT
COUNT(*) AS `Rows`,
CONCAT(use_fname,' ',use_sname) as use_name

FROM changelog

LEFT JOIN user ON changelog.cha_user = user.use_id
GROUP BY `cha_user`
ORDER BY `Rows`  DESC";

$sql = "SELECT
COUNT(*) AS `Rows`,
CONCAT(use_fname,' ',use_sname) as use_name

FROM changelog, user

WHERE changelog.cha_user = user.use_id
GROUP BY `cha_user`
ORDER BY `Rows`  DESC";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage().$sql); }
$numRows = $q->numRows();
if ($numRows !== 0) {
	while ($row = $q->fetchRow()) {
		$changes .= "<tr><td>$row[use_name]</td><td>$row[Rows]</td></tr>\n";
		}
	}


$render .= "
<h2>Activity (total number of database changes made)</h2>
<table>$changes</table>
";














$css = '

#content table, #content tr, #content td {
	margin:0; padding:0;
	}
#content td {
	margin:0; padding:5px;
	border-bottom:1px solid black;
	}
#content h1 {
	margin:10px 0 5px 0;
	font-size:20px;
	color:#666666;
	}
#content h2 {
	margin:20px 0 5px 0;
	font-size:16px;
	color:#999999;
	}
#content hr {
	margin:20px 0 20px 0;
	}
';
$page = new HTML_Page2($page_defaults);
$page->setTitle("Stats");
$page->addStyleSheet(getDefaultCss());
$page->addStyleDeclaration($css);
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->display();

?>