<?php
// email functions

// send email

function send_email($recipient, $sender = "webmaster@woosterstock.co.uk", $subject, $text = null, $html = null)
{

	include('Mail.php');
	include('Mail/mime.php');

	$crlf = "\r\n";
	$hdrs = array(
		'From'    => $sender,
		'Subject' => $subject,
	);
	$mime = new Mail_mime($crlf);
	$mime->setTXTBody($text);
	if ($html) {
		$mime->setHTMLBody($html);
	}
	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);
	$mail =& Mail::factory('mail');
	//$mail->send($recipient, $hdrs, $body);
	$mail->send($recipient, $hdrs, $body);

}

#########################################################
# DISCLAMIERS
#########################################################

function email_footer($_format, $_email, $_name = "NULL")
{

	if ($_name == "NULL") {
		$_recipient = $_email;
	} else {
		$_recipient = $_name . ' (' . $_email . ')';
	}

	$email_footer_html = '
<table width="600" border="0">
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr>
  <td colspan="3"><span style="font-family:Arial, Helvetica, sans-serif; font-size:15px; font-weight: bold; color:#666666">Wooster &amp; Stock</span></td>
</tr>
<tr>
  <td><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">
  <font color="#FF9900">woosterstock.co.uk</font></span></td>
  <td nowrap><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">Nunhead<br>
	</span></td>
  <td nowrap><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#333333">Sydenham<br>
	109 Kirkdale, Sydenham<br>
	London SE26 4QY</span></td>
</tr>
<tr>
  <td colspan="3">&nbsp;</td>
</tr>
<tr>
  <td colspan="3"><span style="font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#666666">This
email and any files transmitted with it are confidential and intended
solely for ' . $_recipient . '. If you are not the named addressee you should
not disseminate, distribute, copy or alter this email. Any views or
opinions presented in this email are solely those of the author and
might not represent those of Wooster &amp; Stock. Warning: Although
Wooster &amp; Stock has taken reasonable precautions to ensure no viruses
are present in this email, the company cannot accept responsibility
for any loss or damage arising from the use of this email or
attachments.</span></td>
</tr>	
</table>
</body>
</html>
';
	$email_footer_text = '
Wooster & Stock
www.woosterstock.co.uk

This email and any files transmitted with it are confidential and intended solely for ' . $_recipient . '.
If you are not the named addressee you should not disseminate, distribute, copy or alter this email. Any views or 
opinions presented in this email are solely those of the author and might not represent those of Wooster &amp; Stock. 
Warning: Although Wooster &amp; Stock has taken reasonable precautions to ensure no viruses are present in this email, 
the company cannot accept responsibility for any loss or damage arising from the use of this email or attachments.
';

	if ($_format == "html") {
		return $email_footer_html;
	} elseif ($_format == "text") {
		return $email_footer_text;
	}
	unset($_format, $_email, $_name, $_address);
} // end function

?>