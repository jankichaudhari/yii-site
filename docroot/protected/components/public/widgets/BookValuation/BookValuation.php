<?php

class BookValuation extends CWidget
{

	public $name = "bookValuation";
	public $view = "";
	public $department = "sales";
	public $errorMessage, $successMessage;

	public $errors = array();

	public function init()
	{

		if (isset($_POST[$this->name]['send'])) {
			file_put_contents(Yii::app()->params['logDirPath'] . '/post.log', print_r($_POST, true), FILE_APPEND);
			$postData = $_POST[$this->name];
			$name = $postData['name'];
			$email = $postData['email'];
			$address = $postData['address'];
			$tel = $postData['tel'];
			$type = $postData['type'];
			$dateTime = $postData['date'];
			$dateTime .= isset($postData['time']) ? ' ' . $postData['time'] : '';

			if (!$name) {
				$this->errors[$this->name]['name'] = 'Name';
			}
			if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$this->errors[$this->name]['email'] = 'Email';
			}
			if (!$address) {
				$this->errors[$this->name]['address'] = 'Address';
			}

			if ($this->errors) {
				$msg                = 'In order for us to deal with your enquiry please provide the following info...';
				$msg                = $msg . '<ul class="horizontal"><li>';
				$msg                = $msg . implode("</li><li>", $this->errors[$this->name]);
				$msg                = $msg . "</li></ul>";
				$this->errorMessage = $msg;
			} else {
				$this->successMessage = 'Your message has been sent, thank you';
				try {
					include_once("Zend/Mail.php");
					$contactName  = strip_tags($name);
					$staffMessage = "Name:\t" . $contactName . "\n"
							. "Tel:\t" . $tel . "\n"
							. "Email:\t" . $email . "\n"
							. "Type:\n" . $type . "\n\n"
							. "Address:\n" . $address . "\n\n"
							. "Preffered date/time:\n" . $dateTime . "\n\n"
							. "Sent:\t" . date("d/m/Y H:i");

					$mailToStaff = new Zend_Mail("UTF-8");
					$mailToStaff->addTo(Yii::app()->params['valuation']['email']);
					$mailToStaff->setFrom(Yii::app()->params['valuation']['sender']);
					$mailToStaff->setSubject("Wooster & Stock Valuation Request");
					$mailToStaff->setBodyText($staffMessage);
					$mailToStaff->send();

					$clientEmail = $email;

					$mailToClient = new Zend_Mail('UTF-8');
					$mailToClient->addTo($clientEmail, $contactName);
					$mailToClient->setFrom(Yii::app()->params['valuation']['sender']);
					$mailToClient->setReplyTo(Yii::app()->params['valuation']['replyTo']);
					$mailToClient->setSubject("Wooster & Stock Valuation Request");

					$mailToClient->setBodyText($this->emailText('text', $clientEmail, $contactName));
					$mailToClient->setBodyHtml($this->emailText('html', $clientEmail, $contactName));

					$mailToClient->send();
				} catch (Exception $e) {
				}

				unset($_POST[$this->name]);
			}

		}
	}

	public function run()
	{

		/** @var  $detector \Device */
		$isMobile = Yii::app()->device->isDevice('mobile') ? true : false;
		$this->render($this->view ? $this->view : 'default', ['department' => $this->department,'isMobile' => $isMobile]);
	}

	public function emailText($format, $email, $name = null)
	{

		if (!$name) { // any false value can't be name
			$name = $recipient = $email;
		} else {
			$recipient = $name . ' (' . $email . ')';
		}

		if ($format === 'html') {
			return '<html>
				<html>
				<head></head>
				<body>
				<div style = "font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000">
				<p> Hi ' . $name . ',</p>
				<p> Many thanks for your valuation request. We will be in touch shortly to book an appointment.</p>
				<p>Kind Regards,<br>Wooster & Stock</p>
				</div>

				' . EmailHelper::signature() . '

				' . EmailHelper::disclaimer($recipient) . '
				</body>
				</html> ';
		}
		if ($format === 'text') {
			return 'Hi ' . $name . ',
					Many thanks for your message . We will be in touch shortly .

		' . EmailHelper::signature(EmailHelper::TYPE_TEXT) . '

		' . EmailHelper::disclaimer($recipient, EmailHelper::TYPE_TEXT);

		}
		return '';
	}

}
