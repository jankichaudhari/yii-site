<?php
// this page handles uplaoding pdfs to web server and sending to printers

session_start();
$pageTitle = "Advertising";
require("global.php"); 
require("secure.php"); 
require("HTTP/Upload.php");
$advertUploadPath = '../advertising'; // upload path



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


if ($_GET["action"]) { 
	$action = $_GET["action"]; 
	} elseif ($_POST["action"]) {
	$action = $_POST["action"]; 
	} else {
	$action = "upload";
	}

if ($action == "create") { // create an issue
	
	if ($_GET["pub"]) {
		$pub = $_GET["pub"];
		$deadline = $_GET["deadline"];
		$notes = $_GET["notes"];
		$sql = "INSERT INTO issue
		(iss_pub,iss_deadline,iss_notes)
		VALUES
		('$pub','$deadline','$notes')";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
		
		$query = 'SELECT LAST_INSERT_ID()'; 
		$result = mysql_query($query);
		$rec = mysql_fetch_array($result); 
		$pub = $rec[0]; 
		
		header("Location:?action=upload&iss=$pub");
		exit;
		
		}
	else {
		$render .= '
		<form method="GET">
		<table border="1" cellspacing="0" cellpadding="5">
		<tr>
		<td>Publication</td>
		<td><select name="pub">
		'.db_enum("issue","iss_pub",$pub).'
		</select></td>
		</tr>
		<tr>
		<td>Deadline</td>
		<td><input type="text" name="deadline" style="width:90px" readonly=true onClick="popUpCalendar(this, form.deadline, \'yyyy-mm-dd\')"></td>
		</tr>
		<tr>
		<td>Notes</td>
		<td><textarea name="notes"></textarea></td>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Add"></td>
		</tr>
		</table>
		<input type="hidden" name="action" value="create">
		</form>
		';
		}
	



	}
elseif ($action == "upload") { // upload a completed pdf and add to db
	
	if ($_POST["issue"]) {
		$issue = $_POST["issue"];
		$pdf = $_FILES["pdf"];
		$position = $_POST["position"];
		$notes = $_POST["notes"];
		
		
		// alt upload
		$mode = "uniq";
		$upload = new HTTP_Upload();
		$file = $upload->getFiles("pdf");
		
		if ($file->isValid()) {
			$file->setName ($mode, $prepend, $append);
			$properties = $file->getProp();
			$moved = $file->moveTo($advertUploadPath);
			if (!PEAR::isError($moved)) {
				//echo "File was moved to $advertUploadPath";
				}
			}
		
		$filename = $properties["name"];
		$origfile = $properties["real"];
		$filesize = $properties["size"];
		$filetype = $properties["type"];
		//print_r($properties);
		//exit;
		
		if ($filetype !== "application/pdf") {
			//$errors[] = "File must be a PDF";
			}
			

		
		
		
		
		
		
		if ($errors) {
			echo error_message($errors);
			exit;
			}
		
		$sql = "INSERT INTO advert
		(adv_issue,adv_file,adv_origfile,adv_filesize,adv_filetype,adv_position,adv_notes)
		VALUES
		('$issue','$filename','$origfile','$filesize','$filetype','$position','$notes')";
		//echo $sql;
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
		
		$query = 'SELECT LAST_INSERT_ID()'; 
		$result = mysql_query($query);
		$rec = mysql_fetch_array($result); 
		$advert = $rec[0]; 
		
		header("Location:?action=list&advert=$advert");
		exit;
		
		}
	else {
		$sql = "SELECT * FROM issue"; 
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("update error: ".$q->getMessage()); }
		while ($row = $q->fetchRow()) {
			$render_iss .= '<option value="'.$row["iss_id"].'"';
			if ($row["iss_id"] == $_GET["iss"]) {
				$render_iss .= ' selected';			
				}
			$render_iss .= '>'.$row["iss_pub"].' ('.$row["iss_deadline"].')</option>';
			}
			
		$render .= '
		<form method="POST" enctype="multipart/form-data">
		<table border="1" cellspacing="0" cellpadding="5">
		<tr>
		<td>Publication</td>
		<td><select name="issue">
		'.$render_iss.'
		</select></td>
		</tr>
		<tr>
		<td>Completed PDF</td>
		<td><input type="file" name="pdf"></td>
		</tr>
		<tr>
		<td>Position</td>
		<td><select name="position">
		'.db_enum("advert","adv_position",$position).'
		</select></td>
		</tr>
		<tr>
		<td>Notes</td>
		<td><textarea name="notes" rows="4"></textarea></td>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Add"></td>
		</tr>
		</table>
		<input type="hidden" name="action" value="'.$action.'">
		</form>
		';
		}
	
		
		
	
	}
elseif ($action == "list") { // list adverts
	
	$sql = "SELECT * FROM issue ORDER BY iss_deadline DESC";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("update error: ".$q->getMessage()); }
	
	$render .= '<tr>
	<td>Issue ID</td>
	<td>Publication</td>
	<td>Deadline</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	</tr>
	';
	while ($row = $q->fetchRow()) {
		$render .= '
		<tr>
		<td>'.$row["iss_id"].'</td>
		<td>'.$row["iss_pub"].'</td>
		<td>'.$row["iss_deadline"].'</td>
		<td><a href="?action=upload&iss='.$row["iss_id"].'">Add PDF</a></td>
		<td><a href="?action=pdfs&iss='.$row["iss_id"].'">View PDFs</a></td>
		</tr>
		';
		}
	$render = '<table border="1" cellspacing="0" cellpadding="5">'.$render.'</table>';
	}
elseif ($action == "pdfs") { // view pdfs for any given issue
	
	
	if (!$_GET["recipient"]) {
	
		$sql = "SELECT * FROM issue, advert 
		WHERE advert.adv_issue = ".$_GET["iss"]." AND advert.adv_issue = issue.iss_id
		ORDER BY issue.iss_deadline DESC";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("update error: ".$q->getMessage()); }
		
		$render .= '<tr>
		<td>Filename</td>
		<td>Size</td>
		<td>Type</td>
		<td>Position</td>
		<td>&nbsp;</td>
		</tr>
		';
		while ($row = $q->fetchRow()) {
			$iss = $row["adv_issue"];
			$pub = $row["iss_pub"];	
			$deadline = $row["iss_deadline"];
			$render .= '
			<tr>
			<td><a href="/advertising/'.$row["adv_file"].'" target="_blank">'.$row["adv_origfile"].'</a></td>
			<td>'.format_filesize($row["adv_filesize"]).'</td>
			<td>'.$row["adv_filetype"].'</td>
			<td>'.$row["adv_position"].'</td>
			<td><a href="?action=delete&advert='.$row["adv_id"].'">Delete</a></td>
			</tr>
			';
			}
		$render = '<table border="1" cellspacing="0" cellpadding="5">'.$render.'</table>';
		$render .= '<p>Please enter the email address of the person you want to send these adverts to, then click send</p>
		<form>
		<table>
		<tr>
		<td>Email address:</td>
		<td><input type="text" name="recipient" value="" size="30"></td>
		<td><input type="submit" value="Send"></td>
		</tr>
		</table>
		<input type="hidden" name="action" value="pdfs">
		<input type="hidden" name="iss" value="'.$iss.'">
		<input type="hidden" name="pub" value="'.$pub.'">
		<input type="hidden" name="deadline" value="'.$deadline.'">
		</form>
		
		<p>
		Living South: <a href="javascript:onClick(document.forms[0].recipient.value = \'Jane.Mukupa@archant.co.uk\');">Jane.Mukupa@archant.co.uk</a><br>
		South London Press: <a href="javascript:onClick(document.forms[0].recipient.value = \'property@slp.co.uk\');">property@slp.co.uk</a><br>
		Southwark Weekender: <a href="javascript:onClick(document.forms[0].recipient.value = \'production@southwarknews.org\');">production@southwarknews.org</a><br>
		Reach: <a href="javascript:onClick(document.forms[0].recipient.value = \'neena.taylor@archant.co.uk\');">neena.taylor@archant.co.uk</a>
		</p>';
		
		} else {
		
		$sender = $_SESSION["s_user"].'@woosterstock.co.uk';
		$recipient = $_GET["recipient"];//"mark@woosterstock.co.uk";
		$subject = "Artwork from Wooster & Stock";
		$body = "Completed artwork is available for download from Wooster and Stock\n\n";		
		$body .= "Publication: $pub\nDeadline: $deadline\n\n";
		$body .= "Please click below to view the downloads\n\n";
		$body .= "http://www.woosterstock.co.uk/advertising/pickup.php?iss=".$_GET["iss"];
		$headers = "Content-Type:text/plain;CHARSET=iso-8859-8-i\r\n";
		$headers .= "From:$sender\r\n";
		mail($recipient, $subject, $body, $headers);
		mail('mark@woosterstock.co.uk', $subject, $body, $headers);
		$render = '<p>Mail sent to '.$recipient.'</p>';
		}
	
	}
elseif ($action == "delete") { // delete a pdf file
	
	if ($_GET["advert"]) {
		
		$sql = "SELECT * FROM advert 
		WHERE advert.adv_id = ".$_GET["advert"]." 
		LIMIT 1";
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
		
		while ($row = $q->fetchRow()) {
			$adv_file = $row["adv_file"];
			}
			
		unlink("/home/woosterstock/htdocs/advertising/".$adv_file);
		
		$sql = "DELETE FROM advert
		WHERE advert.adv_id = ".$_GET["advert"];
		$q = $db->query($sql);
		if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
		
		$render = '<p>File Deleted</p>';
		
		}
	}











/*
//change_log($_SESSION["s_userid"],"clients","Client_ID",$intClientID,$sql_body,$PHPSESSID);
$sql = "
UPDATE clients SET ".$sql_body." 
WHERE Client_ID = $intClientID";	
$q = $db->query($sql);
if (DB::isError($q)) {  die("update error: ".$q->getMessage()); }
$pageTitle = "Update Client Complete";
echo html_header($pageTitle);
echo '
<table width="600" align="center">
<tr> 
<td><span class="pageTitle">'.$pageTitle.'</span></td>
<td align="right"><a href="index.php">Main Menu</a></td>
</tr>
<tr>
<td colspan="2">
<p>&nbsp;</p>
<p><a href="client.php?ClientID='.$intClientID.'&searchLink='.$_POST["searchLink"].'">Edit the Client</a></p>
<p><a href="'.urldecode($_POST["searchLink"]).'">Back to last search</a></p>
</td>
</tr>
</table>
';
}
$sql = "
INSERT INTO clients 
(Password,Email,Email2,Name,Salutation,Address1,Address2,Address3,City,Country,
Postcode,Tel,Fax,Mobile,PropertyType,MinPrice,MaxPrice,Receptions,Bedrooms,
Bathrooms,Areas,Areas2,Notes,DG,GCH,Modern,Period,Tenure,Garden,Parking,
BuyToLet,HeardBy,Selling,Valuation,DateCreated,DateModified,Status,PropertyTypeLet,
BedroomsLet,MinPriceLet,MaxPriceLet,FurnishedLet,TermLet,StatusLet,Lettings,Sales,Branch,neg)
VALUES
('$Password','$Email','$Email2','$Name','$Salutation','$Address1','$Address2','$Address3','$City','$Country',
'$Postcode','$Tel','$Fax','$Mobile','$PropertyType','$MinPrice','$MaxPrice','$Receptions','$Bedrooms',
'$Bathrooms','$AreaSQL','$Areas2','$Notes','$DG','$GCH','$Modern','$Period','$Tenure','$Garden','$Parking',
'$BuyToLet','$HeardBy','$Selling','$Valuation','$dateToday','$dateToday','$Status','$PropertyTypeLet',
'$BedroomsLet','$MinPriceLet','$MaxPriceLet','$FurnishedLet','$TermLet','$StatusLet','$Lettings','$Sales','$selected_branches','$neg')
";
$q = $db->query($sql);
//echo $sql;
if (DB::isError($q)) {  die("insert error: ".$q->getMessage()); }
$query = 'SELECT LAST_INSERT_ID()'; 
$result = mysql_query($query); 
$rec = mysql_fetch_array($result); 
$insert_id = $rec[0]; 
$pageTitle = "Add Client Complete";
echo html_header($pageTitle);
echo '
<table width="600" align="center">
<tr> 
<td><span class="pageTitle">'.$pageTitle.'</span></td>
<td align="right"><a href="index.php">Main Menu</a></td>
</tr>
<tr>
<td colspan="2">
<p>&nbsp;</p>
<p><a href="client.php?ClientID='.$insert_id.'">Edit the client</p></p>
<p><a href="client.php">Add another client</a></p>
</td>
</tr>
</table>
';
// format and send email to appropriate branch or branches with full client details
$subject = $insert_id." - New Client Registration";

$headers 	= "Content-Type:text/plain;CHARSET=iso-8859-8-i\r\n";
$headers	.="From:$Email\r\n";
mail($to, $subject, $body, $headers);	  
*/
echo html_header("Advertising");
?>
<p><a href="?action=create">Create Issue</a> - <a href="?action=upload">Upload PDF</a> - <a href="?action=list">List Adverts</a></p>
<?php echo $render; ?>
</body></html>