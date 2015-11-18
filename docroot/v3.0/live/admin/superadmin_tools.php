<?php
require_once("inx/global.inc.php");

// only accesible to SuperAdmin and Administrator
pageAccess($_SESSION["auth"]["roles"],array('SuperAdmin','Administrator'));


// fork portal datafeed
if ($_GET["feed"] == "rightmove") {
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/rightmove.php > /dev/null &");
	header("Location:?");
	exit;
	}
// fork portal datafeed
if ($_GET["feed"] == "rightmove_lettings") {
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/rightmove_lettings.php > /dev/null &");
	header("Location:?");
	exit;
	}
// fork portal datafeed
if ($_GET["feed"] == "primelocation") {
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/primelocation.php > /dev/null &");
	header("Location:?");
	exit;
	}
// fork portal datafeed
if ($_GET["feed"] == "propertyfinder") {
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/propertyfinder.php > /dev/null &");
	header("Location:?");
	exit;
	}
// fork portal datafeed
if ($_GET["feed"] == "nethouseprices") {
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/nethouseprices.php > /dev/null &");
	header("Location:?");
	exit;
	}
// fork portal datafeed
if ($_GET["feed"] == "ezylet") {
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/ezylet_cam.php > /dev/null &");
	sleep(60);
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/ezylet_syd.php > /dev/null &");
	header("Location:?");
	exit;
	}
// fork portal datafeed
if ($_GET["feed"] == "oodle") {
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/oodle.php > /dev/null &");
	exit;
	}
// fork portal datafeed
if ($_GET["feed"] == "thinkproperty") {
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/thinkproperty.php > /dev/null &");
	exit;
	}
// fork portal datafeed
if ($_GET["feed"] == "online-lettings") {
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/online-lettings.php > /dev/null &");
	exit;
	}

// manually entered property (only include those relating to deals that do not have a location)
$sql = "SELECT pro_id,pro_pcid,CONCAT(pro_addr1,' ',pro_addr2,' ',pro_addr3,' ',pro_addr4,' ',pro_addr5,' ',pro_postcode) AS pro_addr,
pro_east,pro_north
FROM property, deal
WHERE property.pro_pcid = '-1' AND deal.dea_prop = pro_id AND
(dea_status = 'Valuation' OR dea_status = 'Instructed' OR dea_status = 'Production' OR dea_status = 'Proofing')
GROUP BY pro_id
ORDER BY pro_timestamp DESC";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
if ($q->numRows()) {
	while ($row = $q->fetchRow()) {
		if (strlen($row["pro_east"]) == 6 && strlen($row["pro_north"]) == 6) {
			$indicator = "+ ";
			} else {
			$indicator = '';
			}

		$manual_props .= '<tr>
		<td>'.$indicator.'<a href="postcode_tools.php?pro_id='.$row["pro_id"].'">'.$row["pro_addr"].'</a></td>
		</tr>
		';
		}
	}

$render .= '
<div class="home Right">
<h1>Manually Entered Properties</h1>
<div class="scrollDiv" id="manual_props">
<table>
'.$manual_props.'
</table>
</div>
</div>';


// bug reports
$sql = "SELECT *,
DATE_FORMAT(bug_date,'%d/%m/%y') AS date,
CONCAT(use_fname,' ',use_sname) AS use_name
FROM bug
LEFT JOIN user ON bug.bug_user = user.use_id
WHERE bug_status = 'Pending' OR bug_status = 'Accepted'
ORDER BY bug_date DESC";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
if ($q->numRows()) {
	while ($row = $q->fetchRow()) {

		if ($row["bug_type"] == 'Feature Request') {
			$feature_request .= '
			<tr bgcolor="white">
			<td><a href="bug.php?bug_id='.$row["bug_id"].'">'.$row["bug_status"].'</a>&nbsp;&nbsp;'.$row["date"].'&nbsp;&nbsp;'.$row["use_name"].'
			<br />'.$row["bug_blurb"].'</td>
			</tr>
			';
			} else {
			$bug_report .= '
			<tr bgcolor="white">
			<td><a href="bug.php?bug_id='.$row["bug_id"].'">'.$row["bug_status"].'</a>&nbsp;&nbsp;'.$row["date"].'&nbsp;&nbsp;'.$row["use_name"].'
			<br />'.$row["bug_blurb"].'</td>
			</tr>
			';
			}
		}
	}

$render .= '
<div class="home Left">
<h1>Bug Reports</h1>
<div class="scrollDiv" id="bug_report">
<table>
'.$bug_report.'
</table>
</div>

<h1>Feature Requests</h1>
<div class="scrollDiv" id="feature_request">
<table>
'.$feature_request.'
</table>
</div>
</div>';

//<p><a href="user_admin.php">User Management</a></p>
$render .= '
<p><a href="' . WS_YII_URL . 'User/">User Management</a></p>
<p><a href="reset_pw.php">Reset Password</a></p>
<p><a href="changeLog.php" target="_blank">ChangeLog</a></p>
<p><a href="tools/find_property_orphan.php">Orphaned property records</a></p>
<p><a href="ADS_stats.php">Advert Click Statistics</a></p>
<p><a href="' . WS_YII_URL . 'Career/">List Careers</a></p>
<p><a href="' . WS_YII_URL . 'Career/create">Add Career</a></p>';


$page = new HTML_Page2($page_defaults);
$page->setTitle("Home");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="home">');
$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->display();
exit;

?>