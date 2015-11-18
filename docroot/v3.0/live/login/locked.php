<?php
session_start();
require_once("../admin/inx/general.inc.php");
echo '
<html>
<head>
<title>Login</title>
<link href="'.GLOBAL_URL.'css/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="content">
<form name="counter"> 
<h2>Locked</h2>
<p>You can try again in <input type="text" size="4" name="d2"> seconds</p>
<p><a href="'.$login_url.'">Login</a></p>
</form>
<script> 
<!-- 
// 
 var milisec=0 
 var seconds='.($_SESSION["login_delay"]-time()).'
 document.counter.d2.value=\'30\' 

function display(){ 
 if (milisec<=0){ 
    milisec=9 
    seconds-=1 
 } 
 if (seconds<=-1){ 
    milisec=0 
    seconds+=1 
 } 
 else 
    milisec-=1 
    document.counter.d2.value=seconds 
    setTimeout("display()",100) 
} 
display() 
--> 
</script>
</div>
</body>
</html>
';			

?>