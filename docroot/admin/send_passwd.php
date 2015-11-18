<?php
session_start();
require("global.php"); 
require("secure.php"); 


$sql = "SELECT * FROM admin WHERE adm_status = 'Active'";
$q = $db->query($sql);

if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
while ($row = $q->fetchRow()) {
	
	$subject = 'Password reminder';
	$body = '
Log in to admin here: https://www.woosterstock.co.uk/admin

Username: '.$row["adm_user"].'
Password: '.$row["adm_pass"].'

IMPORTANT: You must never disclose this password to anyone else, and you must never send 
it by email to an address outside of this company';
	
	$headers = "Content-Type:text/plain;CHARSET=iso-8859-8-1\r\n";
	$headers .="From:mark@woosterstock.co.uk\r\n";
	$email = $row["adm_user"].'@woosterstock.co.uk';
	mail($email, $subject, $body, $headers);
	
	$email = '';
	$subject = '';
	$body = '';
	$headers = '';
	}


?>