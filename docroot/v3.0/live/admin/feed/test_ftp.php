<!--#!/usr/local/bin/php -q-->
<?php

// copy all files in a folder to ftp site
function ftp_copy($src_dir, $dst_dir) {
	global $conn_id;
	$blmFile = date('Ymd').".tar.gz";
	echo $strpath;
	echo ftp_put($conn_id, $dst_dir."/".$blmFile, $src_dir."/".$blmFile, FTP_ASCII); //Added
	echo "File FTP'd";
	echo TRUE;
	}


// path to save text file
$strFolderName = date('Ymd');
$strPath = '/home/woosterstock/htdocs/v3.0/live/admin/feed/rightmove/'.$strFolderName;
// name of textfile (date.blm)
$strTextFile = date('Ymd').".blm";

// ******************* FindaProperty **********************
// log in to ftp site and upload contents of folder

$ftp_server = "fapfeed.findaproperty.com"; //81.171.194.128
$ftp_username = "woosterandstock";
$ftp_password = "wo0st3r@ndst*ck";

#$conn_id = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
if (ftp_connect($ftp_server)) {
	$conn_id = ftp_connect($ftp_server);
	} else {
	$errors[] = "could not connect to $ftp_server";
	}
$login_result = ftp_login($conn_id, $ftp_username, $ftp_password);
ftp_pasv($conn_id, true);  // Passive mode to test ftp

$src_dir = $strPath;
$dst_dir = ".";

ftp_copy($src_dir, $dst_dir);

ftp_close($conn_id);	

?>
