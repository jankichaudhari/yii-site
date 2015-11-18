<?php
/* 
user preferences include page
this will import all user defined preferences from the database and add them to session variables

preference session variables are stored in the "pref" session containers:

e.g.
$_SESSION["pref"]["calendar"]["zoom"] = calendar
$_SESSION["pref"]["homepage"]["layout"] = homepage
and so on...

maybe store prefs in a single field in database in serialize() format? and unserialize() for use

prefs for:
default scope (sales or lettings) - used to select default values on search pages etc
default status? negs would see available, production would see production? maybe annoying
home page layout - user can choose modules to display on the home page (inbox, appointments, sales props, letings props etc)
calendar view (zoom, default branch, last viewed, others?)
*/

// database connection
require_once("db.inc.php");




?>