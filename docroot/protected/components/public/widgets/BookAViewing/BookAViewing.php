<?php


/**
 * This widget relies on Zend_Mail components;
 *
 * @uses Zend_Mail
 */
class BookAViewing extends CWidget
{
	public $view = '';
	public $errors = array();
	public $successMessage = '';
	public $errorMessage = '';
	/** @var Deal */
	public $deal;

	public $name = "BookAViewing";

	public function init()
	{

		if (isset($_POST[$this->name])) {
			if (!$_POST[$this->name]['name']) {
				$this->errors[$this->name]['name'] = 'Name';
			}
			if (!$_POST[$this->name]['email'] || !filter_var($_POST[$this->name]['email'], FILTER_VALIDATE_EMAIL)) {
				$this->errors[$this->name]['email'] = 'Email';
			}

			if ($this->errors) {
				$this->errorMessage = 'In order for us to deal with your enquiry please provide the following info...<ul><li>' . implode("</li><li>", $this->errors[$this->name]) . "</li></ul>";

			} else {
				$this->successMessage = 'Your request has been sent, thank you';
				try {

					include_once("Zend/Mail.php");

					/** @var $branch Branch */
					$branch      = $this->deal->branch;
					$contactName = strip_tags($_POST[$this->name]['name']);
					$address     = $this->deal->property->address->getLine(3) . ', ' . $this->deal->property->getFirstPostcodePart();
					$viewing     = false;
					if ($this->deal->dea_status == 'Available') {
						$viewing = true;
					}

					$staffMessage = 'Name:        ' . $contactName . '
					Tel:         ' . $_POST[$this->name]['tel'] . '
					Email:       ' . $_POST[$this->name]['email'] . '

					Would like to arrange a viewing of:
					Address:     ' . $address . '
					Price:       ' . Locale::formatPrice($this->deal->dea_marketprice, $this->deal->dea_type == 'sales' ? false : true) . '
					Link:        http://' . Yii::app()->params['hostname'] . 'details/' . $this->deal->dea_id . '
					Date/Time:   ' . (isset($_POST[$this->name]['message']) ? $_POST[$this->name]['message'] : "") . '

					Property ID: ' . $this->deal->dea_id . '
					Sent:        ' . date("d/m/Y H:i") . '
					';

					if (!$viewing) {
						$staffMessage = 'Name:        ' . $contactName . '
											Tel:         ' . $_POST[$this->name]['tel'] . '
											Email:       ' . $_POST[$this->name]['email'] . '

											Would like to register interest in:
											Address:     ' . $address . '
											Price:       ' . Locale::formatPrice($this->deal->dea_marketprice, $this->deal->dea_type == 'sales' ? false : true) . '
											Link:        http://' . Yii::app()->params['hostname'] . '/details/' . $this->deal->dea_id . '

											Property ID: ' . $this->deal->dea_id . '
											Sent:        ' . date("d/m/Y H:i") . '
											';
					}

					$mailToStaff = new Zend_Mail("UTF-8");
					$mailToStaff->addTo($branch->bra_email);
					$mailToStaff->setFrom($branch->bra_email);
					$mailToStaff->setSubject($viewing ? "Arrange viewing: " . $address : "Register interest: " . $address);
					$mailToStaff->setBodyText($staffMessage);
					$mailToStaff->send();

					$mailToClient = new Zend_Mail('UTF-8');
					$mailToClient->addTo($_POST[$this->name]['email'], $contactName);
					$mailToClient->setFrom($branch->bra_email);
					$mailToClient->setSubject($viewing ? "Arrange viewing: " . $address : "Register interest: " . $address);
					$mailToClient->setBodyText($this->emailText('text', $_POST[$this->name]['email'], $contactName, $viewing));
					$mailToClient->setBodyHtml($this->emailText('html', $_POST[$this->name]['email'], $contactName, $viewing));
					$mailToClient->send();
				} catch (Exception $e) {
				}
				unset($_POST[$this->name]);
			}

		}
	}

	public function run()
	{

		$this->render($this->view ? $this->view : 'default', ['model' => $this->deal]);
	}

	public function emailText($format, $email, $name = null, $viewing = true)
	{

		if (!$name) { // any false value can't be name
			$name = $recipient = $email;
		} else {
			$recipient = $name . ' (' . $email . ')';
		}

		/** @var $officeData Office[] */
		if ($format === 'html') {

			$contactUsText = '<p>Contact our ' . $this->deal->branch->bra_title . ' office on ' . $this->deal->branch->bra_tel . '</p>
							<p>' . $this->deal->dea_strapline . '<br />' . $this->deal->property->address->getLine(3) . ', ' . $this->deal->property->getFirstPostcodePart() . '<br />' . Locale::formatPrice($this->deal->dea_marketprice, $this->deal->dea_type == 'sales' ? false : true) . '<br />
							<a href="http://' . Yii::app()->params['hostname'] . '/details/' . $this->deal->dea_id . '">' . Yii::app()->params['hostname'] . '/details/' . $this->deal->dea_id . '</a></p>
							</span>';

			if ($viewing) {
				return '<html>
				<head></head>
				<body>
				<span style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000">
				<p>Hi ' . $name . ',</p>
				<p>Many thanks for your interest. We will be getting back to you shortly to confirm your viewing. Please do call us at any time if you would like to speak to a negotiator to discuss your requirements further.</p>
				' . $contactUsText . '
				<p>Kind Regards,<br>Wooster & Stock</p>
				' . EmailHelper::signature() . '

				' . EmailHelper::disclaimer($recipient);

			} else {
				return '<span style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000000">
				<p>Hi ' . $name . ',</p>
				<p>Many thanks for your interest. We will let you know if this property comes back on the market. Please do call us at any time if you would like to speak to a negotiator to discuss your requirements further.</p>
				' . $contactUsText . '
				<p>Kind Regards,<br>Wooster & Stock</p>
				' . EmailHelper::signature() . '

				' . EmailHelper::disclaimer($recipient);
			}
		} else {
			if ($viewing) {
				return 'Hi ' . $name . ',

							Many thanks for your interest. We will be getting back to you shortly to confirm your viewing. Please do call us at any
							time if you would like to speak to a negotiator to discuss your requirements further.

							Contact our ' . $this->deal->branch->bra_title . ' office on ' . $this->deal->branch->bra_tel . '

							' . $this->deal->dea_strapline . '
							' . $this->deal->property->address->getLine(3) . ', ' . $this->deal->property->getFirstPostcodePart() . '
							' . Locale::formatPrice($this->deal->dea_marketprice, $this->deal->dea_type == 'sales' ? false : true) . '<br />
							' . Yii::app()->params['hostname'] . '/details/' . $this->deal->dea_id . '

							' . EmailHelper::signature(EmailHelper::TYPE_TEXT) . '

							' . EmailHelper::disclaimer($recipient, EmailHelper::TYPE_TEXT);
			} else {
				return 'Hi ' . $name . ',

				Many thanks for your interest. We will let you know if this property comes back on the market. Please do call us at any
				time if you would like to speak to a negotiator to discuss your requirements further.

				Contact our ' . $this->deal->branch->bra_title . ' office on ' . $this->deal->branch->bra_tel . '

				' . $this->deal->dea_strapline . '
				' . $this->deal->property->address->getLine(3) . ', ' . $this->deal->property->getFirstPostcodePart() . '
				' . Locale::formatPrice($this->deal->dea_marketprice, $this->deal->dea_type == 'sales' ? false : true) . '<br />
				' . Yii::app()->params['hostname'] . '/details/' . $this->deal->dea_id . '
				' . EmailHelper::signature(EmailHelper::TYPE_TEXT) . '

				' . EmailHelper::disclaimer($recipient, EmailHelper::TYPE_TEXT);
			}
		}
	}

}