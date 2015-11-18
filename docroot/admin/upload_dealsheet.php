<?php
session_start();
$pageTitle = "Upload Dealsheet";
require("global.php"); 
require("secure.php"); 
require("HTTP/Upload.php");
$filesUploadPath = 'dealsheet'; // upload path



if ($_POST["submitted"] == "uhhu") {

	$thefile = $_POST["thefile"];
	// alt upload
	$mode = "uniq";
	$upload = new HTTP_Upload();
	$file = $upload->getFiles("thefile");		
	if ($file->isValid()) {
		$file->setName("dealsheet.xls");
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

	

	if ($errors) {
		echo error_message($errors);
		exit;
		}
		
	header("Location:?");
	exit;
	
	} else {
	
	
	$render .= '
	<p><a href="dealsheet/dealsheet.xls">View Dealsheet</a></p>
	
	<form method="POST" enctype="multipart/form-data">
	<table border="1" cellspacing="0" cellpadding="5">

	<tr>
	<td>Dealsheet</td>
	<td><input type="file" name="thefile" style="width:400px"></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="Upload" onClick="javascript:uploadWarning();"></td>
	</tr>
	</table>
	<input type="hidden" name="action" value="'.$action.'">
	<input type="hidden" name="submitted" value="uhhu">
	</form>
	
	';
	
	}
	
echo html_header($pageTitle);
echo '<script>
function uploadWarning() {
	uploadmessage ="\nThis will OVERWRITE the current dealsheet\n\nClick the button below to continue";
	return alert(uploadmessage);
	}
</script>';
echo $render; 
echo '<a href="/admin/">Back to main menu</a>
</body></html>';
?>