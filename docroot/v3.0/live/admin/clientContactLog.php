<?php
/* 
added 16/02/10
client contact log view
*/



require_once("inx/global.inc.php");

// get existing values
if ($_GET["cli_id"]){ 
	$cli_id = $_GET["cli_id"];
	} elseif ($_POST["cli_id"]) {
	$cli_id = $_POST["cli_id"];
	} else {
	die("No client specified");
	}



// save log from ajax
if ($_POST) {
	
	$db_data["userId"] = $_SESSION["auth"]["use_id"];
	$db_data["date"] = date('Y-m-d H:i:s');
	$db_data["clientId"] = $_POST["cli_id"];
	$db_data["comment"] = $_POST["comment"];
	$db_data["method"] = $_POST["contactMethod"];	
	db_query($db_data,"INSERT","contactLog","id");
	exit;	
	}





$sql = "SELECT 
contactLog.*,
CONCAT(user.use_fname,' ',user.use_sname) as use_name,CONCAT(LEFT(user.use_fname,1),LEFT(user.use_sname,1)) as use_initial,use_colour
FROM contactLog 
LEFT JOIN user ON contactLog.userId = user.use_id
WHERE clientId = $cli_id ORDER BY contactLog.date DESC";
$q = $db->query($sql);

while ($row = $q->fetchRow()) {	
	$contactLogRender .= '
<tr>
<td style="width:120px">'.date('jS M Y H:i',strtotime($row["date"])).'</td>
<td style="width:30px">'.$row["use_initial"].'</td>
<td style="width:70px">'.$row["method"].'</td>
<td>'.$row["comment"].'</td>
</tr>';
	}
if ($contactLogRender) {
	$contactLogRender = '<table class="log" cellpadding="3" cellspacing="0" width="100%">'.$contactLogRender.'</table>';
	} else {
	$contactLogRender = '<p>(no data)</p>';
	}
	
?>

<script>


jQuery(function($){


	$('#theForm').submit(function(){ 
		//contactMethod = $('input[name$=contactMethod]').val();
		contactMethod = $(":radio[name='contactMethod'][checked]").val() 
		comment = $('#comment').val();
		cli_id = $('#cli_id').val();
		
		$.ajax({
			type: "POST",
			url: "clientContactLog.php",
			data: "contactMethod="+contactMethod+"&comment="+comment+"&cli_id="+cli_id,
			success: function(returned){	
				$("#TB_window").fadeOut("fast",function(){$('#TB_window,#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove();});
				}
			});			
		
		return false
		})
	})



 
</script>
<style>
table.log td {
	vertical-align:top;
	border-top:1px dotted #CCC;
	border-left:1px dotted #CCC;
	}
</style>
<table cellpadding="3" cellspacing="0" width="100%">
<tr>
<th style="width:120px">Date</th>
<th style="width:30px">User</th>
<th style="width:70px">Method</th>
<th>Comment</th>
</tr>
</table>
<div style="height:250px;overflow:auto;border-bottom:1px dotted #CCC;margin-bottom:30px;">
<?php echo  $contactLogRender; ?>
</div>


<h3 style="margin-bottom:10px;">Log new contact</h3>

<form id="theForm">
<table width="100%">
<tr>
<td>Contact Method</td>
<td>
<input type="radio" name="contactMethod" value="Telephone" checked="checked">Telephone
<input type="radio" name="contactMethod" value="Email">Email
<input type="radio" name="contactMethod" value="Other">Other
</td>
</tr>
<tr>
<td>Comment</td>
<td><textarea id="comment" style="width:500px;"></textarea></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" value="Save" /></td>
</tr>
</table>
<input type="hidden" id="cli_id" value="<?php echo $cli_id; ?>" />
</form>