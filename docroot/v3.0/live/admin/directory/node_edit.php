<?php
require_once('../inx/global.inc.php'); 
require_once('../inx/dbtree.inc.php'); 
include("menu.php");
$db = new CDatabase("wsv3_test", "localhost", "wsv3_db_user", "CHe9adru+*=!a!uC7ubRad!TRu#raN");
$tree = new CDBTree($db,'category','cat_id');





if (!$_GET["action"]) {

$node_id = $_GET["node_id"];

$sql = "SELECT cat_title FROM category WHERE cat_id = $node_id LIMIT 1";
$result = mysql_query($sql); 
while ($row = mysql_fetch_array($result)) { 
	$node_title = $row["cat_title"];
	}
	
$formData1 = array( 
	'title'=>array(
		'type'=>'text',
		'label'=>'Title',
		'value'=>$node_title,
		'required'=>2,
		'attributes'=>array('class'=>'addr')
		)
	)
	;

$formData2 = array( 
	'new_id'=>array(
		'type'=>'select',
		'label'=>'Category',
		'value'=>$node_id,
		'options'=>display_tree_select(1,$node_id,'data'),
		'required'=>2,
		'attributes'=>array('class'=>'addr')
		)
	)
	;
	

// start new form object 
$form = new Form();

$form->addForm("form","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","edit");
$form->addField("hidden","id","",$node_id);

$form->addHtml("<fieldset>\n");
$form->addLegend('Edit Node');
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");

$form2 = new Form();
$form2->addForm("form","get",$PHP_SELF);
$form2->addHtml("<div id=\"standard_form\">\n");
$form2->addField("hidden","action","","move");
$form2->addField("hidden","id","",$node_id);

$form2->addHtml("<fieldset>\n");
$form2->addLegend('Move Node');
$form2->addData($formData2,$_GET);
$form2->addHtml($form2->addDiv($form2->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form2->addHtml("</fieldset>\n");
$form2->addHtml("</div>\n");



} elseif ($_GET["action"] == "edit") {

$id = $_GET["id"];
$title = trim(ucwords($_GET["title"]));
$sql = "UPDATE category SET cat_title = '$title' WHERE cat_id = $id";
$result = mysql_query($sql); 
header("Location:tree.php");

} elseif ($_GET["action"] == "move") {

$id = $_GET["id"];
$new_id = $_GET["new_id"];
$tree->moveAll($id, $new_id);
header("Location:tree.php");

} elseif ($_GET["action"] == "delete") {
// delete a node
// do not allow top levels to be deleted
	if (!$_GET["node_id"]) {
		echo "error";
		exit;
		}
	$tree->delete($_GET["node_id"]);
	header("Location:tree.php");
	}

// start a new page
$page = new HTML_Page2($page_defaults);
$page->setTitle("Edit Directory Node");
$page->addStyleSheet(GLOBAL_URL.'css/styles.css');
$page->addScript(GLOBAL_URL.'js/global.js');
$page->addBodyContent('<div id="content">');
$page->addBodyContent($menu);
$page->addBodyContent($form->renderForm());
$page->addBodyContent($form2->renderForm());
$page->addBodyContent('</div>');
$page->display();


?>