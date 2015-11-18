<?php
require_once 'Zend/Mail/Transport/Abstract.php';
require_once __DIR__ . '/../extensions/Mandrill/src/Mandrill.php';
class MandrillMailTransport extends Zend_Mail_Transport_Abstract
{
	private $mergeVars = [];
	private $globalVars = [];
	private $tags = [];
	private $message = array(
		'html'                      => '',
		'text'                      => null,
		'subject'                   => '',
		'from_email'                => '',
		'from_name'                 => '',
		'to'                        => [],
		'headers'                   => null,
		'important'                 => false,
		'track_opens'               => null,
		'track_clicks'              => null,
		'auto_text'                 => null,
		'auto_html'                 => null,
		'inline_css'                => null,
		'url_strip_qs'              => null,
		'preserve_recipients'       => false,
		'bcc_address'               => null,
		'tracking_domain'           => null,
		'signing_domain'            => null,
		'return_path_domain'        => null,
		'merge'                     => true,
		'global_merge_vars'         => [],
		'merge_vars'                => [],
		'tags'                      => null,
		'google_analytics_domains'  => null,
		'google_analytics_campaign' => null,
		'metadata'                  => null,
		'recipient_metadata'        => null,
		'attachments'               => null,
		'images'                    => null,
	);

	/**
	 * Send an email independent from the used transport
	 *
	 * The requisite information for the email will be found in the following
	 * properties:
	 *
	 * - {@link $recipients} - list of recipients (string)
	 * - {@link $header} - message header
	 * - {@link $body} - message body
	 */
	protected function _sendMail()
	{
//		$mandrill = new Mandrill('5H8HqHL9FGE4ETnoSYeIaw');
		print_r($this->recipients);
		print_r($this->_headers['To']);
		print_r($this->_mail->getSubject());

		$mandrill = new Mandrill('5H8HqHL9FGE4ETnoSYeIaw');
		$message  = $this->message;
		$async    = false;
		$result   = $mandrill->messages->send($message, $async);
		print_r($result);
	}

	/**
	 * @param $recepient
	 * @param $vars
	 */
	public function setRecipientMergeVars($recepient, $vars)
	{
		$this->message['merge_vars'][] = array(
			'rcpt' => $recepient,
			'vars' => $vars
		);
	}

	public function setGlobalVars($vars)
	{
		$this->message['global_merge_vars'] = $vars;
	}

	public function setTags($tags)
	{
		$this->message['tags'] = $tags;
	}

	public function setOption($optionName, $value)
	{
		$this->message[$optionName] = $value;
		return $this;
	}

	public function getOption($optionName)
	{
		return (isset($this->message[$optionName]) ? $this->message[$optionName] : null);
	}
}
