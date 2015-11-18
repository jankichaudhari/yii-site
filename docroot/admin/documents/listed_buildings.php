<?php
session_start();
require("../global.php"); 
require("../secure.php"); 
$pageTitle = "Listed Buildings";
echo html_header($pageTitle);
?>
<h2><?php echo $pageTitle;?></h2>
<P style="TEXT-ALIGN: left">Listed buildings are graded to show their relative importance:</P>
<UL>
  <LI>Grade I buildings are those of exceptional interest 
  <LI>Grade II* are particularly important buildings of more than special interest 
    
<LI>Grade II are of special interest, warranting every effort to preserve them</LI>
</UL>
<p><a href="http://www.english-heritage.org.uk/server/show/nav.1374">http://www.english-heritage.org.uk/</a></p>

</BODY>
</HTML>