<?php
require_once("inx/global.inc.php");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
<base target="mainFrame">
<link href="css/menu.css" rel="stylesheet" type="text/css">
<script language="javascript">
function trClick(href)	{ 
	parent.frames['mainFrame'].location.href = href;
	}
</script>
<style type="text/css">
body {
	margin-left: 2px;
	margin-top: 2px;
	margin-right: 2px;
	margin-bottom: 2px;
	background-color: #FFFFFF;
}
#list-menu {
	width: 135px;
	}
#list-menu ul {
	margin: 0; 
	padding: 0;
	list-style-type: none;
	font-family: verdana, arial, sanf-serif;
	font-size: 12px; 
	} 
#list-menu li {
	margin: 0px 0 0;
	} 
#list-menu a {
	display: block;
	width:120px;
	padding: 4px 2px 4px 5px;
	margin: 5px 0px 0px 2px;
	background: #FF9900;
	text-decoration: none; /*lets remove the link underlines*/
	} 
#list-menu a:link, #list-menu a:active, #list-menu a:visited {
	color: #FFFFFF;
	font-weight:bold;
	}
#list-menu a:hover {
	background: #FF6600;
	color: #ffffff;
	} 
	
	
	

#list-menu ul li ul {
	margin: 0; 
	margin-bottom: 10px;
	padding: 0;
	list-style-type: none;
	font-family: arial, sanf-serif;
	font-size: 11px; 
	}
 
#list-menu ul li ul li {
	margin: 0; 
	padding: 0;
	} 
#list-menu ul li ul li a {
	display: block;
	width:120px;
	padding: 1px 1px 1px 8px;
	margin: 0px 0px 0px 0px;
	background: #FFFFFF;
	text-decoration: none; /*lets remove the link underlines*/
	}

#list-menu ul li ul li a:link, #list-menu ul li ul li a:active, #list-menu ul li ul li a:visited {
	color: #000000;
	font-weight:bold;
	}
#list-menu ul li ul li a:hover {
	background: #FFFFFF;
	color: #ff9900;
	} 



</style>
</head>
<body>
<script language="javascript">
if (parent.frames.length == 0) {
    parent.location.href = 'index.php';
}
</script>

<div id="list-menu">

<ul>
  <li><a href="home.php" onfocus="this.blur()">Home Page</a></li>
  <li><a href="calendar.php" onfocus="this.blur()">Calendar</a></li>
    <ul>
	  <li><a href="viewing_add.php" onfocus="this.blur()">Arrange Viewing</a></li>
	  <li><a href="appointment_search.php" onfocus="this.blur()">Search Calendar</a></li>
    </ul>
  <li><a href="property.php" onfocus="this.blur()">Property</a></li>
    <ul>
	  <li><a href="property_search.php" onfocus="this.blur()">Search Property</a></li>
	  <li><a href="valuation_add.php" onfocus="this.blur()">Arrange Valuation</a></li>
	  <li><a href="instruction_add.php" onfocus="this.blur()">New Instruction</a></li>
    </ul>
  <li><a href="client.php" onfocus="this.blur()">Applicants</a></li>
    <ul>
	  <li><a href="client_search.php" onfocus="this.blur()">Search Applicants</a></li>
	  <li><a href="applicant_add.php" onfocus="this.blur()">New Applicant</a></li>
    </ul>
  <li><a href="contact.php" onfocus="this.blur()">Contacts</a></li>
    <ul>
	  <li><a href="contact_add.php">New Contact</a></li>
	  <li><a href="company_add.php">New Company</a></li>
    </ul>
  <li><a href="logout.php">Logout</a></li>
</ul> 

</div>
<!--
<table width="135" border="0" cellspacing="2" cellpadding="3">
  <tr>
	<td class="menu" onClick="trClick('home.php');"><a href="home.php">Home Page</a></td>
  </tr>
  <tr>
    <td><img src="img/blank.gif"></td>
  </tr>
  <tr>
    <td class="menu" onClick="trClick('calendar.php');"><a href="calendar.php">Calendar</a></td>
  </tr>
  <tr>
	<td class="menu_sub" onClick="trClick('appointment_search.php');"><a href="appointment_search.php">Search Appointments</a></td>
  </tr>
  <tr>
    <td><img src="img/blank.gif"></td>
  </tr>
  <tr>
	<td class="menu" onClick="trClick('property.php');"><a href="property.php">Property</a></td>
  </tr>
  <tr>
	<td class="menu_sub" onClick="trClick('property_search.php');">&raquo; <a href="property_search.php">Search Property</a></td>
  </tr>
  <tr>
	<td class="menu_sub" onClick="trClick('viewing_add.php');">&raquo; <a href="viewing_add.php">Arrange Viewing</a></td>
  </tr>
  <tr>
	<td class="menu_sub" onClick="trClick('valuation_add.php');">&raquo; <a href="valuation_add.php">Arrange Valuation</a></td>
  </tr>
  <tr>
	<td class="menu_sub" onClick="trClick('client_lookup.php?dest=instruction');">&raquo; <a href="client_lookup.php?dest=instruction">New Instruction</a></td>
  </tr>
  <tr>
    <td><img src="img/blank.gif"></td>
  </tr>
  <tr>
	<td class="menu" onClick="trClick('client.php');"><a href="client.php">Applicants</a></td>
  </tr>
  <tr>
	<td class="menu_sub" onClick="trClick('client_search.php');">&raquo; <a href="client_search.php">Search Applicants</a></td>
  </tr>
  <tr>
	<td class="menu_sub" onClick="trClick('client_lookup.php');">&raquo; <a href="client_lookup.php">New Applicant</a></td>
  </tr>
  <tr>
    <td><img src="img/blank.gif"></td>
  </tr>
  <tr>
    <td class="menu" onClick="trClick('contact.php');"><a href="contact.php">Contacts</a></td>
  </tr>
  <tr>
	<td class="menu_sub" onClick="trClick('contact_add.php');">&raquo; <a href="contact_add.php">New Contact</a></td>
  </tr>
  <?php if (in_array('SuperAdmin',$_SESSION["auth"]["roles"])) { ?>  
  <tr>
    <td><img src="img/blank.gif"></td>
  </tr>
  <tr>
	<td class="menu_sub" onClick="trClick('superadmin_tools.php');">&raquo; <a href="superadmin_tools.php">Tools</a></td>
  </tr>
  <?php } ?>
  
  <tr>
    <td><img src="img/blank.gif"></td>
  </tr>
  <tr>
	<td class="menu"><a href="directory/">Directory</a></td>
  </tr>
 
  <tr>
    <td><img src="img/blank.gif"></td>
  </tr>
  <tr>
	<td class="menu_sub" onClick="trClick('logout.php');">&raquo; <a href="logout.php" target="_parent">Logout</a></td>
  </tr>
</table>

-->
</body>
</html>
