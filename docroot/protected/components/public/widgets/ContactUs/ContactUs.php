<?php

/**
 * This widget relies on Zend_Mail components;
 * To change POST array key in kase you override 'contact' key somewhere else on a page
 * you can use $this->name property
 *
 *
 * @uses Zend_Mail
 */
class ContactUs extends CWidget
{

	public $errors = array();
	public $successMessage = '';
	public $errorMessage = '';

	/**
	 * @var string
	 * @deprecated
	 */
	public $name = "contactUs";
	public $view = "";
	public $model = null;

	public function init()
	{

		$this->model = new ContactUsForm();

		if (isset($_POST['ContactUsForm'])) {
			$this->model->attributes = $_POST['ContactUsForm'];

			if ($this->model->validate()) {
				$this->successMessage = 'Your message has been sent, thank you';
				try {
					include_once("Zend/Mail.php");
					$contactName  = strip_tags($this->model->name);
					$staffMessage = "Name:\t" . $contactName . "\n"
							. "Tel:\t" . $this->model->telephone . "\n"
							. "Email:\t" . $this->model->email . "\n"
							. "Message:\n" . $this->model->message . "\n\n"
							. "Sent:\t" . date("d/m/Y H:i");

					$mailToStaff = new Zend_Mail("UTF-8");
					$mailToStaff->addTo($this->model->to . Yii::app()->params['contactUs']['email_hostname']);
					$mailToStaff->setFrom($this->model->to . Yii::app()->params['contactUs']['email_hostname']);
					$mailToStaff->setSubject("Message posted from Wooster & Stock Contact Page");
					$mailToStaff->setBodyText($staffMessage);
					$mailToStaff->send();

					$mailToClient = new Zend_Mail('UTF-8');
					$mailToClient->addTo($this->model->email, $contactName);
					$mailToClient->setFrom($this->model->to . Yii::app()->params['contactUs']['email_hostname']);
					$mailToClient->setSubject("Message posted from Wooster & Stock Contact Page");
					$mailToClient->setBodyText($this->emailText('text', $this->model->email, $contactName));
					$mailToClient->setBodyHtml($this->emailText('html', $this->model->email, $contactName));
					$mailToClient->send();
				} catch (Exception $e) {
				}
				$this->model->unsetAttributes();
			}

		}
	}

	public function run()
	{

		/** @var Office[] $o */
		$o       = Office::model()->active()->findAll();
		$offices = [];

		foreach ($o as $office) {
			$offices[explode('@', $office->email)[0]] = $office->shortTitle;
		}

		/** @var  $detector \Device */
		$isMobile = Yii::app()->device->isDevice('mobile') ? true : false;
		$this->render($this->view ? $this->view : 'default', array(
				'model'    => $this->model,
				'offices'  => $offices,
				'isMobile' => $isMobile
		));
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
			<head></head>
			<body>
			<div style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000">
			<p>Hi ' . $name . ',</p>
			<p>Many thanks for your message. We will be in touch shortly.</p>
			</div>
				' . EmailHelper::signature() . '

				' . EmailHelper::disclaimer($recipient) . '
			</body>
			</html>';
		} else {
			return 'Hi ' . $name . ',
			Many thanks for your message. We will be in touch shortly.

				' . EmailHelper::signature(EmailHelper::TYPE_TEXT) . '

				' . EmailHelper::disclaimer($recipient, EmailHelper::TYPE_TEXT);
		}
	}

}