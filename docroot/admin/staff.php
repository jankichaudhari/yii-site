<?php
session_start();
require("global.php"); 
require("secure.php"); 
$pageTitle = "Staff";


$sql = "SELECT * FROM staff WHERE Staff_Status = 'Current' AND Staff_Branch = 1 ORDER BY Staff_Fname";		
$q = $db->query($sql);
if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
$render = '
<table border="1" cellspacing="0" cellpadding="3" width="600">
<tr bgcolor="#E5E5E5">
<td><strong>Name</strong></td>
<td><strong>Mobile</strong></td>
<td><strong>Ext</strong></td>
<td><strong>Car</strong></td>
</tr>';
while ($row = $q->fetchRow()) {
	$render .= '<tr>
	<td>'.$row["Staff_Fname"].' '.$row["Staff_Sname"].'</td>
	<td>'.$row["Staff_Mobile"].'</td>
	<td>'.$row["Staff_Ext"].'</td>
	<td>'.$row["Staff_NumberPlate"].'</td>
	</tr>
	';
	}
$render .= '</table>

';


$sql = "SELECT * FROM staff WHERE Staff_Status = 'Current' AND Staff_Branch = 2 ORDER BY Staff_Fname";		
$q = $db->query($sql);
if (DB::isError($q)) {  die("error: ".$q->getMessage()); }
$render2 = '
<table border="1" cellspacing="0" cellpadding="3" width="600">
<tr bgcolor="#E5E5E5">
<td><strong>Name</strong></td>
<td><strong>Mobile</strong></td>
<td><strong>Ext</strong></td>
<td><strong>Car</strong></td>
</tr>
';
while ($row = $q->fetchRow()) {
	$render2 .= '<tr>
	<td>'.$row["Staff_Fname"].' '.$row["Staff_Sname"].'</td>
	<td>'.$row["Staff_Mobile"].'</td>
	<td>'.$row["Staff_Ext"].'</td>
	<td>'.$row["Staff_NumberPlate"].'</td>
	</tr>
	';
	}
$render2 .= '</table>';


echo html_header($pageTitle);
?>

<h2><?php echo $pageTitle;?></h2>
<h3>Camberwell</h3>
<table width="600"  border="1" cellspacing="0" cellpadding="2">
  <tr>
    <td width="50%"><strong>Sales</strong><br>
T: 020 7708 6700<br>
F: 020 7708 6701<br>
E: cam.sale@woosterstock.co.uk</td>
    <td width="50%"><strong>Lettings</strong><br>
T: 08456 800 460<br>
F: 08456 800 461<br>
E: cam.let@woosterstock.co.uk</td>
  </tr>
  <tr>
    <td valign="top"><strong>Production</strong><br>
T: 020 7708 6720<br>
T: 020 7708 6721<br>
E: production@woosterstock.co.uk</td>
    <td><strong>Property Management</strong><br>
T: 0871 598 0095<br>
F: 08456 800 461<br>
E: mgmt@woosterstock.co.uk</td>
  </tr>
</table>
<br>
<?php echo $render; ?>

<h3>Sydenham</h3>

<table width="600"  border="1" cellspacing="0" cellpadding="2">
  <tr>
    <td width="50%"><strong>Sales</strong><br>
      T: 020 8613 0060 <br>
      F: 020 8613 0070 <br>
      E: syd.sale@woosterstock.co.uk</td>
    <td width="50%"><strong>Lettings</strong><br>
      T: 08456 800 464<br>
      F: 08456 800 465<br>
      E: syd.let@woosterstock.co.uk</td>
  </tr>
</table>

<br>
<?php echo $render2; ?>

<h3>Transferring a Call</h3>
<p>To transfer a call, do not press hold, instead press the TRANSFER button and then dial the extension of the person 
(or group, see below) you want to put the call through to. Their phone will ring, when they pick up you can talk to 
them to say who is calling. If they want to accept the call, simply put your phone down and the call will be transferred.
If they do not want to take the call, they put the phone down and you are back through to the caller.</p>
<h3>Call interception</h3>
<p>It is possible to intercept any incoming call, be it internal or external. To do this, pick up you phone, dial 41 and then the extension you want to intercept. Example: If Luke's phone is ringing but he isn't there, pick up your phone, dial 41 202 and you will pick it up. Or, if the lettings phones are ringing but noone is in there, you can pick up that call by dialling 41 290 (290 being the lettings group extension).</p>
<h3>Caller Groups (Camberwell only) </h3>
<p>Example of how to use groups: You take a call and they ask to be put through to lettings. Instead of dialling an 
individual in lettings (who might not be at their desk), you can dial the lettings "group", which will ring on all 
phones in the lettings office. The first person to pick up gets the call.</p>

<h3>Group extensions (Camberwell only):</h3>
<table width="400">
<tr>
<td>Sales</td>
<td>289</td>
</tr>
<tr>
<td>Lettings</td>
<td>290</td>
</tr>
<tr>
<td>Management</td>
<td>291</td>
</tr>
<tr>
<td>Production</td>
<td>292</td>
</tr>
</table>
<h3>Door Phone  (Camberwell only) </h3>
<p>Three short rings on your phone, and the words &quot;DOOR PHONE 1&quot; on your screen means someone has pressed the door buzzer. Lift your handset, speak, to let them in press 5 on your phone </p>
</body>
</html>