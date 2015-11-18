<?php
require_once("inx/global.inc.php");
//print_r($_GET);


// send the messages
if ($_GET["action"] == "send") {

	if (!$_GET["cli_id"]) {
		$errors[] = 'Please select at least one recipient';
		}
	if (!$_GET["subject"]) {
		$errors[] = 'Subject is required';
		}
	if (!$_GET["message"]) {
		$errors[] = 'Message is required';
		}
	if ($errors) {
		echo error_message($errors);
		exit;
		}

	if (is_array($_GET["cli_id"])) {
		foreach ($_GET["cli_id"] AS $cli_id) {
			$sql = "SELECT CONCAT(cli_fname,' ',cli_sname) AS cli_name,cli_email FROM client WHERE cli_id = $cli_id";
			$row = $db->getRow($sql);
			if ($row["cli_email"]) {
				$recipients[$row["cli_email"]] = $row["cli_name"];
				}
			}
		} else {
		$sql = "SELECT CONCAT(cli_fname,' ',cli_sname) AS cli_name,cli_email FROM client WHERE cli_id = ".$_GET["cli_id"];
		$row = $db->getRow($sql);
		if ($row["cli_email"]) {
			$recipients[$row["cli_email"]] = $row["cli_name"];
			}
		}



	$hdrs = 'From: '.$_SESSION["auth"]["use_email"]."\r\n";

	foreach($recipients as $cli_email=>$cli_name) {
		$message = "Dear $cli_name\n\n".$_GET["message"];
		// send to me for now
		$to = 'markdw@hotmail.com'; // $cli_email
		mail($to,$_GET["subject"],$message,$hdrs);
		}
	header("Location:".urldecode($_GET["return"]));
	exit;
	}

else {

if (!$_GET["cli_id"]) die("No cli_id");

if ($_GET["cli_id"]) {
	if (is_array($_GET["cli_id"])) {
		foreach ($_GET["cli_id"] AS $cli_id) {
			$sql = "SELECT CONCAT(cli_fname,' ',cli_sname) AS cli_name,cli_email FROM client WHERE cli_id = $cli_id";
			$row = $db->getRow($sql);
			if ($row["cli_email"]) {
				$render_client .= '<label for="'.$cli_id.'"><input type="checkbox" name="cli_id[]" value="'.$cli_id.'" id="'.$cli_id.'" checked="checked">'.$row["cli_name"].' ('.$row["cli_email"].')</label><br />';
				$hidden_field .= '<input type="hidden" name="cli_id[]" value="'.$cli_id.'">';
				$recipients[$row["cli_email"]] = $row["cli_name"];
				}
			}
		} else {
		$sql = "SELECT CONCAT(cli_fname,' ',cli_sname) AS cli_name,cli_email FROM client WHERE cli_id = ".$_GET["cli_id"];
		$row = $db->getRow($sql);
		if ($row["cli_email"]) {
			$render_client .= '<label for="'.$cli_id.'"><input type="checkbox" name="cli_id[]" value="'.$cli_id.'" id="'.$cli_id.'" checked="checked">'.$row["cli_name"].' ('.$row["cli_email"].')</label><br />';
			$hidden_field .= '<input type="hidden" name="cli_id[]" value="'.$cli_id.'">';
			$recipients[$row["cli_email"]] = $row["cli_name"];
			}
		}

	$render_client = '<table><tr><td>'.$render_client.'</td></tr></table>';
	}


$formData1 = array(
	'subject'=>array(
		'type'=>'text',
		'label'=>'Subject',
		'value'=>$subject,
		'required'=>2,
		'attributes'=>array('style'=>'width:400px;')
		),
	'message'=>array(
		'type'=>'textarea',
		'label'=>'Message',
		'value'=>$message,
		'required'=>2,
		'attributes'=>array('style'=>'width:400px;height:100px')
		)
	);


$form = new Form();

$form->addForm("","GET",$PHP_SELF);
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","send");
$form->addField("hidden","return","",($_GET["return"]));
//$form->addHtml($hidden_field);


$formName = 'form1';
$form->addHtml("<fieldset>\n");
$form->addHtml('<div class="block-header">Send Message</div>');
$form->addHtml('<div id="'.$formName.'">');
$form->addHtml($form->addLabel('','Recipient(s)',$render_client));

$form->addData($formData1,$_GET);
$buttons = $form->makeField("submit","","","  Send  ",array('class'=>'submit'));
$buttons .= $form->makeField("button","","","Cancel",array('class'=>'button','onClick'=>'document.location.href=\''.urldecode($_GET["return"]).'\';'));
$form->addHtml($form->addDiv($buttons));
$form->addHtml("</div>\n");
$form->addHtml("</fieldset>\n");


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>urldecode($_GET["return"]))
	);
$navbar = navbar2($navbar_array);

// start a new page
$page = new HTML_Page2($page_defaults);

$page->setTitle("Send Message");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content">');
$page->addBodyContent($navbar);
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

exit;

}
?>