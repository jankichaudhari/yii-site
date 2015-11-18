<?php
function check_email($email)
{
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// check array of required GET or POST values (not finished)
function checkRequiredItems($_items, $method = 'GET')
{
	if (is_array($_items)) {
		foreach ($_items as $item=> $label) {

			if (!$_GET[$item]) {
				$errors[] = $label . ' is missing';
			}
		}
	}
	if ($errors) {
		return $errors;
	} else {
		return false;
	}
}

// takes simple array and return delimited string
function array2string($_input, $delimiter = '|')
{
	if (is_array($_input)) {
		foreach ($_input as $key=> $val) {
			$_output .= $val . $delimiter;
		}
		$_output = remove_lastchar($_output, $delimiter);
	}
	return $_output;
}

// removes a key and value from supplied querystring
function replaceQueryString($query_string, $var)
{
	return preg_replace("/$var=[\\d\\w\\+]*/", "", $query_string);
	// return preg_replace("/$var=[\\d\\w]*/","$var=$val",$query_string);
}

// removes keys and values from supplied querystring
function replaceQueryStringArray($query_string, $to_remove)
{

	// we are only interested in anything after the ?, if there is one.
	if (strstr($query_string, '?')) {
		$parts        = explode('?', $query_string);
		$filename     = $parts[0];
		$query_string = $parts[1];
	}

	$query_string = str_replace('&amp;', '&', $query_string);
	parse_str($query_string, $haystack);

	foreach ($haystack AS $key=> $val) {
		if (in_array($key, $to_remove)) {
			unset($haystack[$key]);
		} else {
			$output .= $key . '=' . $val . '&amp;';
		}
	}

	if ($filename) {
		$filename .= '?';
	}
	return $filename . remove_lastchar($output, "&amp;"); //http_build_query($haystack);
}

function join_arrays($_input)
{
	$_output = array();
	foreach ($_input AS $_array) {
		if (is_array($_array)) {
			$_output = $_output + $_array;
		}
	}
	return $_output;
}

//////////////////////////////////////////////////////////////////////
// STRINGS
//////////////////////////////////////////////////////////////////////

// return only a-z, A-Z
function characters_only($_input = NULL)
{
	$_output = preg_replace('/[^a-zA-Z_]*/', '', $_input);
	return $_output;
	unset($_input, $_output);
}

// profanity, case-insensitive
function profanity_filter($_str)
{
	$_str = str_ireplace(
		array('fuck', 'cunt', 'shit'),
		array('****', '****', '****'),
		$_str
	);
	return $_str;
}

// format data for use in db_query and other functions
function format_data($_str = null)
{
	return is_numeric($_str) ? $_str : mysql_real_escape_string($_str);
}

// format specific to strap line, proper case
function format_strap($_str = null)
{
	$_str = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $_str);
	$_str = trim($_str);
	$_str = strtolower($_str);
	$_str = ucfirst(str_replace(
			array("Of ", "A ", "The ", "And ", "An ", "Or ", "Nor ", "But ", "If ", "Then ", "Else ", "When ", "Up ", "At ", "From ", "By ", "On ", "Off ", "For ", "In ", "Out ", "Over ", "To ", "With ", "This ", "Within ", "Plus ", "Arranged ", "As "),
			array("of ", "a ", "the ", "and ", "an ", "or ", "nor ", "but ", "if ", "then ", "else ", "when ", "up ", "at ", "from ", "by ", "on ", "off ", "for ", "in ", "out ", "over ", "to ", "with ", "this ", "within ", "plus ", "arranged ", "as "),
			ucwords(strtolower($_str)))
	);
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
	//$_str = addslashes($_str);
	$_str = trim($_str);
	$_str = ucwords($_str);
	return $_str;
	unset($_str);
}

// format specific to postcode, capitalised (should add validation here too)
function format_postcode($_str = null)
{
	$_str = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $_str);
	//$_str = str_replace(",","&#44;",$_str);
	$_str = trim($_str);
	$_str = strtoupper($_str);
	$_str = str_replace(" ", "", $_str);

	if (strlen($_str) == 5) {
		$_str = substr($_str, 0, 2) . ' ' . substr($_str, 2);
	}
	elseif (strlen($_str) == 6) {
		$_str = substr($_str, 0, 3) . ' ' . substr($_str, 3);
	}
	elseif (strlen($_str) == 7) {
		$_str = substr($_str, 0, 4) . ' ' . substr($_str, 4);
	}

	return $_str;
	unset($_str);
}

// formatting for description field, strips unwanted tags and odd characters
function format_description($_str = null)
{
	$_str = strip_tags($_str, '<p></p><li></li><ul></ul><a></a><br><br/><br /><em></em><strong></strong>');
	$_str = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $_str);
	if (substr($_str, 0, 3) !== "<p>") {
		$_str = '<p>' . $_str;
	}
	$_str = str_replace("|", " ", $_str); // delimiters used in datafeeds
	$_str = str_replace("^", " ", $_str); // delimiters used in datafeeds
	$_str = str_replace("'", "&#039;", $_str);
	$_str = str_replace("&acirc;&euro;&trade;", "&#039;", $_str);
	$_str = str_replace("&acirc;&euro;&tilde;", "&#039;", $_str);
	$_str = str_replace("&acirc;&euro;&ldquo;", "-", $_str);
	$_str = str_replace("&Atilde;&copy;", "&eacute;", $_str);
	$_str = str_replace("&Acirc;", "", $_str);
	$_str = str_replace("&acirc;&euro;&oelig;", "", $_str);
	$_str = str_replace("&acirc;&euro;?", "", $_str);
	//$_str = str_replace("<ul>","",$_str); // remove <ul> to prevent indent of bullet lists
	//$_str = str_replace("</ul>","",$_str);
	$_str = str_replace("<p>&nbsp;</p>", "", $_str);
	$_str = str_replace("<br/><br/>", "</p><p>", $_str);
	$_str = str_replace("<br/>", "", $_str);
	$_str = str_replace("&nbsp;", " ", $_str);
	$_str = trim($_str);
	return $_str;
	unset($_str);
}

// format specific to names, capitalised
function format_name($_str = null)
{
	$_str = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $_str);
	//$_str = str_replace(",","&#44;",$_str);
	//$_str = addslashes($_str);
	$_str = trim($_str);
	$_str = ucwords($_str);
	//$_str = str_replace("'","&#039;",$_str);
	return $_str;
	unset($_str);
}

// strip all html from string
function strip_html($_str)
{
	$_str = strip_tags($_str);
	$_str = htmlspecialchars($_str);
	$_str = htmlentities($_str);
	return $_str;
	unset($_str);
}

// removes the last character from a string if it matches the input
function remove_lastchar($_data, $_char)
{
	$_len  = strlen($_char);
	$_data = trim($_data);
	if (substr($_data, strlen($_data) - $_len) == $_char) {
		$_data = substr($_data, 0, strlen($_data) - $_len);
	}
	return $_data;
}

// removes the first character from a string if it matches the input
function remove_firstchar($_data, $_char)
{
	$_len  = strlen($_char);
	$_data = trim($_data);
	if (substr($_data, 0, 1) == $_char) {
		$_data = substr($_data, 1);
	}
	return $_data;
}

// generate a random string
function random_string($_length = 10, $_style = NULL)
{
	if ($_style == 'safe') {
		$_pattern = "23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ_";
	} else {
		$_pattern = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~_-!";
	}

	for ($_i = 0; $_i < $_length; $_i++) {
		$_key .= $_pattern{rand(0, (strlen($_pattern) - 1))};
	}
	return $_key;
}

// hash password
function encrypt_password($_password, $_salt)
{
	$_output = md5($_salt . md5($_password . $_salt));
	return $_output;
}

// format rules for usernames
function format_username($_input)
{
	$_output = trim(strtolower(preg_replace('/[^a-zA-Z.]*/', '', $_input)));
	return $_output;
}

// clean input
function clean_input($_input)
{
	$_search = array(
		'@<script[^>]*?>.*?</script>@si', // Strip out javascript
		'@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
		'@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
		'@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments including CDATA
	);
	$_text   = preg_replace($_search, '', $_input);
	$_text   = htmlspecialchars($_text);
	return $_text;
}

// format javascript friendly text for calendar overdivs
function format_overdiv($_input)
{
	//return $_input;
	return str_replace(array("\r", "\n", '"', "'"), array("", "", "&quot;", "&rsquo;"), nl2br($_input));
}

//////////////////////////////////////////////////////////////////////
// NUMBERS
//////////////////////////////////////////////////////////////////////

// format price with currency symbol, and perform currency conversion if required
function format_price($_input = "0", $_currency = "GBP", $_decimal = null)
{
	if ($_currency == "GBP") {
		$_symbol = "&pound;";
	}
	else {
		require_once("currencyexchange.inc.php"); // using live feed from www.ecb.int
		$cx = new currencyExchange();
		$cx->getData();
		if ($result = $cx->Convert("GBP", "$_currency", $_input)) {
			$_input = $result;
		}
		if ($_currency == "EUR") {
			$_symbol = "&euro;";
		}
		elseif ($_currency == "USD") {
			$_symbol = "$";
		}
	}
	if ($_decimal) { // display price with decimal place
		$_output = $_symbol . number_format($_input, 2, '.', '');
	} else { // round to nearest whole
		$_output = $_symbol . number_format($_input);
	}
	return $_output;
	unset($_input, $_decimal, $_currency, $_symbol, $_output);
}

// return only the integers from a string, allows decimal point
function numbers_only($_input = NULL)
{
	//$_output = preg_replace('/\D/','',$_input);
	$_output = preg_replace('/[^0-9.]*/', '', $_input);
	return $_output;
	unset($_input, $_output);
}

// convert weekly price to monthly
function pw2pcm($_input = "0")
{
	$_output = (($_input * 52) / 12);
	// alowing decimal point
	$_output = number_format($_output, 2, '.', '');
	return $_output;
	unset($_input, $_output);
}

// convert monthly price to weekly
function pcm2pw($_input = "0")
{
	$_output = (($_input / 52) * 12);
	// alowing decimal point
	$_output = number_format($_output, 2, '.', '');
	return $_output;
	unset($_input, $_output);
}

// convert feet to meters
function ft2mtr($_input = "0")
{
	$_output = round($_input * 0.3048);
	return $_output;
	unset($_input, $_output);
}

// convert sqaure metres to square feet
function sqft2sqmtr($_input = "0")
{
	$_output = $_input * 92903.04;
	$_output = round($_output / 1000000);
	return $_output;
	unset($_input, $_output);
}

// convert meters to feet
function mtr2ft($_input = "0")
{
	$_output = round($_input * 3.28084);
	return $_output;
	unset($_input, $_output);
}

// convert sqaure metres to square feet
function sqmtr2sqft($_input = "0")
{
	$_output = $_input * 1000000;
	$_output = round($_output / 92903.04);
	return $_output;
	unset($_input, $_output);
}

// convert miles to kilometers
function mi2km($_input = "0")
{
	$_output = round($_input * 1.60934);
	return $_output;
	unset($_input, $_output);
}

// convert kilometers to miles
function km2mi($_input = "0")
{
	$_output = round($_input * 0.621371);
	return $_output;
	unset($_input, $_output);
}

// return formatted file size from size input
function format_filesize($_size)
{
	$_kb = 1024; // Kilobyte
	$_mb = 1048576; // Megabyte
	$_gb = 1073741824; // Gigabyte
	$_tb = 1099511627776; // Terabyte

	if ($_size < $_kb) {
		return $_size . " B";
	}
	else {
		if ($_size < $_mb) {
			return round($_size / $_kb, 2) . " KB";
		}
		else {
			if ($_size < $_gb) {
				return round($_size / $_mb, 2) . " MB";
			}
			else {
				if ($_size < $_tb) {
					return round($_size / $_gb, 2) . " GB";
				}
				else {
					return round($_size / $_tb, 2) . " TB";
				}
			}
		}
	}
	unset($_size, $_kb, $_mb, $_gb, $_tb);
}

function round_to_nearest($number, $toNearest = 5)
{
	$retval = 0;
	$mod    = $number % $toNearest;
	if ($mod >= 0) {
		$retval = ($mod > ($toNearest / 2)) ? $number + ($toNearest - $mod) : $number - $mod;
	} else {
		$retval = ($mod > (-$toNearest / 2)) ? $number - $mod : $number + (-$toNearest - $mod);
	}
	return $retval;
}

function duration($_input, $_format = NULL)
{

	$_hour   = floor($_input / 60);
	$_minute = floor($_input % 60);

	if ($_format == 'long') {
		$_hour_text   = ' hour';
		$_minute_text = ' minute';
	} elseif ($_format == 'short') {
		$_hour_text   = 'h';
		$_minute_text = 'm';
	} else {
		$_hour_text   = ' hr';
		$_minute_text = ' min';
	}

	if ($_hour > 0) {
		if ($_hour > 1 && $_format !== 'short') {
			$_hour_text .= 's ';
		}
		$_output = "$_hour$_hour_text ";
	}
	if ($_minute > 0) {
		if ($_minute > 1 && $_format !== 'short') {
			$_minute_text .= 's ';
		}
		$_output .= "$_minute$_minute_text";
	}
	return trim($_output);
}

function padzero($_input, $length = 2)
{
	$_output = str_pad($_input, $length, 0, STR_PAD_LEFT);
	return $_output;
}

// basic phone number validation
function phone_validate($number)
{
	if (strlen($number) < 8) {
		return false;
	}
	return true;
}

/* 
phone number formatting (020 xxxx xxxx, or 07xxx xxx xxx)
more info http://en.wikipedia.org/wiki/UK_telephone_numbering_plan
and http://www.planet-numbers.co.uk/uk_phone_codes.jsp

this needs more work, formatting of non-london numbers is a bit fucked
*/

function phone_format($sPhone, $sCountry = 'UK', $bInternationalFormat = false)
{
	if (empty($sPhone) || !trim($sPhone)) {
		return $sPhone;
	}

	// if number contains more than 2 characters, return
	if (preg_replace('/[^a-zA-Z_]*/', '', $sPhone)) {
		return $sPhone;
	}

	// Supported list of country phone format
	// Country code => International phone code
	$aCountries = array(
		'CA' => '1', // Canada
		'FR' => '33', // France
		'AI' => '1-264', // Anguilla
		'AG' => '1-268', // Antigua/Barbuda
		'BS' => '1-242', // Bahamas
		'BB' => '1-246', // Barbados
		'BM' => '1-441', // Bermuda
		'CA' => '1', // Canada
		'KY' => '1-345', // Cayman Islands
		'DM' => '1-767', // Dominica
		'DO' => '1-809', // Dominican Republic
		'GD' => '1-473', // Grenada
		'GU' => '1-671', // Guam
		'JM' => '1-876', // Jamaica
		'MS' => '1-664', // Montserrat
		'MP' => '1-670', // Northern Mariana Islands
		'PR' => array('1-787', '1-939'), // Puerto Rico
		'KN' => '1-869', // Saint Kitts and Nevis
		'LC' => '1-758', // Saint Lucia
		'VC' => '1-784', // Saint Vincent and the Grenadines
		'TT' => '1-868', // Trinidad and Tobago
		'TC' => '1-649', // Turks and Caicos Islands
		'US' => '1', // United States of America
		'VG' => '1-284', // Virgin Islands (British)
		'VI' => '1-340', // Virgin Islands (U.S.)
		'UK' => '44' // United Kingdom
	);

	if (!isset($aCountries[$sCountry])) {
		return $sPhone;
	}

	// Get rid of parenthesis, dashes, plus and dot signs,
	// then remove any spaces before numbers,
	// and remove duplicate "white spaces".
	//$sFormatted = str_replace(array('+', '(', ')', '-', '.', '/'), '', trim($sPhone)); // leaving the + in
	$sFormatted = str_replace(array('(', ')', '-', '.', '/'), '', trim($sPhone));
	$sFormatted = preg_replace(array('/\s+([0-9])/', '/\s+/'), array('\1', ' '), $sFormatted);
	list($sFormatted, $sExt) = explode(' ', $sFormatted, 2);

	// added, remove all whitespace
	$sFormatted = str_replace(" ", "", $sFormatted);

	$iLen         = strlen($sFormatted);
	$iCountryCode = $aCountries[$sCountry];

	// Deal with the primary phone number part based on the country
	switch ($sCountry) {
		/* case 'CA': See 'US' */

		case 'FR':
			// International format: +33 (0)1 23 45 67 89
			// National format: (0)1 23 45 67 89
			// Toll number format: 0800 12 34 56
			//						08 36 12 34 56
			switch ($iLen) {
				case 10:
					// Numeros Vert, Azur & Indigo
					$aNumerosSpeciaux = array('0800', '0801', '0802', '0803');
					$sIndicatif       = substr($sFormatted, 0, 4);
					if (in_array($sIndicatif, $aNumerosSpeciaux)) {
						// Appels internationaux impossible (?)
						$bInternationalFormat = false;
						$sFormatted           = $sIndicatif . ' ' . substr($sFormatted, 4, 2) . ' ' . substr($sFormatted, 6, 2) . ' ' . substr($sFormatted, -2);

					} elseif ($sIndicatif == '0836' && !$bInternationalFormat) {
						// Numeros Kiosque sont traites normalement a
						// l'international, mais en France the zero n'est pas mis
						// entre parentheses
						$sFormatted = substr($sFormatted, 0, 2) . ' ' . substr($sFormatted, 2, 2) . ' ' .
								substr($sFormatted, 4, 2) . ' ' . substr($sFormatted, 6, 2) . ' ' . substr($sFormatted, -2);

					} else {
						$sFormatted = '(' . substr($sFormatted, 0, 1) . ')' . substr($sFormatted, 1, 1) . ' ' .
								substr($sFormatted, 2, 2) . ' ' . substr($sFormatted, 4, 2) . ' ' . substr($sFormatted, 6, 2) . ' ' . substr($sFormatted, -2);
					}
					break;

				case 9:
					$sFormatted = '(0)' . substr($sFormatted, 0, 1) . ' ' . substr($sFormatted, 1, 2) . ' ' .
							substr($sFormatted, 3, 2) . ' ' . substr($sFormatted, 5, 2) . ' ' . substr($sFormatted, -2);

				default:
					// Any other unrecognized phone numbers are return as
					// they were passed.
					return $sPhone;
			}
			break;
		// End [CASE] FR / France

		// The following countries are folded into the US numbering plan
		case 'AI': // Anguilla
		case 'AG': // Antigua/Barbuda
		case 'BS': // Bahamas
		case 'BB': // Barbados
		case 'BM': // Bermuda
		case 'CA': // Canada
		case 'KY': // Cayman Islands
		case 'DM': // Dominica
		case 'DO': // Dominican Republic
		case 'GD': // Grenada
		case 'GU': // Guam
		case 'JM': // Jamaica
		case 'MS': // Montserrat
		case 'MP': // Northern Mariana Islands
		case 'PR': // Puerto Rico
		case 'KN': // Saint Kitts and Nevis
		case 'LC': // Saint Lucia
		case 'VC': // Saint Vincent and the Grenadines
		case 'TT': // Trinidad and Tobago
		case 'TC': // Turks and Caicos Islands
		case 'VG': // Virgin Islands (British)
		case 'VI': // Virgin Islands (U.S.)

		case 'US': // United States of America
			// National format: (123) 456-7890
			// International format: +1 (1) 123-456-7890
			// Toll number format: 1-800-123-4567
			if ($iLen == 11) {
				$sFormatted = substr($sFormatted, 1);
				$iLen       = 10;
			}
			switch ($iLen) {
				case 7:
					// Local number
					// Note: International number format cannot
					//be used for US and Canada
					$sFormatted = substr($sFormatted, 0, 3) . '-' . substr($sFormatted, -4);
					$bInternationalFormat &= ($sCountry != 'US' && $sCountry != 'CA');
					break;

				case 10:
					// Full number
					// Toll phone area codes
					$aTollAreaCodes = array(800, 866, 877, 888, 855, 844, 833, 822, 900, 880, 881, 882, 883);
					$sAreaCode      = substr($sFormatted, 0, 3);

					// Countries using the US phone numbering system
					// use the code area as country code, so we "reset"
					// the country code to "1" for phone numbers already including
					// the area code.
					$iCountryCode = '1';

					if (in_array((int)$sAreaCode, $aTollAreaCodes)) {
						// Note: International format cannot be supported here
						//   for toll numbers.
						$sFormatted           = '1-' . $sAreaCode . '-' . substr($sFormatted, 3, 3) . '-' . substr($sFormatted, -4);
						$bInternationalFormat = false;

					} elseif ($bInternationalFormat) {
						$sFormatted = '(1) ' . $sAreaCode . '-' . substr($sFormatted, 3, 3) . '-' . substr($sFormatted, -4);

					} else {
						$sFormatted = '(' . $sAreaCode . ') ' . substr($sFormatted, 3, 3) . '-' . substr($sFormatted, -4);
					}
					break;

				default:
					// Any other unrecognized phone numbers are return as
					// they were passed.
					return $sPhone;

			} // End [SWITCH] on length of number
			break;
		// End [CASE] US & Canada (CA)

		case 'UK': // United Kingdom
			// National format: (020) 1234 5678
			// International format: +44 (20) 1234-5678
			// Toll number format: 0800-123-456
			$iLen = strlen($sPhone);

			switch ($iLen) {
				/*
				 case 8:


					 // Local number
					 // Note: International number format cannot
					 // be used for UK
					 $sFormatted = substr($sFormatted, 0, 3) . ' ' . substr($sFormatted, -4);
					 $bInternationalFormat &= $sCountry != 'UK';



					 break;
			 */
				case (10 || 11):

					// mod for uk mobile numbers
					if (substr($sPhone, 0, 2) == "07") {
						$sFormatted = substr($sFormatted, 0, 5) . ' ' . substr($sFormatted, 5, 3) . ' ' . substr($sFormatted, 8);

						// mod for london 020 numbers
					} elseif (substr($sPhone, 0, 3) == "020") {
						$sFormatted = substr($sFormatted, 0, 3) . ' ' . substr($sFormatted, 3, 4) . ' ' . substr($sFormatted, 7);
					} else {

						// Full number
						// Toll phone area codes
						$aTollAreaCodes = array(800, 844, 845, 870, 871, 90, 91);

						if (in_array((int)substr($sFormatted, 1, 3), $aTollAreaCodes)) {
							// Note: International format cannot be supported here
							//   for toll numbers.
							$sFormatted           = substr($sFormatted, 0, 4) . ' ' . substr($sFormatted, 4, 3) . ' ' . substr($sFormatted, 7);
							$bInternationalFormat = false;

						} elseif ($bInternationalFormat) {
							$sFormatted = '(' . substr($sFormatted, 1, 2) . ') ' . substr($sFormatted, 3, 4) . ' ' . substr($sFormatted, 7);

						} else {
							$sFormatted = substr($sFormatted, 0, 5) . ' ' . substr($sFormatted, 5, 3) . ' ' . substr($sFormatted, 8);
						}

					} // end mod
					break;

				default:
					// Any other unrecognized phone numbers are return as
					// they were passed.
					return $sPhone;

			} // End [SWITCH] on length of number
			break;
		// End [CASE] UK

	} // End [SWITCH] on country code

	// Prepend with the country code and append extension if needed.
	return (($bInternationalFormat) ? '+' . $iCountryCode . ' ' : '') . $sFormatted . (($sExt) ? ' ' . $sExt : '');
}

?>
