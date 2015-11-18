<?php
require_once("inx/global.inc.php");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
<link href="css/top.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
<!--
var currenttime = '<?php echo date("F d, Y H:i:s", time()); ?>';
var montharray=new Array("January","February","March","April","May","June","July","August","September","October","November","December");
var dayarray=new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");

var serverdate=new Date(currenttime);

function padlength(what){
var output=(what.toString().length==1)? "0"+what : what;
return output;
}

function displaytime(){
serverdate.setSeconds(serverdate.getSeconds()+1);
thisDate=serverdate.getDate()
switch (thisDate) {
case 1:
dateSuffix="st"
break
case 21:
dateSuffix="st"
break
case 31:
dateSuffix="st"
break    
case 2:  
dateSuffix="nd"  
break    
case 22:
dateSuffix="nd"
break;   
case 3:
dateSuffix="rd"  
break     
case 23:
dateSuffix="rd"  
break      
default:   
dateSuffix="th"
}

var datestring=dayarray[serverdate.getDay()]+" "+serverdate.getDate()+dateSuffix+" "+montharray[serverdate.getMonth()]+", "+serverdate.getFullYear();
var formattedHour = padlength(serverdate.getHours());
var ampm = " PM"
if (formattedHour < 12){
	ampm = " AM"
	}
if (formattedHour > 12){
	formattedHour -= 12
	}
var timestring="<b>"+formattedHour+":"+padlength(serverdate.getMinutes())+" "+ampm+"</b>";
document.getElementById("servertime").innerHTML=datestring+" "+timestring;
}

window.onload=function(){
setInterval("displaytime()", 1000);
}
-->
</script>
</head>
<body background="../../../images/sys/admin/title_bg.gif">
<script language="javascript">
if (parent.frames.length == 0) {
 //   parent.location.href = 'index.php';
}
</script>
<div id="top">

<table width="100%" border="0" cellspacing="0" cellpadding="0" onClick="location.reload();">
  <tr>
    <td class="time"><?php echo $_SESSION["auth"]["use_fname"].' '.$_SESSION["auth"]["use_sname"]; ?> <span class="versionInfo"><?php echo "Version: ".SYSTEM_VERSION." / Database: ".$dsn["database"]; ?></span></td>
    <td align="right" height="25"><span id="servertime" class="time"></span></td>
  </tr>
</table>
	
	<br>
	

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
	  <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td class="title">Wooster &amp; Stock Administration</td>
		<td class="quicklink"><a href="client_lookup.php?dest=viewing" target="mainFrame">Arrange Viewing</a> | 
		<a href="client_lookup.php" target="mainFrame">New Applicant</a> | 
		Send Message | 
		View Manuals</td>
      </tr>	  
	  </table>  
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="head">Logged in as <?php echo '<a href="user.php?stage=2&use_id='.$_SESSION["auth"]["use_id"].'" target="mainFrame">'.$_SESSION["auth"]["use_fname"].' '.$_SESSION["auth"]["use_sname"].'</a> - <a href="logout.php" target="_top">Logout</a>'; ?></td>
        <td nowrap class="head" align="right"><layer name="putmedate"><div id="putmedate">&nbsp;</div></layer>
<script language="JavaScript" type="text/JavaScript">

var _dom = document.getElementById;
var _iex = document.all;
var _ns4 = document.layers;

var _sourceLayer = 'putmedate'; // name of layer to write in

function padout(number) {
	return (number < 10) ? '0' + number : number;
}

function _writeinto(what) {
	if (_dom) {
		x = document.getElementById(_sourceLayer);
		x.innerHTML = what;
	} else if (_iex) {
		x = document.all[_sourceLayer];
		x.innerHTML = what;
	} else if (_ns4) {
		var x = document.layers[_sourceLayer];
		text2 = '<p class="dated2">' + what + '</p>';
		x.document.open();
		x.document.write(text2);
		x.document.close();
	}
}

function liveclock() {

	var time = new Date();
	var _month = padout(time.getMonth() + 1);
	var _day = padout(time.getDate());
	var _hours = padout(time.getHours());
	var _minutes = padout(time.getMinutes());
	var _seconds = padout(time.getSeconds());
	var _year = time.getYear();
	if (_year < 2000) _year = _year + 1900;
	
	var amOrPm = "";

	
	var _current = "<div class=\"dated2\">"+ _year + "." + _month + "."+ _day +" "+ _hours +":"+ _minutes +":"+ _seconds + " "+ amOrPm +"</div>";
	_writeinto(_current);
	
	setTimeout("liveclock()",1000);
}

liveclock();


</script><a href="javascript:location.reload()" title="Click to refresh clock"><span id="servertime"></span></a></td>
      </tr>
    </table></td>
  </tr>
</table>

</div>
</body>
</html>