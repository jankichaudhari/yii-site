<?php
// error handling and reporting

error_reporting(E_ERROR | E_WARNING | E_PARSE);

// return formatted error page
function error_message($_errors,$_return=NULL,$noheader=NULL) {

	global $header_and_menu;

	// $_errors no longer needs to be an array
	if (!is_array($_errors)) {
		$_errors = array($_errors);
		}

	$_errorcount = count($_errors);

	/*
	$_funnymsg[] = ", come on";
	$_funnymsg[] = ", make an effort will you";
	$_funnymsg[] = ", it's not rocket science";
	$_funnymsg[] = ", you really aren't trying very hard are you";
	$_funnymsg[] = ", erm...";
	$_funnymsg[] = ", deary me";

	$_num = rand(0,(count($_funnymsg)-1));
	*/

	if ($_errorcount == 1) {
		$_message = "<h1>Sorry, an error has occurred</h1>";
		} else {
		$_message = "<h1>$_errorcount errors have occurred".$_funnymsg[$_num]."</h1>";
		}
	foreach ($_errors AS $key=>$val) {
		$_message .= $val."<br>\n";
		}
	/*
	for ($_n=0; $_n < $_errorcount; $_n++) {
		$_message .= $_errors[$_n]."<br>\n";
		}
	*/
	if ($_return) {
		$_link = urldecode($_return);
		} else {
		$_link = "javascript:history.back(1);";
		}
	$_message .= '<p>Please go <a href="'.$_link.'">back</a> and try again, or return to your <a href="home.php">home page</a></p>';


	$output = '<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3c.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>Error</title>
<link rel="stylesheet" href="/css/old_admin_styles.css" type="text/css" />
<script type="text/javascript" src="/v3.0/live/admin/js/global.js"></script>
</head>
<body>
';
if (!$noheader) {
	$output .= $header_and_menu;
	}
$output .= '
<div id="error">
'.$_message.'
</div>
</body>
</html>';
	return $output;
	unset($_errors,$_errorcount,$_n,$_message,$_return,$_link);
}



// send notification email to administrator
function admin_notify($_data) {

	//admin_notify(array('subject'=>'Manual Property Entry','content'=>'Property ID:'.$pro_id));
	if ($_data['subject']) {
		$subject = $_data['subject'];
		} else {
		$subject = "admin notify (blank subject)";
		}
	$content = $_data['content'];
	// add technical info
	$content .= "\n\n
User ID:	".$_SESSION["auth"]["use_id"]."
DateTime:	".date('r')."
Page:		".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."
SessionID:	".$_SESSION["auth"]["session_id"];

	//send_email("webmaster@woosterstock.co.uk","webmaster@woosterstock.co.uk",$subject,$content);
	}
?>