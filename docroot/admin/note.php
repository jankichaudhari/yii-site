<?php
session_start();
$pageTitle = "Notes";
require("global.php"); 
require("secure.php"); 
require("queryLog.php");


/*
notes
browse notes, show all notes flagged for follow up, show notes by user (my notes)
*/

echo html_header($pageTitle);
?>
<table width="600" align="center">
	  <tr> 
		<td><span class="pageTitle"><?php echo $pageTitle; ?></span></td>
		<td align="right"><?php if ($_GET["searchLink"]) { echo '<a href="'.urldecode($_GET["searchLink"]).'">Back to Search</a> &nbsp; '; } ?><a href="index.php">Main Menu</a></td>
	  </tr>
	</table> 

<?php 
if (!$_GET["page"]) {
?>
 	
	<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th class="greyForm">Notes</th>
    </tr>
    <tr>
      <td class="greyForm"><p>Clients </p>
        <ul>
          <li><a href="?page=2&table=clients&view=mynotes">My Notes</a></li>
          <li><a href="?page=2&table=clients&view=followup">Follow-up</a></li>
          <li><a href="?page=2&table=clients&view=all">All</a></li>
        </ul> 
	</td>
	</tr>
    <tr>
      <td class="greyForm">       
        <p>Property </p>
        <ul>
          <li><a href="?page=2&table=property&view=mynotes">My Notes</a></li>
          <li><a href="?page=2&table=property&view=followup">Follow-up</a></li>
          <li><a href="?page=2&table=property&view=all">All</a></li>
      </ul></td>
    </tr>
  </table>
<?php
} elseif ($_GET["page"] == 2) {



$table = $_GET["table"];

if ($_GET["view"] == "all") {
	$sql_inner = "";
	} elseif ($_GET["view"] == "followup") {
	$sql_inner = " AND not_flag = 'Follow-up' ";
	} elseif ($_GET["view"] == "mynotes") {
	$sql_inner = " AND not_user = '".$_SESSION["s_userid"]."'";
	} 



if ($_GET["table"] == "clients") {
	$table = $_GET["table"];	
	$sql_join = "LEFT JOIN clients ON note.not_row = clients.Client_ID ";
	$editlink = 'client_edit.php?cli_id=';
	}
elseif ($_GET["table"] == "property") {
	$table = $_GET["table"];	
	$sql_join = "LEFT JOIN property ON note.not_row = property.prop_ID  ";
	$editlink = 'property.php?propID=';
	}

$sql = "SELECT note.*,
admin.*,
$table.*,
date_format(note.not_date, '%a %D %b %y %H:%i') as date
FROM note 
LEFT JOIN admin ON note.not_user = admin.adm_id
$sql_join
WHERE 
not_table = '$table'
$sql_inner
ORDER BY not_date DESC 
";

$q = $db->query($sql);
if (DB::isError($q)) {  die("error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {

	if ($table == "clients") {
		$page_header = "Client Notes";
		$note_header = 'Client: '.$row["Name"].' (<a href="'.$editlink.$row["Client_ID"].'">'.$row["Client_ID"].'</a>)';
		$subject = $row["not_subject"];
		}
	elseif ($table == "property") {
		$page_header = "Property Notes";
		$note_header = 'Property: '.$row["Address1"].' (<a href="'.$editlink.$row["prop_ID"].'">'.$row["prop_ID"].'</a>)';
		$subject = $row["not_subject2"];
		}
	
	$notesTable .= '
	<tr>
	<td colspan="3">'.$note_header.'</td>
	</tr>
	<tr>
	<td><strong>'.$subject.'</strong> by <strong>'.$row["adm_name"].'</strong> on '.$row["date"].'</td>
	<td>Flag: <span class="flag'.$row["not_flag"].'"><img src="images/flag'.$row["not_flag"].'.gif" align="absmiddle">'.$row["not_flag"].'</span></td>
	<td>[ <a href="?page=3&amp;not_id='.$row["not_id"].'">Edit</a> ]</td>
	</tr>
	<tr>
	<td colspan="3">'.nl2br($row["not_note"]).'</td>
	</tr>
	<tr>
	<td colspan="3"><hr size="1"></td>
	</tr>
	';
	}


?>
<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th class="greyForm"><?php echo $page_header; ?> (<a href="?">back</a>)</th>
    </tr>
    <tr>
      <td>
	  <table width="100%" cellspacing="0" cellpadding="1">
	  <?php echo $notesTable; ?>	  
	  </table>
	  </td>
    </tr>
	</table>
<?php



} elseif ($_GET["page"] == 3 || $_POST["page"] == 3) {

if ($_POST["action"] == "update") {
	
	if ($_POST["not_subject"]) {
	$fieldnames[] = "not_subject";
	$newvalues[] = trim($_POST["not_subject"]);
	}
	if ($_POST["not_subject2"]) {
	$fieldnames[] = "not_subject2";
	$newvalues[] = trim($_POST["not_subject2"]);
	}
	
	$fieldnames[] = "not_note";
	$newvalues[] = trim($_POST["not_note"]);
	$fieldnames[] = "not_flag";
	$newvalues[] = trim($_POST["not_flag"]);
	
	queryLog($fieldnames,$newvalues,'note','not_id',$_POST["not_id"],'Update');
	
	echo '<p align="center">Changes saved, <a href="?page=3&amp;not_id='.$_POST["not_id"].'">click here to continue</a></p>';
	exit;
	}
	
else {	

	$sql_not = "SELECT * FROM note WHERE not_id = ".$_GET["not_id"];
	$q_not = $db->query($sql_not);
	if (DB::isError($q_not)) {  die("error: ".$q_not->getMessage()); }
	while ($row = $q_not->fetchRow()) {
		$table = $row["not_table"];
		$not_subject = $row["not_subject"];
		$not_subject2 = $row["not_subject2"];
		$not_note = $row["not_note"];
		$not_flag = $row["not_flag"];
		}	
		
	$render_editnote = '
	<form method="post" name="form">
  	<input type="hidden" name="page" value="3">  
 	<input type="hidden" name="not_id" value="'.$_GET["not_id"].'">
  	<input type="hidden" name="action" value="update">  
	<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr>
      <th colspan="2" class="greyForm">Notes (<a href="?">back</a>)</th>
    </tr>
	<tr>
	  <td width="20%" align="right" class="greyForm">Type</td>
      <td class="greyForm">
       ';
	   if ($table == "clients") {
	   		$render_editnote .= '<select name="not_subject">'.db_enum("note","not_subject",$not_subject);
			} elseif ($table == "property") {
	   		$render_editnote .= '<select name="not_subject2">'.db_enum("note","not_subject2",$not_subject2);
			};
			
	$render_editnote .= '   </select></td>
	</tr>
	<tr>
	  <td align="right" class="greyForm">Note</td>
	  <td class="greyForm"><textarea name="not_note" rows="4" id="newnote" style="width: 350px;">'.$not_note.'</textarea></td>
    </tr>
	<tr>
	  <td align="right" class="greyForm">Flag</td>
	  <td class="greyForm"><select name="not_flag">
       '.db_enum("note","not_flag",$not_flag).'
      </select></td>
	  </tr>
	<tr>
	  <td class="greyForm"><input type="hidden" name="not_id" value="'.$_GET["not_id"].'"></td>
	  <td class="greyForm"><input type="submit" name="SubmitNote" value="Save Changes"></td>
    </tr>
  </table>
  ';
	
	echo $render_editnote;
	
	// edit note page contains log of all changes
	
	$sqlLog = "SELECT *, date_format(changelog.cha_datetime, '%d/%m/%y %h:%i:%s') as cha_date 
	FROM changelog, admin 
	WHERE changelog.cha_user = admin.adm_id AND changelog.cha_table = 'note' AND changelog.cha_row = ".$_GET["not_id"]." 
	ORDER BY changelog.cha_datetime DESC";
		$qLog = $db->query($sqlLog);
		if (DB::isError($qLog)) {  die("error: ".$qLog->getMessage()); }
		$strRenderLog = '		
       	<table align="center" width="600" border="0" cellspacing="3" cellpadding="4">
    <tr> 
      <th colspan="5" class="greyForm">Log for this Note</th>
    </tr>
    <tr> 
	<td>
	<table width="100%" cellspacing="2" cellpadding="1">
		<tr>
		<td><strong>Date</strong></td>
		<td><strong>Field</strong></td>
		<td><strong>Old Value</strong></td>
		<td><strong>New Value</strong></td>
		<td><strong>User</strong></td>
		</tr>
		';
		while ($rowLog = $qLog->fetchRow()) {
		
			$cha_date = $rowLog["cha_date"];			
			
			$strRenderLog .= '<tr>
			<td>'.$cha_date.'</td>
			<td>'.str_replace("not_","",$rowLog["cha_field"]).'</td>
			<td><span title="'.$rowLog["cha_old"].'">'.strip_tags(substr($rowLog["cha_old"],0,25)).'</span></td>
			<td><span title="'.$rowLog["cha_new"].'">'.strip_tags(substr($rowLog["cha_new"],0,25)).'</span></td>
			<td>'.$rowLog["adm_name"].'</td>
			</tr>
			';
			}
		$strRenderLog .= '
		</table>
		</td>
		</tr>
		</table>';
		echo $strRenderLog;

	
	// end of edit note page
	
	
	}
	}
?>