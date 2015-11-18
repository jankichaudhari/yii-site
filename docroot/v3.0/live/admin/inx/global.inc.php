<?php
require_once dirname(__FILE__) . "/../../../../../config/config.admin.inc.php";
// general include file for wsv3 admin
session_start();

// DEVELOPMENT only show to allowed ip addresses
$allowed_ip = array(
	"80.46.83.190", // home
	"217.206.41.58", // ship
	"217.37.154.177", // syd
	"217.46.185.77", // shad?
	"87.194.39.16", // camberwell hall be
	"87.194.39.39", // sydenham be
	"87.74.115.239" // camberwell hall bulldog
);
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ip)) {
	#echo "site under development (".$_SERVER['REMOTE_ADDR'].")";
	#exit;
}
// END

//require_once("general.inc.php"); // general file (paths etc)
require_once(dirname(__FILE__) . "/db.inc.php"); // database connection and general functions
require_once(dirname(__FILE__) . "/secure.inc.php"); // password protection and session functions
require_once(dirname(__FILE__) . "/error.inc.php"); // error handling
require_once(WS_PATH_PUBLIC_INCLUDES . "/format.inc.php"); // string formatting // comes from components/includes
require_once(dirname(__FILE__) . "/content.inc.php"); // content and styles
require_once(dirname(__FILE__) . "/postcode.inc.php"); // postcode lookup
require_once(dirname(__FILE__) . "/postcode.class.inc.php"); // postcodeanywhere class
require_once(dirname(__FILE__) . "/page.inc.php"); // PEAR page constructor (HTML_Page2)
require_once(dirname(__FILE__) . "/form.inc.php"); // form construtor
require_once(dirname(__FILE__) . "/table.inc.php");
require_once(dirname(__FILE__) . "/ptype.inc.php"); // table
require_once(dirname(__FILE__) . "/ptype2.inc.php"); // multi dropdowns
require_once(dirname(__FILE__) . "/area.inc.php");
require_once(dirname(__FILE__) . "/source.inc.php");
require_once(WS_PATH_COMPONENTS . "/Numbers/Words.php"); // PEAR display numbers as words
require_once(dirname(__FILE__) . "/image.inc.php"); // general image functions
require_once(dirname(__FILE__) . "/map.class.inc.php"); // map class
require_once(dirname(__FILE__) . "/mail.inc.php"); // email functions
require_once(dirname(__FILE__) . "/notify.inc.php");
// default page values for HTML_Page2 class
$page_defaults = array(
	'charset' => 'utf-8',
	'lineend' => 'unix',
	'doctype' => 'XHTML 1.0 Transitional',
	'language'=> 'en',
	'tab'     => '',
	'cache'   => 'true'
);


// debug options
//$_SESSION["debug"]["db_query"] = 1; // set db_query to print all arrays


?>