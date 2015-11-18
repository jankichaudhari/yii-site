<?php
require_once("inx/global.inc.php");

// choose a colour for a user. must not allow the same colour to be chosen for more than one user.

$sql = "SELECT
col_colour
FROM
colour
WHERE col_colour NOT IN ( SELECT use_colour FROM user )
ORDER BY col_id";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
while ($row = $q->fetchRow()) {
	$render .= '
	<span style="width:60px; height:60px; margin:2px 2px 2px 2px; background-color: #'.$row["col_colour"].'; border: 1px solid #666666;"><a href="javascript:pick(\''.$row["col_colour"].'\');"><img src="/images/sys/admin/blank.gif" width="60" height="60" border="0"></a></span>
	';

	}


// start a new page
$page = new HTML_Page2($page_defaults);
$page->setTitle("Colour Picker");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addScriptDeclaration('function pick(color) {
  if (window.opener && !window.opener.closed)
    window.opener.document.forms[0].use_colour.value = color;
	window.opener.document.forms[0].use_colour.style.background = color;
	window.opener.document.forms[0].use_colour.style.color = color;
  window.close();
}');
$page->addBodyContent($render);
$page->display();

exit;

?>