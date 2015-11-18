<?php
require_once("../inx/global.inc.php");
include("menu.php");
/*
Directory images:
Unlimited images allowed, but can only add 1 at a time
Upload, move from temp folder to directory image folder ($image_path_directory)
Thumbmail creation - sizes? 
Add to media table
Allow re-ordering or image, add more and delete
*/


/* 
this will delete the select image and the related database row 
14/09/2006
*/
if ($_GET["action"] == "delete_image") {
	if (!$_GET["dir_id"]) {
		echo "no directory enrty selected";
		exit;
		}
	if (!$_GET["med_id"]) {
		echo "no media file selected";
		exit;
		}
	$sql = "SELECT med_file FROM media WHERE med_id = ".$_GET["med_id"];
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	while ($row = $q->fetchRow()) {
		$med_file = $row["med_file"];
		}
	@unlink(IMAGE_PATH_DIRECTORY.$med_file);
	# here we also need to delete the thumbnails
	@unlink(IMAGE_PATH_DIRECTORY.str_replace('.jpg','_S.jpg',$med_file));
	
	$sql = "DELETE FROM media WHERE med_id = $med_id AND med_table = 'directory'";
	$q = $db->query($sql);
	if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
	
	header("Location:image.php?dir_id=".$_GET["dir_id"]);
	exit;
	}


if ($_GET["action"] == "recreatethumbs") {
	
	$dest_name = $_GET["file"];
	
	$thumbnail_width = 100;
	$thumbnail_height = 100;
	require_once("../inx/phpThumb/phpThumb.php");
	$phpThumb = new phpThumb();
	// set data
	$phpThumb->setSourceFilename(IMAGE_PATH_DIRECTORY.$dest_name);
	$phpThumb->setParameter('h', $thumbnail_height);
	$phpThumb->setParameter('config_output_format', 'jpg');			
	
	// generate & output thumbnail
	$output_filename = IMAGE_PATH_DIRECTORY.str_replace('.jpg','',$dest_name).'_t_'.$thumbnail_width.'x'.$thumbnail_height.'.'.$phpThumb->config_output_format;
	if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
		$output_size_x = ImageSX($phpThumb->gdimg_output);
		$output_size_y = ImageSY($phpThumb->gdimg_output);
		if ($output_filename) {
			if ($phpThumb->RenderToFile($output_filename)) {
				// do something on success
				#echo 'Successfully rendered:<br><img src="'.$output_filename.'">';
				} else {
				// do something with debug/error messages
				echo 'Failed (size='.$thumbnail_width.'):<pre>'.implode("\n\n", $phpThumb->debugmessages).'</pre>';
				}
			} else {
			$phpThumb->OutputThumbnail();
			}
		} else {
			// do something with debug/error messages
			echo 'Failed (size='.$thumbnail_width.').<br>';
			echo '<div style="background-color:#FFEEDD; font-weight: bold; padding: 10px;">'.$phpThumb->fatalerror.'</div>';
			echo '<form><textarea rows="10" cols="60" wrap="off">'.htmlentities(implode("\n* ", $phpThumb->debugmessages)).'</textarea></form><hr>';
			}			
		
	header("Location:image.php?dir_id=".$_GET["dir_id"]);
	exit;
	}

if ($_GET["action"] == "reorder") {
	
	$image = $_GET["med_id"];
	$direction = $_GET["direction"];
	
	
	
	header("Location:image.php?dir_id=".$_GET["dir_id"]);
	}




$formData1 = array(	 
	'med_file'=>array(
		'type'=>'file',
		'label'=>'Image',
		'required'=>3,
		'attributes'=>array('style'=>'width:320px')
		), 
	'med_title'=>array(
		'type'=>'text',
		'label'=>'Title',
		'required'=>1,
		'attributes'=>array('style'=>'width:320px')
		), 
	'med_blurb'=>array(
		'type'=>'textarea',
		'label'=>'Description',
		'required'=>1,
		'attributes'=>array('style'=>'width:320px;height:50px')
		)
	)
	;

if (!$_POST["action"]) {

if (!$_GET["dir_id"]) {
	echo "no directory entry identifier supplied";
	exit;
	}
	
// start new form object 
$form = new Form();

$form->addForm("form","post",$PHP_SELF,"multipart/form-data");
$form->addHtml("<div id=\"standard_form\">\n");
$form->addField("hidden","action","","update");
$form->addField("hidden","dir_id","",$_GET["dir_id"]);

/////////////////////////////////////////////////////////////////////////////////
$form->addHtml("<fieldset>\n");
$form->addLegend('Add Image');
$form->addData($formData1,$_GET);
$form->addHtml($form->addDiv($form->makeField("submit","","","Save Changes",array('class'=>'submit'))));
$form->addHtml("</fieldset>\n");


/////////////////////////////////////////////////////////////////////////////////
// get existing images
$sql = "SELECT * FROM media WHERE
med_table = 'directory' AND
med_row = ".$_GET["dir_id"]."
ORDER BY med_order ASC, med_created ASC";
$q = $db->query($sql);
if (DB::isError($q)) {  die("db error: ".$q->getMessage()); }
$numRows = $q->numRows();

if ($numRows <> 0) {
	$form->addHtml("<fieldset>\n");
	$form->addLegend('Existing Images');
	while ($row = $q->fetchRow()) {
		$html .= '
		<tr>
		<td width="200"><a href="'.IMAGE_URL_DIRECTORY.$row["med_file"].'" target="_blank"><img src="'.IMAGE_URL_DIRECTORY.str_replace('.jpg','_S.jpg',$row["med_file"]).'"></a></td>
		<td>Title: '.$row["med_title"].'<br>
		Description: '.$row["med_blurb"].'<br>
		Size: '.format_filesize($row["med_filesize"]).'<br>
		Date: '.$row["med_created"].'</td>
		<td width="60" align="right">
		<a href="javascript:reOrder('.$row["med_id"].',\'up\');"><img src="/images/sys/admin/icons/arrow_up.gif" border="0" alt="Move Up" height="16" width="16"></a>
		<a href="javascript:reOrder('.$row["med_id"].',\'down\');"><img src="/images/sys/admin/icons/arrow_down.gif" border="0" alt="Move Down" height="16" width="16"></a>
		<a href="?action=delete_image&dir_id='.$_GET["dir_id"].'&med_id='.$row["med_id"].'"><img src="/images/sys/admin/icons/cross.gif" border="0" alt="Delete" height="16" width="16"></a>
		</td>
		</tr>';		
		}
	$form->addHtml('<table width="600" cellpadding="5" cellspacing="0">'.$html.'</table>');
	$form->addHtml("</fieldset>\n");
	}
/////////////////////////////////////////////////////////////////////////////////


// start a new page
$page = new HTML_Page2($page_defaults);
$page->setTitle("Directory > Edit");
$page->addStyleSheet(GLOBAL_URL.'css/styles.css');
$page->addScript(GLOBAL_URL.'js/global.js');
$page->addScript(GLOBAL_URL.'js/scriptaculous/prototype.js');
$page->addScriptDeclaration($source['js']);
$page->setBodyAttributes(array('onLoad'=>$source['onload']));
$page->addBodyContent('<div id="content">');
$page->addBodyContent($menu);
$page->addBodyContent('<p><a href="edit.php?dir_id='.$dir_id.'">Back</a>');
$page->addBodyContent($form->renderForm());
$page->addBodyContent('</div>');
$page->display();

} else {

$result = new Validate();
$results = $result->process($formData1,$_POST);
if ($results['Errors']) {
	echo error_message($results['Errors'],urlencode($return));
	exit;
	}
#print_r($_POST);
#print_r($_FILES);

	require_once "HTTP/Upload.php";

	$upload = new HTTP_Upload("en");
	$files = $upload->getFiles();
	
	foreach($files as $file){
		if (PEAR::isError($file)) {
			echo $file->getMessage();
			}
	
		if ($file->isValid()) {
		
			if ($file->getProp("type") == 'image/pjpeg' || $file->getProp("type") == 'image/jpeg') {
				$allowed = "1";
				}
			if (!$allowed) {
			
				echo "Only JPG images are allowed<br>";
				echo $file->getProp("type");
				exit;
				}
			
			
			$file->setName("uniq");
			$dest_name = $file->moveTo(IMAGE_PATH_DIRECTORY); 
			#echo $image_path_directory.$dest_name;
	
			if (PEAR::isError($dest_name)) {
				echo $dest_name->getMessage();
				}	
					
					
			// create thumbnails
			
			$thumbnail_width = 100;
			$thumbnail_height = 100;
			require_once("../inx/phpThumb/phpthumb.class.php");
			$phpThumb = new phpThumb();
			// set data
			$phpThumb->setSourceFilename(IMAGE_PATH_DIRECTORY.$dest_name);
			$phpThumb->setParameter('h', $thumbnail_height);
			$phpThumb->setParameter('config_output_format', 'jpg');			
			
			// generate & output thumbnail
			$output_filename = IMAGE_PATH_DIRECTORY.str_replace('.jpg','',$dest_name).'_S.'.$phpThumb->config_output_format;
			if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
				$output_size_x = ImageSX($phpThumb->gdimg_output);
				$output_size_y = ImageSY($phpThumb->gdimg_output);
				if ($output_filename) {
					if ($phpThumb->RenderToFile($output_filename)) {
						// do something on success
						#echo 'Successfully rendered:<br><img src="'.$output_filename.'">';
					} else {
						// do something with debug/error messages
						echo 'Failed (size='.$thumbnail_width.'):<pre>'.implode("\n\n", $phpThumb->debugmessages).'</pre>';
					}
				} else {
					$phpThumb->OutputThumbnail();
				}
			} else {
				// do something with debug/error messages
				echo 'Failed (size='.$thumbnail_width.').<br>';
				echo '<div style="background-color:#FFEEDD; font-weight: bold; padding: 10px;">'.$phpThumb->fatalerror.'</div>';
				echo '<form><textarea rows="10" cols="60" wrap="off">'.htmlentities(implode("\n* ", $phpThumb->debugmessages)).'</textarea></form><hr>';
			}
			
			$db_data["med_table"] = 'directory';
			$db_data["med_row"] = $_POST["dir_id"];
			$db_data["med_type"] = 'Photograph';
			$db_data["med_order"] = $i;
			$db_data["med_title"] = $_POST["med_title"]; 
			$db_data["med_blurb"] = $_POST["med_blurb"]; 
			$db_data["med_file"] = $file->getProp("name");
			$db_data["med_realname"] = $file->getProp("real");
			$db_data["med_filetype "] = $file->getProp("type");
			$db_data["med_filesize"] = $file->getProp("size");
			$db_data["med_created"] = $date_mysql;
			$med_id = db_query($db_data,"INSERT","media","med_id");
			#print_r($db_data);
			$i++;		
			
	
		} elseif ($file->isMissing()) {
			#echo "No file was provided.";
		} elseif ($file->isError()) {
			echo $file->errorMsg();
		}	
		
	}

	header("Location:?dir_id=".$_POST["dir_id"]);


}
?>