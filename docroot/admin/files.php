<?php
// this page handles uplaoding files too large to email, and then sends an email with link

session_start();
$pageTitle = "Files";
require("global.php"); 
require("secure.php"); 
require("HTTP/Upload.php");
$filesUploadPath = '../files'; // upload path

ini_set(max_execution_time,600);
$max_execution_time = ini_get(max_execution_time);
ini_set(max_input_time,600);
$max_input_time = ini_get(max_input_time);
ini_set(memory_limit,'200M');
$memory_limit = ini_get(memory_limit);
ini_set(post_max_size,'30M');
$post_max_size = ini_get(post_max_size);
ini_set(upload_max_filesize,'30M');
$upload_max_filesize = ini_get(upload_max_filesize);
/*
max_execution_time = 600
max_input_time = 600
memory_limit = 200M
post_max_size = 40M
upload_max_filesize = 40M
*/

// auto-delete everything older than a month
$dateLast31Days = date("Y-m-d H:i:s",strtotime(date('Y-m-j H:i:s')) - (4 * 7 * 24 * 60 * 60));
$sql = "SELECT * FROM files 
WHERE file_date <  '$dateLast31Days' ORDER BY file_date";
$q = $db->query($sql);
if (DB::isError($q)) {  die("error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {	
	@unlink("/home/woosterstock/htdocs/files/".$row["file_file"]);	
	$sql2 = "DELETE FROM files
	WHERE file_id = ".$row["file_id"];		
	$q2 = $db->query($sql2);
	if (DB::isError($q2)) {  die("error: ".$q2->getMessage()); }	
	}



if ($_GET["action"]) { 
	$action = $_GET["action"]; 
	} elseif ($_POST["action"]) {
	$action = $_POST["action"]; 
	} else {
	$action = "upload";
	}

if ($action == "upload") { // upload a file
	
	if ($_POST["submitted"] == 'uhhu') {
	
		print_r($_FILES);
		
		if (!$_POST["recipient"]) {
			$errors[] = "Recipient is required and must be a valid email address";
			} else {
			$recipient = $_POST["recipient"];
			# mulitple recipients			
			/*(if (strpos($recipient,';') == true) {
				$recipient = explode(';',$recipient);
				}			*/
			}
		if (!validate_email($_POST["sender"])) {
			$errors[] = "Sender is required and must be a valid email address";
			} else {
			$sender = $_POST["sender"];
			}
		
		
		$title = $_POST["title"];
		
		#if ($_POST["thefile"]) {
		
			$thefile = $_FILES["thefile"];
			// alt upload
			$mode = "uniq";
			$upload = new HTTP_Upload();
			$file = $upload->getFiles("thefile");		
			if ($file->isValid()) {
				$file->setName ($mode, $prepend, $append);
				$properties = $file->getProp();
				$moved = $file->moveTo($filesUploadPath);
				if (!PEAR::isError($moved)) {
					//echo "File was moved to $advertUploadPath";
					}
				}	
			$filename = $properties["name"];
			$origfile = $properties["real"];
			$filesize = $properties["size"];
			$filetype = $properties["type"];
			#if ($filetype !== "application/pdf") {
			#	$errors[] = "File must be a PDF";
			#	}
		#} else {
		#$errors[] = "You must select a file to upload";
		#}
					
		if ($errors) {
			echo error_message($errors);
			exit;
			}
		
		$sql = "INSERT INTO files
		(file_filename,file_type,file_size,file_orig,file_title,file_recipient,file_sender,file_date,file_user)
		VALUES
		('$filename','$filetype','$filesize','$origfile','$title','$recipient','$sender','$dateToday','".$_SESSION["s_userid"]."')";
		#echo $sql;
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
		
		$query = 'SELECT LAST_INSERT_ID()'; 
		$result = mysql_query($query);
		$rec = mysql_fetch_array($result); 
		$file = $rec[0]; 
		
		# send email
		
		$subject = "File Sending from Wooster & Stock";
		$body = "You have been sent a file from Wooster & Stock\n\n";
		$body .= "File Name: ".$origfile."\n";
		$body .= "File Type: ".$filetype."\n";
		$body .= "File Size: ".format_filesize($filesize)."\n\n";
		$body .= "Please click the link below to download the file:\n\n";
		$body .= "http://www.woosterstock.co.uk/files/pickup.php?file=".$filename."\n\n";
		$body .= "If the url is broken into more than one line, please copy and paste the whole url into your browser";
		
		$headers 	= "Content-Type:text/plain;CHARSET=iso-8859-8-i\r\n";
		$headers	.="From:$sender\r\n";
		
		if (!mail($recipient, $subject, $body, $headers)) {
			$errors[] = "There was a problem sending the email, please try again";
			}
		
		/*
		if (is_array($recipient)) {
			foreach ($recipient as $rec) {
				$recipients .= $recipient.';';
				mail($rec, $subject, $body, $headers);
				}
			} else {
			mail($recipient, $subject, $body, $headers);
			}
		*/
		
		if ($errors) {
			echo error_message($errors);
			exit;
			}
		
		$render = '<p>Your file was successfully uploaded and sent to the recipient</p>';
		$render .= '<p><a href="?">Click here to send another file</a></p>';
		$render .= '<p><a href="?action=list">Click here view all sent files</a></p>';
		#header("Location:?file=$file");
		#exit;
		
		}
	else {
		
		
		$render .= '
		<form method="POST" enctype="multipart/form-data">
		<table border="1" cellspacing="0" cellpadding="5">
		<tr>
		<td>Sender\'s Email</td>
		<td><input type="text" name="sender" value="'.$_SESSION["s_user"].'@woosterstock.co.uk" style="width:400px"></td>
		</tr>
		<tr>
		<td>Recipient\'s Email</td>
		<td><input type="text" name="recipient" value="" style="width:400px"><br>(seperate multiple email address with a comma)</td>
		</tr>
		<tr>
		<td>Subject or Title of file</td>
		<td><input type="text" name="title" style="width:400px"></td>
		</tr>
		<tr>
		<td>File to send</td>
		<td><input type="file" name="thefile" style="width:400px"></td>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Send" onClick="javascript:uploadWarning();"></td>
		</tr>
		</table>
		<input type="hidden" name="action" value="'.$action.'">
		<input type="hidden" name="submitted" value="uhhu">
		</form>
		';
		#$render .= '<p><a href="?">Click here to send another file</a></p>';
		$render .= '<p><a href="?action=list">Click here view all sent files</a></p>';
		}
	
	}

elseif ($action == "list") {
	
	$sql = "SELECT * FROM files ORDER BY file_date DESC";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("update error: ".$q->getMessage()); }
	
	$render .= '<tr>
	<td>File Title</td>
	<td>File Name</td>
	<td>File Type</td>
	<td>File Size</td>
	<td>Sender</td>
	<td>Recipient</td>
	<td>Date</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	</tr>
	';
	while ($row = $q->fetchRow()) {
		$render .= '
		<tr>
		<td>'.$row["file_title"].'</td>
		<td>'.$row["file_orig"].'</td>
		<td>'.$row["file_type"].'</td>
		<td>'.format_filesize($row["file_size"]).'</td>
		<td>'.$row["file_sender"].'</td>
		<td>'.str_replace(',',',<br>',$row["file_recipient"]).'</td>
		<td>'.$row["file_date"].'</td>
		<td><a href="/files/'.$row["file_filename"].'" target="_blank">VIEW</a></td>
		<td><a href="?action=resend&file='.$row["file_id"].'">RESEND</a></td>
		<td><a href="?action=delete&file='.$row["file_id"].'">DELETE</a></td>
		</tr>
		';
		$total_size = $total_size+$row["file_size"];
		}
	$render = '<table border="1" cellspacing="0" cellpadding="5">'.$render.'</table>';
	$render .= '<p>Total space used: '.format_filesize($total_size).'</p>';
	$render .= '<p><a href="?">Click here to send another file</a></p>';
	$render .= '<p><a href="?action=list">Click here view all sent files</a></p>';

	}
elseif ($action == "delete") { // delete a file
	
	if ($_GET["file"]) {
		
		$sql = "SELECT * FROM files 
		WHERE file_id = ".$_GET["file"]." 
		LIMIT 1";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
		
		while ($row = $q->fetchRow()) {
			$file_file = $row["file_filename"];
			}
			
		@unlink("/home/woosterstock/htdocs/files/".$file_file);
		
		$sql = "DELETE FROM files
		WHERE file_id = ".$_GET["file"];
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
		
		$render = '<p>File Deleted</p>';
		$render .= '<p><a href="?">Click here to send another file</a></p>';
		$render .= '<p><a href="?action=list">Click here view all sent files</a></p>';
		
		}
	}

elseif ($action == "resend") { // delete a file
	
	if ($_POST["submitted"] == "uhhu") {
	
		$sql = "SELECT * FROM files 
		WHERE file_id = ".$_POST["file_id"]." 
		LIMIT 1";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
		
		while ($row = $q->fetchRow()) {
			$file_id = $row["file_id"];
			$file_sender = $row["file_sender"];
			$file_recipient = $row["file_recipient"];
			$file_title = $row["file_title"];
			$file_filename = $row["file_filename"];
			$file_orig = $row["file_orig"];
			$file_size = $row["file_size"];
			$file_type = $row["file_type"];
			}
		
		#send email
		$subject = "File Sending from Wooster & Stock";
		$body = "You have been sent a file from Wooster & Stock\n\n";
		$body .= "File Name: ".$file_orig."\n";
		$body .= "File Type: ".$file_type."\n";
		$body .= "File Size: ".format_filesize($file_size)."\n\n";
		$body .= "Please click the link below to download the file:\n\n";
		$body .= "http://www.woosterstock.co.uk/files/pickup.php?file=".$file_filename."\n\n";
		$body .= "If the url is broken into more than one line, please copy and paste the whole url into your browser";
		
		$headers 	= "Content-Type:text/plain;CHARSET=iso-8859-8-i\r\n";
		$headers	.="From:$sender\r\n";
		mail($recipient, $subject, $body, $headers);
		
		$render = '<p>Your file was successfully uploaded and sent to the recipient</p>';
		$render .= '<p><a href="?">Click here to send another file</a></p>';
		$render .= '<p><a href="?action=list">Click here view all sent files</a></p>';
		
		
		} else {
		
		$sql = "SELECT * FROM files 
		WHERE file_id = ".$_GET["file"]." 
		LIMIT 1";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
		
		while ($row = $q->fetchRow()) {
			$file_id = $row["file_id"];
			$file_sender = $row["file_sender"];
			$file_recipient = $row["file_recipient"];
			$file_title = $row["file_title"];
			$file_filename = $row["file_filename"];
			$file_orig = $row["file_orig"];
			}
		
		$render .= '
		<form method="POST" enctype="multipart/form-data">
		<table border="1" cellspacing="0" cellpadding="5">
		<tr>
		<td>Sender\'s Email</td>
		<td><input type="text" name="sender" value="'.$file_sender.'" style="width:400px"></td>
		</tr>
		<tr>
		<td>Recipient\'s Email</td>
		<td><input type="text" name="recipient" value="'.$file_recipient.'" style="width:400px"><br>(seperate multiple email address with a comma)</td>
		</tr>
		<tr>
		<td>Subject or Title of file</td>
		<td><input type="text" name="title" value="'.$file_title.'" style="width:400px"></td>
		</tr>
		<tr>
		<td>File to send</td>
		<td>'.$file_orig.'</td>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Send"></td>
		</tr>
		</table>
		<input type="hidden" name="file_id" value="'.$file_id.'">
		<input type="hidden" name="action" value="'.$action.'">
		<input type="hidden" name="submitted" value="uhhu">
		</form>
		';
		#$render .= '<p><a href="?">Click here to send another file</a></p>';
		$render .= '<p><a href="?action=list">Click here view all sent files</a></p>';
		
		}
	}

echo html_header($pageTitle);
echo '<script>
function uploadWarning() {
	uploadmessage ="\nPlease be patient while the file is uploaded to the server\n\nThis process can take quite a while, depending on the size of the file\n\nClick the button below to continue";
	return alert(uploadmessage);
	}
</script>';
echo $render; 
echo '<p><font color=red>Note: Files older than a month are automatically deleted</font></p>'; 
echo '<a href="/admin/">Back to main menu</a>
</body></html>';

echo "<pre>
max_execution_time     $max_execution_time 
max_input_time         $max_input_time 
memory_limit           $memory_limit 
post_max_size          $post_max_size 
upload_max_filesize    $upload_max_filesize
</pre>";


	


?>