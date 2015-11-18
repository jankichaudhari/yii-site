<?php
require_once("inx/global.inc.php");

// only accesible to SuperAdmin and Administrator
pageAccess($_SESSION["auth"]["roles"],array('SuperAdmin','Administrator'));

$page = new HTML_Page2($page_defaults);

$sql = "SELECT use_id,use_fname,use_sname FROM user";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	$users[$row["use_id"]] = $row["use_fname"].' '.$row["use_sname"];
	}

$formData1 = array(
	'use_id'=>array(
		'type'=>'select',
		'label'=>'User',
		'value'=>$use_id,
		'options'=>$users,
		'attributes'=>array('class'=>'addr'),
		'required'=>2
		),
	'use_password'=>array(
		'type'=>'text',
		'label'=>'Password',
		'value'=>$use_password,
		'attributes'=>array('class'=>'addr'),
		'required'=>2,
		'tooltip'=>'Passwords must be at least 8 characters, and contain at least one number and one UPPER CASE letter'
		)
	);

if (!$_GET["action"]) {



$form = new Form();

$form->addForm("","get",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","reset");
/////////////////////////////////////////////////////////////////////////////////

$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Reset Password</div>');
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");
$form->addHtml('<pre>');
for ($i=1; $i <= 5; ++$i) {
	$form->addHtml(random_string(16,'safe')."\n");
	}
$form->addHtml('</pre>');

$page->setTitle("Change Password");
$page->addStyleSheet(getDefaultCss());
$page->addScript(GLOBAL_URL.'js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();




} elseif ($_GET["action"] == "reset") {


$result = new Validate();
$results = $result->process($formData1,$_GET);
$db_data = $results['Results'];
if ($results['Errors']) {
	echo error_message($results['Errors']);
	exit;
	}

// take the use_id out of the array
$use_id = $db_data["use_id"];
unset($db_data["use_id"]);

// validate password strength
$password = $db_data["use_password"];


// get the user's salt, or make one if one does not exist
$sql = "SELECT use_id,use_salt FROM user WHERE use_id = ".$use_id;
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	if ($row["use_salt"] == "") {
		$salt = random_string(30);
		$sql_inner = "UPDATE user SET use_salt = '$salt' WHERE use_id = ".$row["use_id"]." LIMIT 1";
		$q_inner = $db->query($sql_inner);
		if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
		} else {
		$salt = $row["use_salt"];
		}
	}




$db_data["use_password"] =  encrypt_password($password,$salt);

// make sure this password has not been used before by this user
$sql = "SELECT * FROM changelog WHERE
	cha_table = 'user' AND
	cha_row = $use_id AND
	cha_field = 'use_password' AND
	(cha_old = '".$db_data["use_password"]."' OR cha_new = '".$db_data["use_password"]."')
	";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();
if ($numRows <> 0) {
	$errors[] = "This password has been used before, it is not possible to use the same password twice for this user";
	echo error_message($errors);
	exit;
	}

$cli_id = db_query($db_data,"UPDATE","user","use_id",$use_id);
header("Location:".$_SERVER['PHP_SELF']);
}
?>