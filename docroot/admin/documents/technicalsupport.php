<?php
session_start();
require("../global.php"); 
require("../secure.php"); 
$pageTitle = "Technical support and contacts";
echo html_header($pageTitle);
?>
<h2><?php echo $pageTitle; ?></h2>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
  <tr bgcolor="#666666"> 
    <td width="256"><font color="#FFFFFF"><strong>Problem with...</strong></font></td>
    <td width="259"><font color="#FFFFFF"><strong>Contact</strong></font></td>
    <td width="360"><font color="#FFFFFF"><strong>Notes</strong></font></td>
  </tr>
  <tr valign="top" bgcolor="#CCCCCC"> 
    <td colspan="3"><strong>Computers and Network</strong></td>
  </tr>
  <tr valign="top"> 
    <td><strong>Website</strong> <br>
      (www.woosterstock.co.uk)</td>
    <td>Titan Internet - 0845 125 9500<br> <a href="mailto:support@titaninternet.co.uk%20">support@titaninternet.co.uk 
      </a></td>
    <td>If our web site stops responding and other sites (google, msn) still work</td>
  </tr>
  <tr valign="top"> 
    <td><strong>Internet Connection </strong><br>
      (Camberwell)</td>
    <td>Be - 0808 234 8566</td>
    <td>Be     ADSL phone number: 020 7 ??<br>
      Be Public IP address: 87.194.39.16</td>
  </tr>
  <tr valign="top"> 
    <td nowrap><strong>Internet Connection </strong><br>
      (Sydenham)</td>
    <td>Be - 0808 234 8566</td>
    <td>Be ADSL line: 020 8613 0070 <br>
Be Public IP address: 87.194.39.84</td>
  </tr>
  <tr valign="top"> 
    <td><strong>Database</strong> <br>
      (figthedog)</td>
    <td nowrap>David Williams - 020 8761 3190</td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top" bgcolor="#CCCCCC"> 
    <td colspan="3"><strong>Printers</strong></td>
  </tr>
  <tr valign="top"> 
    <td>Camberwell<br>
      Xerox DC12</td>
    <td>Xerox - 0870 900 5501</td>
    <td>Serial numbers - DC12: 2137113360 / RIP: 3076274842<br>
      Use this number to book engineer visits, and to order consumables</td>
  </tr>
  <tr valign="top">
    <td>Camberwell<br>
    Konica Minolta BizHub 350 </td>
    <td>Technologic - 020 7511 7746</td>
    <td>Serial number: 211745254<br>
    Use this number to book engineer visits, and to order consumables </td>
  </tr>
  <tr valign="top"> 
    <td> Sydenham <br>
      Konica Minolta BizHub 350 </td>
    <td>Technologic - 020 7511 7746</td>
    <td>Serial number: 211745184<br>
      Use this number to book engineer visits, and to order consumables </td>
  </tr>
  <tr valign="top" bgcolor="#CCCCCC"> 
    <td colspan="3"><strong>Phone Lines and Systems</strong></td>
  </tr>
  <tr valign="top"> 
    <td>Camberwell and Sydenham</td>
    <td>Xtel Europe - 01342 335000</td>
    <td>Xtel now handle all billing and technical issues instead of BT </td>
  </tr>
  <tr valign="top" bgcolor="#CCCCCC"> 
    <td colspan="3"><strong>Mobile Phones</strong></td>
  </tr>
  <tr valign="top"> 
    <td>o2</td>
    <td>Mike Rogerson - 01928 702 308<br> <a href="mailto:mike.rogerson@o2.com">mike.rogerson@o2.com</a> 
    </td>
    <td>Lost or stolen phones must be cancelled. </td>
  </tr>
  <tr valign="top">
    <td>T-Mobile</td>
    <td>Jonathon Weeks (3b Direct)<br>
    0151 335 3600 </td>
    <td>Lost or stolen phones must be cancelled. </td>
  </tr>
</table>

</body>
</html>