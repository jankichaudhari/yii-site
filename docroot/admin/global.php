<?php
require_once(dirname(__FILE__) . "/../../config/config.admin.inc.php");
if (!isset($PHPSESSID) || !$PHPSESSID) {
	$PHPSESSID = session_id();
}
// Check HTTPS is on, if not redirect to same page with HTTPS. 10/12/05

if (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on') {
//		 echo "<pre>";
//		 print_r('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//		 echo "</pre>";
//		 exit;
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit();
}

/*
$dsn = array(
	'phptype'  => "mysql",
	'database' => "ws_db",
	'username' => "root",
	'password' => "changeoninstall"//"a345uyv"
);
*/

include("DB.php");
$db = DB::connect($dsn);
if (DB::isError($db)) {
	die("connection error: " . $db->getMessage());
}
$db->setFetchMode(DB_FETCHMODE_ASSOC);

function db_enum($_table, $_field, $_pick = null, $_order = null)
{

	$_render    = "";
	$_query     = "describe $_table $_field";
	$_sqlresult = mysql_query($_query);
	$_sqlrow    = mysql_fetch_array($_sqlresult);
	$_value     = $_sqlrow["Type"];
	preg_match_all("/'([^']+)'/", $_value, $_matches, PREG_SET_ORDER);
	foreach ($_matches as $_v) {
		$_render .= "<option value=\"" . $_v[1] . "\"";
		if ($_v[1] == $_pick) {
			$_render .= " selected";
		}
		$_render .= ">" . $_v[1] . "</option>\n";
	}
	return $_render;
	unset($_table, $_where, $_query, $_sqlresult, $_sqlrow, $_v, $_value, $_matches, $_render);

}

// website default email (used in MailTo:)
$global_email = "post@woosterstock.co.uk";
$admin_email  = "vitaly@woosterstock.co.uk";

// additional security measure
$auth_secret = "hl_+ed7nqa728jew2.32..2";

// mapping defaults
$default_location_x = 534500;
$default_location_y = 176500;

// image sizes
$image_main_w        = 400;
$image_main_h        = 400;
$image_thumb1_w      = 146;
$image_thumb1_h      = 146;
$image_thumb2_w      = 56;
$image_thumb2_h      = 56;
$image_internal_w    = 200;
$image_internal_h    = 200;
$floorplan_max_width = 750;

$dateToday      = date('Y-m-d H:i:s');
$dateAccess     = date('d/m/Y'); // access short date friendly
$dateFriendly   = date('d/m/Y H:i');
$dateLong       = date('jS F Y'); // fiendly date format, used on printed details
$dateLast7Days  = date("Y-m-d H:i:s", strtotime(date('Y-m-j H:i:s')) - (3 * 7 * 24 * 60 * 60));
$dateLast20Mins = date("Y-m-d H:i:s", strtotime(date('Y-m-j H:i:s')) - (10 * 60));
$dateFile       = date('YmdHis'); // user for image naming

//$image_folder = "http://www.woosterstock.co.uk/customerPages/images/";
$image_folder = WS_URL_IMAGES . "/property/property/";
$map_folder   = "https://" . WS_HOSTNAME . "/images/mapping/";
//$image_folder = "/images/property/";
$image_path = WS_PATH_IMAGES;
/*    
* $mode is a string with values:    
*         - 'uniq' -> Unique file name (42341242353451.jpg)    
*         - 'safe' -> A Unix safe name. If real name is: 'p �\!.j pg'    
*                        will be converted to 'p_a__.j_pg'    
*        - 'real' -> The file name the user submits (not very secure)    
*        - other values -> the destination file name you want    
*    
* $prepend a string to prepend to the file name    
* $append  a string to append to the file name    
*/
$mode          = 'safe';
$prepend       = $dateFile . '_'; // prepend string to filename, this should be a user prefix
$append        = ''; // append to filename, not used yet
$uploadPath    = '../images/property'; // upload path
$MAX_FILE_SIZE = '21000000';

// people who are allowed to release property
$proofers[] = "1"; // mark
$proofers[] = "4"; // gemma (temporary)

// people who are allowed to send mailshots
$emailers[] = "1"; // mark
$emailers[] = "14"; // becky
$emailers[] = "4"; // gemma
$emailers[] = "5"; // bella
$emailers[] = "34"; // nicky

function format_filesize($_size)
{

	// First check if the file exists.
	//if(!is_file($file)) exit("File does not exist!");
	// Setup some common file size measurements.
	$_kb = 1024; // Kilobyte
	$_mb = 1048576; // Megabyte
	$_gb = 1073741824; // Gigabyte
	$_tb = 1099511627776; // Terabyte

	// Get the file size in bytes.
	//$size = filesize($file);
	/* If it's less than a kb we just return the size, otherwise we keep going until
		the size is in the appropriate measurement range. */
	if ($_size < $_kb) {
		return $_size . " B";
	} else {
		if ($_size < $_mb) {
			return round($_size / $_kb, 2) . " KB";
		} else {
			if ($_size < $_gb) {
				return round($_size / $_mb, 2) . " MB";
			} else {
				if ($_size < $_tb) {
					return round($_size / $_gb, 2) . " GB";
				} else {
					return round($_size / $_tb, 2) . " TB";
				}
			}
		}
	}
	unset($_size, $_kb, $_mb, $_gb, $_tb);
}

function validate_email($email)
{

	return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function html_header($_title = "Wooster & Stock")
{

	$_html = '<html>
<head>
<title>' . $_title . '</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="/admin/adminStyles.css" type="text/css">
<script LANGUAGE="JavaScript" type="text/javascript" src="adminScripts.js"></script>
<script LANGUAGE="JavaScript" type="text/javascript" src="fckeditor/fckeditor.js"></script>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript" SRC="js/popcalendar.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript" SRC="js/lw_layers.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript" SRC="js/lw_menu.js"></SCRIPT>
<script LANGUAGE="JavaScript" type="text/javascript" src="js/ieSpell.js"></script>
</head>

<body bgcolor="#FFFFFF">
';
	return $_html;
	unset($_title, $_html);
}

function error_message($_errors)
{

	$_errorcount = count($_errors);
	if ($_errorcount == 1) {
		$_message = "<p>$_errorcount error has occoured</p>";
	} else {
		$_message = "<p>$_errorcount errors have occoured</p>";
	}
	for ($_n = 0; $_n < $_errorcount; $_n++) {
		$_message .= $_errors[$_n] . "<br>\n";
	}
	$_message .= "<p>Please go <a href=\"javascript:history.back(1);\">back</a> and try again</p>";
	return $_message;
	unset($_errors, $_errorcount, $_n, $_message);
}

// record database changes
// works on update querys only, and requires the $sql body between the SET and the WHERE
function change_log($cha_user = 0, $cha_table, $cha_field, $cha_row, $cha_sql, $cha_session)
{

	// split $sql
	$cha_sql_split = $cha_sql;
	// took this out as it altered the description and notes
	//$cha_sql_split = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/","",$cha_sql);
	$cha_sql_split = explode(",", $cha_sql_split);
	$cha_sql_count = count($cha_sql_split);
	for ($cha_i = 0; $cha_i < $cha_sql_count; $cha_i++) {
		$cha_split = explode("=", $cha_sql_split[$cha_i]);
		$cha_fields .= trim($cha_split[0]) . "|";
		$cha_values .= trim($cha_split[1]) . "|";
	}
	//echo $cha_fields;
	// comma seperate list of database field names
	$cha_columns = removeCharacter($cha_fields, "|");
	// comma seperated list of current values for above field names
	$cha_values = removeCharacter($cha_values, "|");
	//echo "<p><b>new values</b>$cha_values</p>";

	// split both into arrays to loop through
	$cha_columns_array = explode("|", $cha_columns);
	$cha_values_array  = explode("|", $cha_values);
	//print_r($cha_columns_array);
	// compare number of fields to columns
	if (count($cha_columns_array) <> count($cha_values_array)) {
		echo "number of fields and values do not match";
		exit;
	} else {
		$cha_columns_count = count($cha_columns_array);
	}

	// select current values
	$cha_sql2 = "SELECT " . str_replace("|", ",", $cha_columns) . " FROM " . $cha_table . " WHERE " . $cha_field . " = " . $cha_row . "";
	//echo "<p>SELECT: $cha_sql2</p>";
	$cha_result2 = mysql_query($cha_sql2);
	if (!$cha_result2) {
		die("MySQL Error:  " . mysql_error() . $cha_sql2);
	}

	while ($cha_row2 = mysql_fetch_array($cha_result2)) {
		for ($cha_i = 0; $cha_i < $cha_columns_count; $cha_i++) // loop through array of fields
		{
			$cha_array_field[]   = $cha_columns_array[$cha_i]; //mysql_field_name($cha_result2, $cha_i); //$cha_fields
			$cha_array_current[] = $cha_row2[$cha_i];
			$cha_new_value       = str_replace("'", "", $cha_values_array[$cha_i]);
			// testing
			/*
			echo "old value: ".$cha_array_current[$cha_i]."<br>
			new value: ".$cha_new_value."<br>
			field: ".$cha_array_field[$cha_i]."<p>";
			*/

			// compare old and new values, if different, insert into changelog
			if ($cha_row2[$cha_i] <> $cha_new_value) {
				//$cha_render .= "<p>".mysql_field_name($cha_result2, $cha_i)." has changed from ".$row[$i]." to ".$cha_values_array[$cha_i]."</p>";
				$cha_old_value = addslashes(substr($cha_row2[$cha_i], 0, 250));
				$cha_new_value = addslashes(substr($cha_new_value, 0, 250));
				$sqlChangeLog  = "INSERT INTO changelog
				(cha_user,cha_session,cha_table,cha_field,cha_row,cha_old,cha_new)
				VALUES 
				('$cha_user','$cha_session','$cha_table','$cha_array_field[$cha_i]','$cha_row','$cha_old_value','$cha_new_value')
				";
				//echo $sqlChangeLog;
				mysql_query($sqlChangeLog) or die ("Error in ChangeLog Query: " . mysql_error() . "\n" . $sqlChangeLog);
			}
		}
	}
	return $cha_render;
}

function price_format($_price_input = "0", $_price_decimal = "False", $_price_currency = "&pound;")
{

	if ($_price_decimal == "True") {
		$_price_output = $_price_currency . number_format($_price_input, 2, '.', ',');
	} else {
		$_price_output = $_price_currency . number_format($_price_input);
	}
	return $_price_output;
	unset($_price_input, $_price_decimal, $_price_currency, $_price_output);
}

function strip_html($_str)
{

	return htmlentities(htmlspecialchars(strip_tags($_str)));
}

function makeRandomPassword()
{

	$salt = "abchefghjkmnpqrstuvwxyz0123456789";
	srand((double)microtime() * 1000000);
	$i = 0;
	while ($i <= 7) {
		$num  = rand() % 33;
		$tmp  = substr($salt, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	return $pass;
}

// returns nearest tile image name, 500 mtrs to a tile
function format_tile($_os)
{

	// need to devise way of handling multiple scales
	$_os = (floor($_os / 500) * 500);
	return $_os;
}

// convert weekly price to monthly
function pw2pcm($_price_input = "0")
{

	$_price_output = round(($_price_input * 52) / 12);
	return $_price_output;
	unset($_price_input, $_price_output);
}

// convert monthly price to weekly
function pcm2pw($_price_input = "0")
{

	$_price_output = round(($_price_input / 52) * 12);
	return $_price_output;
	unset($_price_input, $_price_output);
}

// format specific to strap line, proper case
function format_strap($_str = null)
{

	$_str = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $_str);
	$_str = trim($_str);
	$_str = strtolower($_str);
	$_str = ucfirst(str_replace(array(
									 "Of ", "A ", "The ", "And ", "An ", "Or ", "Nor ", "But ", "If ", "Then ", "Else ",
									 "When ", "Up ", "At ", "From ", "By ", "On ", "Off ", "For ", "In ", "Out ",
									 "Over ", "To ", "With ", "This ", "Within ", "Plus ", "Arranged ", "As ", "Be ",
									 "Into ", "Is "
								), array(
										"of ", "a ", "the ", "and ", "an ", "or ", "nor ", "but ", "if ", "then ",
										"else ", "when ", "up ", "at ", "from ", "by ", "on ", "off ", "for ", "in ",
										"out ", "over ", "to ", "with ", "this ", "within ", "plus ", "arranged ",
										"as ", "be ", "into ", "is "
								   ), ucwords(strtolower($_str))));
	$_str = str_replace("Osp", "OSP", $_str);
	$_str = str_replace("Chain Free", "CHAIN FREE", $_str);
	$_str = str_replace("ii", "II", $_str);
	$_str = str_replace("Ii", "II", $_str);
	$_str = str_replace("& ", "&amp; ", $_str);
	$_str = str_replace("Live/work", "Live/Work", $_str);
	$_str = str_replace("off Street", "Off Street", $_str);
	//$_str = str_replace(",","&#44;",$_str);
	return $_str;
	unset($_str);
}

// format specific to street, capitalised
function format_street($_str = null)
{

	$_str = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $_str);
	//$_str = str_replace(",","&#44;",$_str);
	$_str = trim($_str);
	$_str = ucwords($_str);
	return $_str;
	unset($_str);
}

// format specific to street, capitalised
function format_postcode($_str = null)
{

	$_str = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $_str);
	//$_str = str_replace(",","&#44;",$_str);
	$_str = trim($_str);
	$_str = strtoupper($_str);
	return $_str;
	unset($_str);
}

function format_description($_str = null)
{

	$_str = strip_tags($_str, '<p></p><li></li><a></a><br><br/><br /><em></em><strong></strong>');
	$_str = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $_str);
	if (substr($_str, 0, 3) !== "<p>") {
		$_str = '<p>' . $_str;
	}
	$_str = str_replace("'", "&#039;", $_str);
	$_str = str_replace("&acirc;&euro;&trade;", "&#039;", $_str);
	$_str = str_replace("&acirc;&euro;&tilde;", "&#039;", $_str);
	$_str = str_replace("&acirc;&euro;&ldquo;", "-", $_str);
	$_str = str_replace("&Atilde;&copy;", "&eacute;", $_str);
	$_str = str_replace("&Acirc;", "", $_str);
	$_str = str_replace("&acirc;&euro;&oelig;", "", $_str);
	$_str = str_replace("&acirc;&euro;?", "", $_str);
	$_str = str_replace("<ul>", "", $_str); // remove <ul> to prevent indent of bullet lists
	$_str = str_replace("</ul>", "", $_str);
	$_str = str_replace("<p>&nbsp;</p>", "", $_str);
	$_str = str_replace("<br/><br/>", "</p><p>", $_str);
	#$_str = str_replace("<br/>","",$_str);
	$_str = str_replace("&nbsp;", " ", $_str);
	$_str = trim($_str);
	return $_str;
	unset($_str);
}

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

function make_thumb1($_filename)
{ // medium thumbnail
	$_file = $_filename;
	//$_file = str_replace("ftx.",".",$_file);
	$_file = str_replace(".jpg", "x.jpg", $_file);
	return $_file;
}

function make_thumb2($_filename)
{ // small thumbnail
	$_file = $_filename;
	//$_file = str_replace("ftxx.",".",$_file);
	$_file = str_replace(".jpg", "xx.jpg", $_filename);
	return $_file;
}

function get_thumb1($_filename)
{ // medium thumbnail
	$_file = $_filename;
	//$_file = str_replace("ftx.",".",$_file);
	$_file = str_replace(".jpg", "x.jpg", $_file);
	return $_file;
}

function get_thumb2($_filename)
{ // small thumbnail
	$_file = $_filename;
	//$_file = str_replace("ftxx.",".",$_file);
	$_file = str_replace(".jpg", "xx.jpg", $_filename);
	return $_file;
}

function removeCharacter($whichData, $whichString)
{

	$whichData = trim($whichData);
	if (substr($whichData, strlen($whichData) - 1) == $whichString) {
		$whichData = substr($whichData, 0, strlen($whichData) - 1);
	}
	return $whichData;
}

// copy all files in a folder to ftp site
function ftp_copy($src_dir, $dst_dir)
{

	global $conn_id;
	$d = dir($src_dir);
	while ($file = $d->read()) {
		if ($file != "." && $file != "..") {
			if (is_dir($src_dir . "/" . $file)) {
				if (!@ftp_chdir($conn_id, $dst_dir . "/" . $file)) {
					ftp_mkdir($conn_id, $dst_dir . "/" . $file);
				}
				ftp_copy($src_dir . "/" . $file, $dst_dir . "/" . $file);
			} else {
				$upload = ftp_put($conn_id, $dst_dir . "/" . $file, $src_dir . "/" . $file, FTP_BINARY);
			}
		}
	}
	$d->close();
}

// delete folder and all contents
function delDir($dirName)
{

	if (empty($dirName)) {
		return;
	}
	if (file_exists($dirName)) {
		$dir = dir($dirName);
		while ($file = $dir->read()) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dirName . '/' . $file)) {
					delDir($dirName . '/' . $file);
				} else {
					@unlink($dirName . '/' . $file) or die('File ' . $dirName . '/' . $file . ' couldn\'t be deleted!');
				}
			}
		}
		@rmdir($dirName . '/' . $file) or die('Folder ' . $dirName . '/' . $file . ' couldn\'t be deleted!');
	} else {
		echo 'Folder "<b>' . $dirName . '</b>" doesn\'t exist.';
	}
}

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
  <td colspan="3"><span style="font-family:Arial, Helvetica, sans-serif; font-size:15px; font-weight: bold; color:#666666">Wooster &amp; Stock </span></td>
</tr>
<tr>
  <td><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">
  <font color="#FF9900">woosterstock.co.uk</font></span></td>
  <td nowrap><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">Camberwell<br>
	Nunhead
  020 7708 6700</span></td>
  <td nowrap><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">Sydenham<br>
	109 Kirkdale, Sydenham<br>
	London SE26 4QY<br>
  020 8613 0060</span></td>
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
Wooster &amp; Stock
www.woosterstock.co.uk

Nunhead
020 7708 6700

Sydenham
109 Kirkdale
London SE26 4QY
020 8613 0060

This email and any files transmitted with it are confidential and intended solely for ' . $_recipient . '.
If you are not the named addressee you should not disseminate, distribute, copy or alter this email. Any views or 
opinions presented in this email are solely those of the author and might not represent those of Wooster &amp; Stock. 
Warning: Although Wooster &amp; Stock has taken reasonable precautions to ensure no viruses are present in this email, 
the company cannot accept responsibility for any loss or damage arising from the use of this email or attachments.
';

	if ($_format == "html") {
		return $email_footer_html;
	} elseif ($_format == "text") {
		return $email_footer_text;
	}
	unset($_format, $_email, $_name, $_address);
} // end function
