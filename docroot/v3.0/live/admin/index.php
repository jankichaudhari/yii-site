<?php

header("Location:home.php");
exit;
require_once("inx/global.inc.php");

// dynamic main frame page
	
if ($_GET["display"]) {
	$display = urldecode($_GET["display"]);
	$url = parse_url(GLOBAL_URL);
	$display = str_replace($url["path"],'',$display);
	$display = str_replace('index.php?display=','',$display);
	
	// make sure the searchLink remains encoded, if present
	if (strstr($display,'searchLink')) {
		$parts = explode('searchLink=',$display);
		$display = $parts[0].'searchLink='.urlencode($parts[1]);
		}
	}

//if (!$display || $display == 'index.php' || $display == 'frametop.php' || $display == 'frameleft.php' || $display == 'admin') {
if (!$display) {
	$mainFrame = 'home.php';
	} else {
	$mainFrame = $display;
	}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Wooster &amp; Stock Administration</title>
<link rel="SHORTCUT ICON" href="<?php echo GLOBAL_URL; ?>favicon.ico" />
</head>
<script language="javascript">
if (parent.frames.length > 0) {
    parent.location.href = self.document.location
}
</script>
<frameset rows="25,*" cols="*" frameborder="NO" border="0" framespacing="0">
  <frame src="frametop.php" name="topFrame" scrolling="NO" noresize>
  <frameset cols="140,*" frameborder="NO" border="0" framespacing="0">
    <frame src="frameleft.php" name="leftFrame" scrolling="NO" noresize>
    <frame src="<?php echo $mainFrame; ?>" name="mainFrame">
  </frameset>
</frameset>
<noframes><body>
</body></noframes>
</html>