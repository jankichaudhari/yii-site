<?php
/*
$allowed_ip = array("80.46.83.190");
if (!in_array($_SERVER['REMOTE_ADDR'],$allowed_ip)) {
	header("Location:offline.php?ip=".$_SERVER['REMOTE_ADDR']);
	exit;
	}
*/

/*
global include for public site
*/
session_start();
// OTHER INCLUDES
require_once("db.inc.php");
require_once("format.inc.php");
require_once("map.class.inc.php");

// PATHS
/*
define(GLOBAL_URL,'http://www.woosterstock.co.uk/');
define(IMAGE_URL_PROPERTY,'http://www.woosterstock.co.uk/v3/images/p/');
define(IMAGE_URL_MAPPING, "http://www.woosterstock.co.uk/images/mapping/");
*/
define(GLOBAL_URL, 'http://new.wooster-1.titaninternet.co.uk/');
define(IMAGE_URL_PROPERTY, 'http://new.wooster-1.titaninternet.co.uk/v3/images/p/');
define(IMAGE_URL_MAPPING, "http://new.wooster-1.titaninternet.co.uk/images/mapping/");

// DATES
define(MYSQL_DATE_FORMAT, 'Y-m-d H:i:s');
$date_mysql = date(MYSQL_DATE_FORMAT);

// ERROR
function error_message($_errors, $_return = null)
{

	$_errorcount = count($_errors);

	if ($_errorcount == 1) {
		$_message = "<h1>An error has occoured</h1>";
	} else {
		$_message = "<h1>$_errorcount errors have occoured</h1>";
	}
	foreach ($_errors AS $key => $val) {
		$_message .= $val . "<br>\n";
	}
	/*
	for ($_n=0; $_n < $_errorcount; $_n++) {
		$_message .= $_errors[$_n]."<br>\n"; 
		}
	*/
	if ($_return) {
		$_link = urldecode($_return);
	} else {
		$_link = "javascript:history.back(1);";
	}
	$_message .= '<p>Please go <a href="' . $_link . '">back</a> and try again</p>';

	$_message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Error</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta http-equiv="content-language" content="en">
<meta http-equiv="pics-label" content=\'(pics-1.1 "http://www.icra.org/ratingsv02.html" comment "ICRAonline v2.0" l gen true for "http://www.woosterstock.co.uk"  r (nz 1 vz 1 lz 1 oz 1 cz 1) "http://www.rsac.org/ratingsv01.html" l gen true for "http://www.woosterstock.co.uk"  r (n 0 s 0 v 0 l 0))\'>
<meta name="description" content="Contact Wooster & Stock Estate Agents">
<meta name="keywords" content="property for sale, quay house, house, flat, estate agent, south london, buying property, selling property, stamp duty, peckham, camberwell, dulwich, brixton, buy to let, first time buyer">
<meta name="robots" content="index,follow">
<link rel="stylesheet" href="ws.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#0000CC" vlink="#0000CC" alink="#CC0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="765" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="256"><script type="text/javascript">document.write(\'<a href="/">\')</script><img src="/images/title_logo.gif" width="180" height="84" alt="Wooster &amp; Stock - London Estate Agents" border="0"><script type="text/javascript">document.write(\'</a>\')</script></td>
    <td width="509" align="center" nowrap><h1>South London Property Online</h1></td>
  </tr>
  <tr>
    <td colspan="2" background="/images/navbar_bg.gif" nowrap height="24">
		<table width="765" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td colspan="9" height="2"><img src="/images/topNav1.gif" width="765" height="2" alt=""></td>
        </tr>
        <tr> 
          <td width="104" class="topNav" height="20"><a href="http://www.woosterstock.co.uk/">Introduction</a></td>
          <td width="57" height="20" class="topNav"><a href="http://www.woosterstock.co.uk/Property.php">Sales</a></td>
          <td width="72" height="20" class="topNav"><a href="http://www.woosterstock.co.uk/Lettings.php">Lettings</a></td>
          <td width="77" height="20" class="topNav"><a href="http://www.woosterstock.co.uk/Register.php">Register</a></td>
          <td width="102" height="20" class="topNav"><a href="http://www.woosterstock.co.uk/Mortgages.php">Mortgages</a></td>
	  
	  <td width="118" height="20" class="topNav"><a href="http://www.woosterstock.co.uk/AreaOverview.php">Area&nbsp;Overview</a></td>
          <td width="89" height="20" class="topNav"><a href="http://www.woosterstock.co.uk/Valuations.php">Valuations</a></td>
          
         <td width="56" height="20" class="topNav"><a href="http://www.woosterstock.co.uk/Links.php">Links</a></td>
          <td width="90" height="20" class="topNav"><a href="http://www.woosterstock.co.uk/ContactUs.php">Contact&nbsp;Us</a></td>
        </tr>
        <tr> 
          <td colspan="9" height="2" bgcolor="#A4A4A4"><img src="/images/spacer.gif" width="1" height="2" alt=""></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td colspan="2" height="8"><img src="/images/spacer.gif" width="1" height="8" alt=""></td>
  </tr>
</table>
<table width="140" border="0" cellspacing="0" cellpadding="2" align="left">
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<table width="625" border="0" cellspacing="0" cellpadding="2" align="left">
  <tr> 
    <td valign="top" class="bodyTextbig"> 
     <div id="error">
	' . $_message . '
	</div>
    </td>
  </tr>
</table>
</body>
</html>';
	return $_message;
	unset($_errors, $_errorcount, $_n, $_message, $_return, $_link);
}

// EMAIL
$global_email = 'post@woosterstock.co.uk';

// using hotmail address in times of woe
//$use_alternate_email = 1;

function email_footer($_format, $_email, $_name = "NULL")
{

	if ($_name == "NULL") {
		$_recipient = $_email;
	} else {
		$_recipient = $_name . ' (' . $_email . ')';
	}
	$email_footer_html = '
<table width="600" border="0">
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr>
  <td colspan="3"><span style="font-family:Arial, Helvetica, sans-serif; font-size:15px; font-weight: bold; color:#666666">Wooster &amp; Stock</span></td>
</tr>
<tr>
  <td><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">
  <font color="#FF9900">woosterstock.co.uk</font></span></td>
  <td nowrap><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">Nunhead<br>
  Sales: 020 7708 6700<br>
  Lettings: 08456 800 460</span></td>
  <td nowrap><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">Sydenham<br>
	109 Kirkdale<br>
	London SE26 4QY<br>
  Sales: 020 8613 0060<br>
  Lettings: 08456 800 464</span></td>
</tr>
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr>
  <td colspan="3"><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#666666">This
email and any files transmitted with it are confidential and intended
solely for ' . $_recipient . '. If you are not the named addressee you should
not disseminate, distribute, copy or alter this email. Any views or
opinions presented in this email are solely those of the author and
might not represent those of Wooster &amp; Stock. Warning: Although
Wooster &amp; Stock has taken reasonable precautions to ensure no viruses
are present in this email, the company cannot accept responsibility
for any loss or damage arising from the use of this email or
attachments.</span></td>
</tr>	
</table>
</body>
</html>
';
	$email_footer_text = '
Wooster & Stock
woosterstock.co.uk

Nunhead
Sales: 020 7708 6700
Lettings: 08456 800 460

Sydenham
109 Kirkdale
London SE26 4QY
Sales: 020 8613 0060
Lettings: 08456 800 464

This email and any files transmitted with it are confidential and intended solely for ' . $_recipient . '.
If you are not the named addressee you should not disseminate, distribute, copy or alter this email. Any views or 
opinions presented in this email are solely those of the author and might not represent those of Wooster & Stock. 
Warning: Although Wooster & Stock has taken reasonable precautions to ensure no viruses are present in this email, 
the company cannot accept responsibility for any loss or damage arising from the use of this email or attachments.
';

	if ($_format == "html") {
		return $email_footer_html;
	} elseif ($_format == "text") {
		return $email_footer_text;
	}
	unset($_format, $_email, $_name, $_address);
} // end function

// MAPPING
// outer extremities of the cropped tiles, anything outside will use alternate map
$map_top    = 181500;
$map_right  = 549500;
$map_bottom = 165500;
$map_left   = 521500;

function GetTile($_intOS)
{ // input coords to get base tile filename
	//$GetTileCh4 = mid($_intOS,4,1);		
	$GetTileCh4 = substr($_intOS, 3, 1);
	if ($GetTileCh4 < 5) {
		$GetTileCh4 = 0;
	} else {
		$GetTileCh4 = 5;
	}
	$GetTileResult = substr($_intOS, 0, 3) . $GetTileCh4 . "00";
	return $GetTileResult;
}

?>