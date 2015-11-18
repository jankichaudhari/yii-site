<?php
session_start();
require(dirname(__FILE__) . "/global.php");
require(dirname(__FILE__) . "/secure.php");
$sqlsales = "SELECT * FROM property
WHERE property.SaleLet = 1 AND (property.state_of_trade_id = 1 OR property.state_of_trade_id = 2)
ORDER BY property.Address1";
$q = $db->query($sqlsales);
//echo $sqlsales;
if (DB::isError($q)) {  die("error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
	$renderSalesList .= '<option value="'.$row["prop_ID"].'"';
	if ($row["state_of_trade_id"] == 2) {
		$renderSalesList .= ' class=selState2';
		}
	$renderSalesList .= '>'.$row["Address1"].' ('.$row["house_number"].')</option>';
	}

$sqllets = "SELECT * FROM property
WHERE property.SaleLet = 2 AND (property.state_of_trade_id = 1 OR property.state_of_trade_id = 2)
ORDER BY property.Address1";
$q = $db->query($sqllets);
if (DB::isError($q)) {  die("error: ".$q->getMessage()); }

while ($row = $q->fetchRow()) {
	$renderLettingsList .= '<option value="'.$row["prop_ID"].'"';
	if ($row["state_of_trade_id"] == 2) {
		$renderLettingsList .= ' class=selState2';
		}
	$renderLettingsList .= '>'.$row["Address1"].' ('.$row["house_number"].')</option>';
	}
$countsalepend = $db->getOne('SELECT COUNT(*) FROM property WHERE property.SaleLet = 1 AND property.state_of_trade_id = 11');
$countsaleproof = $db->getOne('SELECT COUNT(*) FROM property WHERE property.SaleLet = 1 AND property.state_of_trade_id = 12');
$countletpend = $db->getOne('SELECT COUNT(*) FROM property WHERE property.SaleLet = 2 AND property.state_of_trade_id = 11');
$countletproof = $db->getOne('SELECT COUNT(*) FROM property WHERE property.SaleLet = 2 AND property.state_of_trade_id = 12');

$pageTitle = "Admin Menu";
echo html_header($pageTitle);
?>
<script type="text/javascript">
function convert(form)
{
var feet;
var metric;
feet = eval(form.number.value);
metric = Math.round(feet * 30.48 * 100) * .01;

if (metric < 0)
     {
     metric = 0;
     }

form.centimeters.value = metric
form.meters.value = form.centimeters.value / 100
}
</script>
<style type="text/css">
<!--
.style1 {color: #00FF00}
-->
</style>
<center>
<h3><a href="/v3.0/live/admin">Click here to go to new system</a></h3>

  <table border="0" cellspacing="10" cellpadding="0" align="center">
    <tr>
      <td align="center" colspan="2">Logged in as <?php echo $_SESSION["s_name"]; ?>
        - <a href="logout.php">Click here to log out</a></td>
    </tr>
    <tr>
      <td width="50%" valign="top"> <table border="1" cellspacing="0" cellpadding="5" align="center" width="260" bordercolor="#990000">
          <tr>
            <th class="redForm">Sales</th>
          </tr>
          <tr>
            <td align="center"><a href="prop_search.php"><strong>Sales Property
              Search</strong></a></td>
          </tr>
          <tr>
            <form method="get" action="property.php">
              <td valign="top"><b><font color="#FF9900">List of Available Property</font></b><br>
                <select name="propID" style="width: 200px">
                  <?php echo $renderSalesList; ?> </select> <input type="submit" value=" Go "></td>
            </form>
          </tr>
          <tr>
            <form method="get" action="prop_search.php">
              <td valign="top"> <b><font color="#FF9900">Quick Search</font></b>
                <input type="hidden" name="action" value="Search"> <input type="hidden" name="Order" value="property.Dates DESC">
                <br> <input type="text" name="Keyword" style="width: 200px"> <input type="submit" value=" Go ">
              </td>
            </form>
          </tr>
          <tr>
            <td valign="top"><a href="property.php?SaleLet=1">Add New Sales Property</a></td>
          </tr>
          <tr>
            <td valign="top"><a href="prop_search.php?Order=property.Dates DESC&Status[]=11&Layout=plain&limit=25&action=Search">View
              Pending Sales Property</a> (<?php echo $countsalepend; ?>)</td>
          </tr>
          <tr>
            <td valign="top"><a href="prop_search.php?Order=property.Dates DESC&Status[]=12&Layout=plain&limit=25&action=Search">View
              Proofing Sales Property</a> (<?php echo $countsaleproof; ?>)</td>
          </tr>
        </table>
        <br> <table border="1" cellspacing="0" cellpadding="5" align="center" width="260" bordercolor="#990000">
          <tr>
            <td align="center"><a href="client_search.php"><strong>Sales Client
              Search</strong></a></td>
          </tr>
          <tr>
            <form name="ClientSearch" method="GET" action="client_search.php">
              <td> <b><font color="#FF9900">Search Clients</font></b> <input type="hidden" name="action" value="Search">
                <input type="hidden" name="PropType" value="Any"> <br> <input type="text" name="Keyword"  style="width: 200px">
                <input type="submit" value=" Go "> </td>
            </form>
          </tr>
          <tr>
            <td><a href="client.php?action=Clear&Sales=L">Add New Client</a></td>
          </tr>
        </table></td>
      <td width="50%" valign="top"><table border="1" cellspacing="0" cellpadding="5" align="center" width="260" bordercolor="#000066">
          <tr>
            <th class="blueForm">Lettings</th>
          </tr>
          <tr>
            <td align="center"><strong><a href="prop_search_lettings.php">Lettings
              Property Search</a></strong></td>
          </tr>
          <tr>
            <form name="EditProperty"  method="get" action="property.php">
              <td valign="top"> <b><font color="#FF9900">List of Available Property</font></b><br>
                <select name="propID" style="width: 200px">
                  <?php echo $renderLettingsList; ?> </select> <input type="submit" value=" Go ">
              </td>
            </form>
          </tr>
          <tr>
            <form name="EditProperty"  method="get" action="prop_search_lettings.php">
              <td valign="top"> <b><font color="#FF9900">Quick Search</font></b>
                <input type="hidden" name="action" value="Search"> <input type="hidden" name="Order" value="property.Dates DESC">
                <br> <input type="text" name="Keyword" style="width: 200px"> <input type="submit" value=" Go ">
              </td>
            </form>
          </tr>
          <tr>
            <td valign="top"><a href="property.php?SaleLet=2">Add New Lettings
              Property</a></td>
          </tr>
          <tr>
            <td valign="top"><a href="prop_search_lettings.php?Order=property.Dates DESC&Keyword=&Status[]=11&Layout=plain&action=Search">View
              Pending Lettings Property</a> (<?php echo $countletpend; ?>)</td>
          </tr>
          <tr>
            <td valign="top"><a href="prop_search_lettings.php?Order=property.Dates DESC&Keyword=&Status[]=12&Layout=plain&action=Search">View
              Proofing Lettings Property</a> (<?php echo $countletproof; ?>)</td>
          </tr>
        </table>
        <br> <table border="1" cellspacing="0" cellpadding="5" align="center" width="260" bordercolor="#000066">
          <tr>
            <td align="center"><a href="client_search_lettings.php"><strong>Lettings
              Client Search</strong></a></td>
          </tr>
          <tr>
            <form name="ClientSearch" method="GET" action="client_search_lettings.php">
              <td> <b><font color="#FF9900">Search Clients</font></b> <input type="hidden" name="action" value="Search">
                <input type="hidden" name="PropType" value="Any"> <br> <input type="text" name="Keyword"  style="width: 200px">
                <input type="submit" value=" Go "> </td>
            </form>
          </tr>
          <tr>
            <td><a href="client.php?action=Clear&Lettings=L">Add New Client</a></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td valign="top"> <table width="260" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#666666">
          <tr>
            <th class="greyForm">Links / Tools</th>
          </tr>
          <tr>
            <td><a href="note.php">Notes</a> (<font color=red>new</font>) </td>
          </tr>
          <tr>
            <td><a href="files.php">Send Files</a></td>
          </tr>
          <tr>
            <td><a href="advertising.php">Advertising</a></td>
          </tr>
          <tr>
            <td><a href="cards.php">Manage Cards</a></td>
          </tr>
          <tr>
            <td><a href="places.php?type=Station">List Train Stations</a><br>
              <a href="places.php?type=Tube">List Tube Stations</a></td>
          </tr>
          <tr>
            <td><a href="http://www.afd.co.uk/internet/online/PIOSearch1.php" target="_blank">Postcode
              Search</a></td>
          </tr>
        </table>
        <br> <table width="260" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#666666">
          <tr>
            <th class="greyForm">Stats</th>
          </tr>
          <tr>
            <td><a href="stats.php">View Statistics</a> </td>
          </tr>
          <tr>
            <td><a href="http://217.77.187.24">View NEW stats</a><br> <font size="1">domain:
              www.woosterstock.co.uk<br>
              user: stats<br>
              pass: figthedog</font> </td>
          </tr>
        </table></td>
      <td valign="top"> <table border="1" cellspacing="0" cellpadding="5" align="center" width="260" bordercolor="#666666">
          <tr>
            <th class="greyForm">Download</th>
          </tr>
          <tr>
            <td><a href="datafeeds/download.php">Prepare Download.txt</a></td>
          </tr>
        </table>
        <br> <table width="260" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#666666">
          <tr>
            <th class="greyForm">Documents</th>
          </tr>
          <tr>
            <td><a href="dealsheet/dealsheet.xls" target="_blank">Deal Sheet (Excel)</a> </td>
          </tr>
          <tr>
            <td><a href="/images/postcode-map2007.gif" target="_blank">Area Map / Branch Coverage</a></td>
          </tr>
          <tr>
            <td><a href="documents/">Documents, Forms &amp; Instructions</a></td>
          </tr>
          <tr>
            <td><a href="staff.php">Staff Mobile Numbers</a></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</center>
</body>
</html>