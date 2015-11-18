<?php
require_once("inx/global.inc.php");

// fork portal datafeed
if ($_GET["feed"] == "rightmove") {
	exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/feed/rightmove.php > /dev/null &");
	header("Location:?");
	exit;
}

// get all documents
$sql = "SELECT document.*, CONCAT(use_fname,' ',use_sname) AS usename FROM document LEFT JOIN user ON doc_user = use_id ORDER BY doc_ord";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
if ($numRows) {
	$documents = '
	<h1>Documents</h1>
	<div class="scrollDiv" id="documents" style="height:400px">
	<table>
	';
	// onClick="javascript:toggleDiv(\'documents\');"

	while ($row = $q->fetchRow()) {
		$documents .= '
		<tr class="trOff" height="20">
		<td><a href="document.php?doc_id=' . $row["doc_id"] . '" target="_blank">' . $row["doc_title"] . '</a><br>' . $row["doc_blurb"] . '</td>
		</tr>
		';
	}
	$documents .= '</table>
	</div>';
}

// get all links
$sql = "SELECT * FROM link";
$q   = $db->query($sql);
if (DB::isError($q)) {
	die("db error: " . $q->getMessage());
}
$numRows = $q->numRows();
if ($numRows) {
	$links = '
	<h1>Links</h1>
	<div class="scrollDiv" id="links" style="height:200px">
	<table>
	';
	// onClick="javascript:toggleDiv(\'documents\');"

	while ($row = $q->fetchRow()) {
		$links .= '
		<tr class="trOff" height="20">
		<td><a href="' . $row["lin_uri"] . '" target="' . $row["lin_target"] . '">' . $row["lin_title"] . '</a>';
		if ($row["lin_blurb"]) {
			$links .= '<br />' . $row["lin_blurb"];
		}
		$links .= '</td>
		</tr>
		';
	}
	$links .= '</table>
	</div>';
}
$tools = '
<h1>Tools</h1>
<div class="scrollDiv" id="links" style="height:200px">
<table>
  <tr class="trOff" height="20">
	<td><a href="' . WS_YII_URL . 'User/UserPreferences">User Preferences</a></td>
  </tr>
  <tr class="trOff" height="20">
	<td><a href="' . WS_YII_URL . 'user/staff">Staff List</a></td>
  </tr>
  <tr class="trOff" height="20">
	<td><a href="bug.php">Give me your Feedback!</a></td>
  </tr>
  <tr class="trOff" height="20">
	<td><a href="board.php">Board Management</a></td>
  </tr>
   <tr class="trOff" height="20">
	<td><a href="' . WS_YII_URL . 'transportStations">Map Control Panel</a></td>
  </tr>
  <tr class="trOff" height="20">
	<td><a href="' . WS_YII_URL . 'file/PageGalleryImage">Contact page Gallery</a></td>
  </tr>
  <tr class="trOff" height="20">
	<td><a href="' . WS_YII_URL . 'instructionVideo/manageVideo">Manage Videos Sequence</a></td>
  </tr>
    <tr class="trOff" height="20">
      	<td><a href="' . WS_YII_URL . 'QuickReport/list">Quick reports</a></td>
	</tr>
</table>
</div>';

$page = new HTML_Page2($page_defaults);
$page->setTitle("Tools");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="home">');
$page->addBodyContent('<div class="home Right">' . $documents . '</div>');
$page->addBodyContent('<div class="home Left">' . $tools . $links . '</div>');
$page->addBodyContent('</div>');
$page->display();
exit;
?>