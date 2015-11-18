<?php
require_once("inx/global.inc.php");

$render = '
<br />
<table width="100%" border="1" cellspacing="0" cellpadding="5">

  <tr valign="top">
    <td><strong>Computer setup</strong></td>
    <td colspan="2"><p>To set yourself up on a computer, you will need your login name and password <br />
	(if using a computer in Camberwell, please skip to step 6)</p>
	<ol>

		<li>Log in with an existing account (ask collegue to do this for you)</li>
		<li>Open Control Panel / User Accounts / click Create New Account</li>
		<li>Enter the username, click the button, and choose Computer Administrator as account type</li>
		<li>Once done, click the icon labelled with the new user\'s name, and click create password (you must use the exact supplied password)</li>
		<li style="margin-bottom:10px">Log out</li>

		<li>Log on to the computer with your username and password</li>

		<li>Open Outlook, say yes to set up new account, and choose Microsoft Exchange Server from the list</li>
		<li>Server name: Q2C-Server &nbsp; Mailbox name: (your username)</li>
		<li>Next, next and finish</li>
		<li>That is it, you now just need to log into admin and you are up and running</li>
	</ol>
  </tr>
  <tr valign="top">
    <td><strong>Internet Connection</strong><br>
      General </td>
    <td colspan="2"><p><img src="../../../images/sys/admin/vigor3300_v4.jpg" width="270" height="120" align="right">If the internet stops working, the first thing to try is to restart the router. The router is pictured on the right, and the power switch is on the back towards the left hand side. Switch it off, wait a few moments, switch it back on again. This cures the problem 99% of the time. </p>
    <p>If this does not solve the problem, try power cycling the moden, which sits on top of the router (Vigor 2700).</p></td>
  </tr>
  <tr bgcolor="#666666">
    <td width="200"><font color="#FFFFFF"><strong>Problem with...</strong></font></td>
    <td><font color="#FFFFFF"><strong>Contact</strong></font></td>
    <td><font color="#FFFFFF"><strong>Notes</strong></font></td>
  </tr>
  <tr valign="top" bgcolor="#CCCCCC">
    <td colspan="3"><strong>Computers and Network</strong></td>
  </tr>
  <tr valign="top">
    <td><strong>Internet Connection </strong><br>
      (Camberwell)</td>
    <td><a href="http://www.bethere.co.uk" target="_blank">Be</a> - 0808 234 8566</td>
    <td>Be     ADSL phone number: 020 7 ??<br>
      Be Public IP address: 87.194.39.16</td>
  </tr>
  <tr valign="top">
    <td nowrap><strong>Internet Connection </strong><br>
      (Sydenham)</td>
    <td><a href="http://www.bethere.co.uk" target="_blank">Be</a> - 0808 234 8566</td>
    <td>Be ADSL line: 020 8613 0070 <br>
Be Public IP address: 87.194.39.84</td>
  </tr>
  <tr valign="top">
    <td><strong>Database</strong> <br>
    (figthedog)</td>
    <td nowrap>David Williams - 020 8761 3190</td>
    <td>If you lose the figthedog icon from your desktop, simply open My Computer, then the <a href="Z:\">Z:\</a> drive. You will find figthedog in there.</td>
  </tr>
  <tr valign="top">
    <td><strong>Website</strong> <br>
    (www.woosterstock.co.uk)</td>
    <td><a href="http://www.titaninternet.co.uk" target="_blank">Titan Internet</a> - 0845 125 9500<br>
        <a href="mailto:support@titaninternet.co.uk">support@titaninternet.co.uk</a></td>
    <td>If our web site stops responding and other sites (google, msn) still work</td>
  </tr>
  <tr valign="top" bgcolor="#CCCCCC">
    <td colspan="3"><strong>Printers</strong></td>
  </tr>
  <tr valign="top">
    <td>Camberwell<br>
      Xerox DC12</td>
    <td><a href="http://www.xerox.co.uk" target="_blank">Xerox</a> - 0870 900 5501</td>
    <td>Serial numbers - DC12: 2137113360 / RIP: 3076274842<br>
      Use this number to book engineer visits, and to order consumables</td>
  </tr>
  <tr valign="top">
    <td>Camberwell<br>
    Konica Minolta BizHub 350 </td>
    <td><a href="http://www.technologic.co.uk" target="_blank">Technologic</a> - 020 7511 7746</td>
    <td>Serial number: 211745254<br>
    Use this number to book engineer visits, and to order consumables </td>
  </tr>
  <tr valign="top">
    <td> Sydenham <br>
      Konica Minolta BizHub 350 </td>
    <td><a href="http://www.technologic.co.uk" target="_blank">Technologic</a> - 020 7511 7746</td>
    <td>Serial number: 211745184<br>
      Use this number to book engineer visits, and to order consumables </td>
  </tr>
  <tr valign="top" bgcolor="#CCCCCC">
    <td colspan="3"><strong>Phone Lines and Systems</strong></td>
  </tr>
  <tr valign="top">
    <td>Camberwell and Sydenham</td>
    <td><a href="http://www.xteleurope.com" target="_blank">Xtel Europe</a> - 01342 335000</td>
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
</table>';


$navbar_array = array(
	'back'=>array('title'=>'Back','label'=>'Back','link'=>$_GET["searchLink"]),
	'search'=>array('title'=>'Property Search','label'=>'Property Search','link'=>'property_search.php')
	);
$navbar = navbar2($navbar_array);

$page = new HTML_Page2($page_defaults);
$page->setTitle("Technical Support");
$page->addStyleSheet(getDefaultCss());
$page->addScript('js/global.js');
$page->addBodyContent($header_and_menu);
$page->addBodyContent('<div id="content_wide">');
$page->addBodyContent($navbar);
$page->addBodyContent($render);
$page->addBodyContent('</div>');
$page->display();
?>
