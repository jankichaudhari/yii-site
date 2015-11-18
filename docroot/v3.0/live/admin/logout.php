<?php
session_start();
require_once("inx/global.inc.php");
$_SESSION["auth"] = '';
session_unregister("auth");
/*
$_SESSION["auth_use_id"] = "";
$_SESSION["auth_use_loa"] = "";
$_SESSION["auth_use_username"] = "";
$_SESSION["auth_use_password"] = "";
$_SESSION["auth_use_email"] = "";
$_SESSION["auth_use_fname"] = "";
$_SESSION["auth_use_sname"] = "";

session_unregister("auth_use_id");
session_unregister("auth_use_loa");
session_unregister("auth_use_username");
session_unregister("auth_use_password");
session_unregister("auth_use_email");
session_unregister("auth_use_fname");
session_unregister("auth_use_sname");
*/
$_SESSION = array();

session_unset();
session_destroy();

header("Location:".GLOBAL_URL);

?>