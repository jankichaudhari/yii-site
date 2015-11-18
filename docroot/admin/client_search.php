<?php
session_start();
$pageTitle = "Clients";
require("global.php"); 
require("secure.php"); 
include("DB/Pager.php");
if (DB::isError($con = DB::connect($dsn))){
    die (DB::errorMessage($con));
}

if ($_GET["action"] == "Search") {

$strPageLink = "?action=Search";
$sql = "SELECT *, date_format(clients.DateCreated,'%d/%m/%Y') as datecreate FROM clients WHERE ";

if ($_GET["Keyword"]) { 
	$sqlKeyword = " ( ";
	$arrayKeyword = explode(",",$_GET["Keyword"]);
	for ($i = 0; $i < count($arrayKeyword); $i++) { 
		$sqlKeyword .= " clients.Client_ID LIKE '%".trim($arrayKeyword[$i])."%' OR clients.Name LIKE '%".trim($arrayKeyword[$i])."%' OR clients.Email LIKE '%".trim($arrayKeyword[$i])."%' OR clients.Notes LIKE '%".trim($arrayKeyword[$i])."%' OR clients.Postcode LIKE '%".$arrayKeyword[$i]."%' OR "	;
		}
	$sqlKeyword = substr($sqlKeyword,0,-3);
	$sqlKeyword .= " ) AND ";
	$strPageLink .= "&Keyword=".$_GET["Keyword"];
	$sql .= $sqlKeyword;
	}

if ($_GET["Area"]) { 
	$sqlArea = " ( ";
	$arrayArea = $_GET["Area"];
	for ($i = 0; $i < count($arrayArea); $i++) { 
		$sqlArea .= " clients.Areas LIKE '%".$arrayArea[$i]."%' OR ";
		$strPageLink .= "&Area[]=".$arrayArea[$i];
		}
	$sqlArea = substr($sqlArea,0,-3);
	$sqlArea .= " ) AND ";
	$sql .= $sqlArea;
	}
	
if ($_GET["PropType"] && $_GET["PropType"] !== "Any") {
	$sql .= " (clients.PropertyType = '".$_GET["PropType"]."' OR clients.PropertyType = 'Any') AND ";
	$strPageLink .= "&PropType=".$_GET["PropType"];
	}

if (!$_GET["Status"]) { 
	$sql .= " clients.Status = 'L' AND ";
	}

if ($_GET["Price"]) {
$sql .= " clients.MinPrice <= ".$_GET["Price"]." AND clients.MaxPrice >=".$_GET["Price"]." AND ";
$strPageLink .= "&Price=".$_GET["Price"];
}

if ($_GET["Branch"]) {
$sql .= " clients.Branch LIKE '%".$_GET["Branch"]."%' AND ";
$strPageLink .= "&Branch=".$_GET["Branch"];
}

if ($_GET["neg"]) {
$sql .= " clients.neg = ".$_GET["neg"]." AND ";
$strPageLink .= "&neg=".$_GET["neg"];
}
/*
if ($_GET["PriceFrom"]) {
$sql .= " property.price >= ".$_GET["PriceFrom"]." AND ";
$strPageLink .= "&PriceFrom=".$_GET["PriceFrom"];
}

if ($_GET["PriceTo"]) {
$sql .= " property.price <= ".$_GET["PriceTo"]." AND ";
$strPageLink .= "&PriceTo=".$_GET["PriceTo"];
}
*/
if ($_GET["BedFrom"]) {
$sql .= " clients.Bedrooms >= ".$_GET["BedFrom"]." AND ";
$strPageLink .= "&BedFrom=".$_GET["BedFrom"];
}
/*
if ($_GET["BedTo"]) {
$sql .= " property.bedrooms <= ".$_GET["BedTo"]." AND ";
$strPageLink .= "&BedTo=".$_GET["BedTo"];
}
*/

if ($_GET["DateFrom"]) {
	$DateFromArray = explode("/",$_GET["DateFrom"]); 
	$strDateFrom = $DateFromArray[2]."-".$DateFromArray[1]."-".$DateFromArray[0]." 00:00:00";
	$sql .= " clients.DateCreated >= '".$strDateFrom."' AND ";
	$strPageLink .= "&DateFrom=".$_GET["DateFrom"];
	}

if ($_GET["DateTo"]) {
	$DateToArray = explode("/",$_GET["DateTo"]); 
	$strDateTo = $DateToArray[2]."-".$DateToArray[1]."-".$DateToArray[0]." 00:00:00";
	$sql .= " clients.DateCreated <= '".$strDateTo."' AND ";
	$strPageLink .= "&DateTo=".$_GET["DateTo"];
	}
	
if ($_GET["Selling"]) {	
	$sql .= " clients.Selling = '".$_GET["Selling"]."' AND ";
	$strPageLink .= "&Selling=".$_GET["Selling"];
	}
if ($_GET["Val"]) {	
	$sql .= " clients.Val = '".$_GET["Val"]."' AND ";
	$strPageLink .= "&Val=".$_GET["Val"];
	}
if ($_GET["Mortgage"]) {	
	$sql .= " clients.Mortgage = '".$_GET["Mortgage"]."' AND ";
	$strPageLink .= "&Mortgage=".$_GET["Mortgage"];
	}


if (!$_GET["Order"]) {
	$strOrderBy = "clients.DateCreated DESC";
	$strPageLinkORD = $strPageLink;
	$strPageLink .= "&Order=".$strOrderBy;
	}
else {
	$strOrderBy = $_GET["Order"];	
	$strPageLinkORD = $strPageLink;
	$strPageLink .= "&Order=".$strOrderBy;
	}

$sql .= "clients.Client_ID > 1 ORDER BY ".$strOrderBy;
echo '<!--'.$sql.'-->';



if (DB::isError($res = $con->query($sql))){
    die (DB::errorMessage($res));
}

$limit = 16;
$maxpages = 10; 
$pager = new DB_Pager ($res, $from, $limit);
$data = $pager->build();
if (DB::isError($data)){
    die (DB::errorMessage($data));
}
if (!$data) {
// no results

// pages table cells
$render_pages = '            
	  <tr> 
		<td class="podTextCenter" colspan="2"><b>Found 0 Properties</b></td>
	  </tr>
	  ';

// no results message
	$render = 'No results';
		
} else {



// direct links to page numbers:
foreach ($data['pages'] as $page => $start_row) {
	if ($page <> $data['current']) {
    $render_pages .= "[<a href=\"$strPageLink&from=$start_row\">$page</a>] ";
	} else {
	$render_pages .= "<strong>[$page]</strong> ";
	}
}

// pages table cells
$render_pages = '            
	  <tr> 
		<td class="podTextCenter" colspan="2"><b>Found '.$data['numrows'].' Client</b></td>
	  </tr>
	  <tr> 
		<td class="podTextCenter" colspan="2">Page: '.$render_pages.'</td>
	  </tr>'
	  ;



// Prev  and Next buttons
if ($from) { $prev = "<a href=\"$strPageLink&from=".$data['prev']."\">&lt Prev Page</a>";}
if ($data['next']) { $next = "<a href=\"$strPageLink&from=".$data['next']."\">Next Page &gt</a>";}

// start of table
$render = '		
        <table border="0" cellspacing="2" cellpadding="2" width="700">
		  <tr>
		    <td><a href="?">New Search</a> - '.$data['numrows'].' records found</td>
			<td align="right"><a href="index.php">Main Menu</a></td>
		  </tr>
          <tr> 
            <td align="left">Page: '.$data['current'].' of '.$data['numpages'].'</td>
            <td align="right" nowrap>'.$prev.' &nbsp; '.$next.'</td>
          </tr>
		  <tr>
		    <td colspan="2"><hr size="1"</td>
		  </tr>
        </table>
';

// loop property results
while ($row = $pager->fetchRow(DB_FETCHMODE_ASSOC)){
	if ($row["MinPrice"] == 0) {
		$MinPrice = "No Min";
		}
	else {
		$MinPrice = price_format($row["MinPrice"]);
		}
	if ($row["MaxPrice"] == 999999999) {
		$MaxPrice = "No Max";
		}
	else {
		$MaxPrice = price_format($row["MaxPrice"]);
		}

	$render .= '
        <table border="0" cellspacing="1" cellpadding="1" width="700">
          <tr> 
            <td width="350"><strong>'.$row['Name'].'</strong> - (<a href="mailto:'.$row["Email"].'">'.$row["Email"].'</a>)</td>
            <td align="right" width="100">PropType&nbsp;&nbsp;</td>
            <td>'.$row["PropertyType"].'</td>
            <td width="80">
			<a href="client_edit.php?cli_id='.$row["Client_ID"].'&searchLink='.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'">Edit</a> ::
			<a href="prop_search.php?action=Search&PriceFrom='.$row["MinPrice"].'&PriceTo='.$row["MaxPrice"].'&BedFrom='.$row["Bedrooms"].'&PropType='.$row["PropertyType"].'&Status[]=1&Status[]=2&Order=state_of_trade_id,Price&searchLink='.$PHP_SELF.'?'.urlencode($_SERVER['QUERY_STRING']).'">Match</a>

			</td>
          </tr>
          <tr> 
            <td>'.$row["house_number"].' '.$row['Address1'].', '.$row['Postcode'].'</td>
            <td align="right" width="100">PriceRange&nbsp;&nbsp;</td>
            <td>'.$MinPrice.' to '.$MaxPrice.'</td>
            <td width="80">'.$row["datecreate"].'</td>
          </tr>
		  <tr>
		    <td colspan="4"><hr size="1"</td>
		  </tr>
        </table>       
    ';
}




// add end of table

$render .= '		
        <table border="0" cellspacing="2" cellpadding="2" width="700">
          <tr> 
            <td align="left">Page: '.$data['current'].' of '.$data['numpages'].'</td>
            <td align="right" nowrap>'.$prev.' &nbsp; '.$next.'</td>
          </tr>
        </table>
';
}


echo html_header("Search Results");
echo '<div align="center">
<table>'.$render.'</table>
</div>';



}
else {
echo html_header("Client Search");

?>
<div align="center">
<form name="form" method="GET">
    <table width="650" border="0" cellpadding="5" cellspacing="3">
      <tr> 
        <td class="pageTitle">Sales Client Search</td>
        <td align="right"><a href="index.php">Main Menu</a></td>
      </tr>
    </table>

    <table width="650" border="0" cellpadding="3" cellspacing="2">
      <tr> 
        <td colspan="4" class="head">General search criteria</td>
      </tr>
      <tr>
        <td align="right" class="greyText">Keyword(s)</td>
        <td colspan="3" nowrap><input name="Keyword" type="text" value="" style="width:500px"> 
          <a href="#" title="separate multiple keywords with commas">[ ? ]</a> </td>
      </tr>
      <tr>
        <td align="right" class="greyText">Property&nbsp;Type</td>
        <td nowrap><select name="PropType" style="width:180px">
          <option value="" selected>Any</option>
          <option value="House">House</option>
          <option value="Apartment">Apartment</option>
          <option value="Commercial">Commercial</option>
          <option value="Live/Work">Live/Work</option>
        </select></td>
        <td align="right" class="greyText">Negotiator</td>
        <td><select name="neg" id="neg" style="width:180px">
          <option value="" selected>Any</option>
          <?php
			$sqlNeg = "SELECT * FROM staff  WHERE (Staff_Type = 'SalesNegotiator' OR Staff_Type = 'LettingsNegotiator') AND Staff_Status = 'Current' ORDER BY Staff_Fname";
			$qNeg = $db->query($sqlNeg);
			if (DB::isError($qNeg)) {  die("insert error: ".$qNeg->getMessage()); }
			
			while ($rowNeg = $qNeg->fetchRow()) {		
				echo '<option value="'.$rowNeg["Staff_ID"].'"';
				if ($intNeg == $rowNeg["Staff_ID"]) {
					echo ' selected';
					}

				echo '>'.$rowNeg["Staff_Fname"].' '.$rowNeg["Staff_Sname"].'</option>';

				}

			?>
        </select></td>
      </tr>
      <tr> 
        <td align="right" class="greyText">Price</td>
        <td nowrap><input name="Price" type="text" style="width:90px" onKeyPress="if (event.keyCode < 48 || event.keyCode > 58) event.returnValue = false;"> 
        </td>
        <td align="right" class="greyText">Branch</td>
        <td><span class="greyText">
          <select name="Branch" style="width:180px">
            <option value="" selected>Any</option>
            <?php
			$sqlBranch = "SELECT * FROM branch ORDER BY Branch_Title";
			$qBranch = $db->query($sqlBranch);
			if (DB::isError($qBranch)) {  die("insert error: ".$qBranch->getMessage()); }
			
			while ($rowBranch = $qBranch->fetchRow()) {		
				echo '<option value="'.$rowBranch["Branch_ID"].'"';
				if ($intBranch == $rowBranch["Branch_ID"]) {
					echo ' selected';
					}
				echo '>'.$rowBranch["Branch_Title"].'</option>';
				}
			?>
          </select>
        </span></td>
      </tr>
      <tr> 
        <td align="right" class="greyText">Bedrooms</td>
        <td nowrap><select name="BedFrom" style="width:90px">
          <option value="" selected>Any</option>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="8">8</option>
          <option value="9">9</option>
          <option value="10">10</option>
        </select></td>
        <td align="right" class="greyText">Sale Status</td>
        <td><select name="Selling" style="width: 180px">
          <option value=""></option>
		  <?php echo db_enum("clients","Selling"); ?>
        </select></td>
      </tr>
      <tr>
        <td align="right" class="greyText">Date&nbsp;Range</td>
        <td nowrap><input type="text" name="DateFrom"  style="width:90px" readonly=true onClick="popUpCalendar(this, form.DateFrom, 'dd/mm/yyyy')">
  to
    <input type="text" name="DateTo" style="width:90px" readonly=true onClick="popUpCalendar(this, form.DateTo, 'dd/mm/yyyy')"></td> 
        <td align="right" class="greyText">Valuation Required</td>
        <td class="greyText">
          <select name="Val" style="width: 180px">
          <option value=""></option>
            <?php echo db_enum("clients","Val"); ?>
          </select>
        </td>
      </tr>
      <tr> 
        <td colspan="2" align="center" class="greyText"><input name="Status" type="checkbox" id="Status" value="S">
Include Archived and Lettings Clients</td>
        <td align="right" nowrap class="greyText">Mortgage Status </td>
        <td nowrap>
          <select name="Mortgage" style="width: 180px">
          <option value=""></option>
            <?php echo db_enum("clients","Mortgage"); ?>
          </select>
        </td>
      </tr><tr> 
        <td height="30" colspan="4" align="center"><input type="submit" value="Search Clients"> 
          <input type="hidden" name="action" value="Search"> <input type="reset" value="Reset"></td>
      </tr>
	</table>
	<table width="650" border="0" cellpadding="3" cellspacing="2">
	  <tr>
	    <td class="head">Specific area(s)</td>
      </tr>
	  <tr>
	    <td>
		
		<table width="100%">
            <tr> 
              <?php
			$sqlArea = "SELECT area_ID, area_title, area_pc FROM area ORDER BY area_title";
			$qArea = $db->query($sqlArea);
			if (DB::isError($qArea)) {  die("insert error: ".$qArea->getMessage()); }
			
			while ($rowArea = $qArea->fetchRow()) {		
				echo '<td><input type="checkbox" name="Area[]" value="'.$rowArea["area_title"].'"';
				if ($intArea == $rowArea["area_ID"]) {
					echo ' checked';
					}
				echo '>'.$rowArea["area_title"].'</td>';							
			
				$i++;
				if ($i % 4 == 0) 
					{
					echo '
					</tr>
					<tr>';
					}
				}
			?>
            </tr>
          </table>
		</td>
	  </tr><tr> 
        <td height="30" colspan="4" align="center"><input type="submit" value="Search Clients"> 
          <input type="hidden" name="action" value="Search"> <input type="reset" value="Reset"></td>
      </tr>
	</table>



        <table width="650" border="0" cellpadding="3" cellspacing="2">

    <tr> 

      <td colspan="2" class="head">Display Options</td>

    </tr>

    <tr> 

        <td valign="top">Order by</td>

        <td><select name="Order" style="width:200">

          <option value="clients.DateCreated DESC">Date (newest first)</option>

          <option value="clients.DateCreated ASC">Date (newest last)</option>

          <option value="clients.Name ASC">Name (a - z)</option>

          <option value="clients.Name DESC">Name (z - a)</option>

        </select></td>

    </tr>

  </table>

</form>



</div>

</body>

</html>

<?php } ?>