<?php 
require_once('../inx/global.inc.php'); 
require_once('../inx/dbtree.inc.php'); 
include("menu.php");
$db = new CDatabase("wsv3_test", "localhost", "wsv3_db_user", "CHe9adru+*=!a!uC7ubRad!TRu#raN");

$tree = new CDBTree($db,'category','cat_id');




	
	
// add a node 
// need to make sure no duplicates are created
if ($_GET["action"] == "add_node") {
	if (!$_GET["node_id"] || !$_GET["node_title"]) {
		echo "error";
		exit;
		}
	$tree->insert($_GET["node_id"], array('cat_title'=>$_GET["node_title"]));
	header("Location:?");
	}

/*
// delete a node
// do not allow top levels to be deleted
if ($_GET["action"] == "delete_node") {
	if (!$_GET["node_id"]) {
		echo "error";
		exit;
		}
	$tree->delete($_GET["node_id"]);
	header("Location:?");
	}
*/



$sql = 'SELECT cat_id,cat_title, cat_left, cat_right, cat_level 
FROM category 
WHERE cat_id != 1
ORDER BY cat_level, cat_title';
$result = mysql_query($sql); 

while ($row = mysql_fetch_array($result)) { 
	// create treeview
	$parent_array = mysql_fetch_assoc($tree->getParent($row['cat_id']));
	$parent = $parent_array["cat_id"];
	#$render_node .= '<option value="'.$row['cat_id'].'">'.$row['cat_title'].'</option>';
	
	// nodes with children are not selectable
	if (mysql_fetch_assoc($tree->enumChildrenAll($row['cat_id']))) {
		$js_tree .= "d.add(".$row['cat_id'].",".($parent).",'".$row['cat_title']."');\n";
		} else {
		$js_tree .= "d.add(".$row['cat_id'].",".($parent).",'".$row['cat_title']."','node_edit.php?node_id=".$row['cat_id']."');\n";
		}

	}

$formData1 = array( 
	'node_id'=>array(
		'type'=>'select',
		'label'=>'Category',
		'value'=>$node_id,
		'options'=>display_tree_select(1,$node_id,'data'),
		'required'=>2,
		'attributes'=>array('class'=>'addr')
		),
	'node_title'=>array(
		'type'=>'text',
		'label'=>'Title',
		'required'=>2,
		'attributes'=>array('class'=>'addr')
		)		
	)
	;

// start new form object 
$form = new Form();

$form->addForm("form","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","add_node");
$form->addField("hidden","id","",$node_id);

$form->addHtml("<fieldset>\n");
$form->addLegend('Add Node');
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");



#<p><a href="javascript: d.openAll();">open all</a> | <a href="javascript: d.closeAll();">close all</a></p>
$render = '
<script type="text/javascript">
		<!--

		d = new dTree(\'d\');
		d.config.closeSameLevel = true;
		d.config.useIcons = false;
		d.config.useLines = false;
		d.config.useCookies = false;

		d.add(1,-1,\'Directory\');
		'.$js_tree.'

		document.write(d);

		//-->
	</script>
';

$form->addHtml("<fieldset>\n");
$form->addLegend('Browse Tree');

$form->addHtml('<div id="inset">'.$render.'</div>');
$form->addHtml("</fieldset>\n");
$form->addHtml("</div>\n");

// start a new page
$page = new HTML_Page2($page_defaults);
$page->setTitle("Edit Directory Node");
$page->addStyleSheet(GLOBAL_URL.'css/styles.css');
$page->addScript(GLOBAL_URL.'js/global.js');
$page->addStyleSheet('dtree.css');
$page->addScript('dtree.js');
$page->addBodyContent('<div id="content">');
$page->addBodyContent($menu);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();
?>