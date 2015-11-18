<?php

require_once("../inx/global.inc.php");

header("Location:https://www.woosterstock.co.uk/v3/admin/directory/list.php");
exit;
include("menu.php");
// start a new page
$page = new HTML_Page2($page_defaults);

$render = '
<p><a href="add.php">Add Directory Entry</a></p>
<p><a href="list.php">List Directory Entries</a></p>
<p><a href="tree.php">Manage the Directory Category Tree</a></p>
';




$page->setTitle("Directory > Add");
$page->addStyleSheet(GLOBAL_URL.'css/styles.css');
$page->addScript(GLOBAL_URL.'js/global.js');
$page->addScript(GLOBAL_URL.'js/scriptaculous/prototype.js');
$page->addScriptDeclaration($source['js']);
$page->setBodyAttributes(array('onLoad'=>$source['onload']));
$page->addBodyContent('<div id="content">');
$page->addBodyContent($menu);
$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->display();

exit;
?>