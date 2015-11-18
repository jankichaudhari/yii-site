<?php

/**
 * Created by PhpStorm.
 * User: janki.chaudhari
 * Date: 24/02/14
 * Time: 17:00
 */
class ClientController extends PublicController
{

	public $layout = "//layouts/default";

	public function sendEmailForRegisteredClient($thisEmail, $thisTel)
	{

		$callbackMsg = null;
		$criteria    = new CDbCriteria();
		$criteria->compare('cli_email', $thisEmail);
		$criteria->with = ['branch'];
		/** @var $client Client */
		$client    = Client::model()->find($criteria);
		$recipient = 'cam@woosterstock.co.uk';

		//Find recipient and send email
		if (!empty($client->branch->bra_email)) {
			$recipient     = $client->branch->bra_email;
			$recipientNote = "The client is assigned to branch " . $client->branch->bra_title;
		} else {
			$cliSales    = strtolower($client->cli_sales);
			$cliLettings = strtolower($client->cli_lettings);
			if ($cliSales == 'yes') {
				$recipientNote = "The client is not assigned to any branch, is registered for sales";
			} else {
				if ($cliLettings == 'yes') {
					$recipientNote = "The client is not assigned to any branch, is registered for lettings";
				} else {
					$recipientNote = "The client is not assigned to any branch, is not registered for lettings or sales";
				}
			}

		}

		$fromEmail    = 'noreply@woosterstock.co.uk';
		$emailMessage = "From:\t" . $fromEmail . "\n\n";

		$emailMessage .= "
					A client has requested a callback.

					- Open client record: https://www.woosterstock.co.uk/admin4/client/update/id/" . $client->cli_id .
				"- Check the Contact Log to make sure no one else has already made the call
					- Make the call (note that the phone number in this email may differ from the one on record)
					- Make a record of the call in the Contact Log system

					Client:      " . $client->cli_id . "
					Telephone:  " . $thisTel . "

					**********************************************************************************************
					This email was sent to $recipient
					$recipientNote
					**********************************************************************************************

					 ";

		$emailMessage .= "Sent:\t" . date("d/m/Y H:i");

		try {
			include_once("Zend/Mail.php");

			$mailToFriend = new Zend_Mail("UTF-8");
			$mailToFriend->addTo($recipient);
			$mailToFriend->setFrom($fromEmail);
			$mailToFriend->setSubject("Web Site recommendation from your friend or colleague");
			$mailToFriend->setBodyText($emailMessage);
			$mailToFriend->send();
			$callbackMsg = 'Thanks, someone will call you shortly.';

		} catch (Exception $e) {
			$callbackMsg = 'Error!! Your message has not been sent. Please use contact details';
		}
		return $callbackMsg;
	}

	public function actionRegister()
	{

		$model = new PublicClientRegisterForm();
//		$model->branch = Branch::model()->registerClients()->findAll()[0]->bra_id;
		$result = ['type' => '', 'html' => ''];

		if (isset($_POST['PublicClientRegisterForm']) && $_POST['PublicClientRegisterForm']) {

			$model->attributes = $_POST['PublicClientRegisterForm'];

			if ($model->register()) {
				$result['type'] = 'success';
				$result['html'] = '<div class="green">You registered successfully. We will be in touch with you very soon...</div>';
			}

			if ($model->errors) {
				if (array_key_exists('registeredInfo', $model->errors)) {
					/** @var  \Device */
					if (Yii::app()->device->isDevice('mobile')) {
						$this->actionCallback($model->email, $model->telephone, $model->errors["registeredInfo"][0]);
						return;
					} else {
						$result['type'] = 'callback';
						$result['html'] = '';
					}
				} else {
					$result['type'] = 'error';
					$data           = '<ul>';
					foreach ($model->errors as $key => $value):
						$data = $data . '<li>' . $model->getAttributeLabel($key) . ' : ' . $model->getError($key) . '</li>';
					endforeach;
					$data           = $data . '</ul>';
					$result['html'] = $data;
				}
			}

		}

		$this->render('register', [
				'model'  => $model,
				'result' => $result
		]);
	}

	public function actionCallback($email = '', $telephone = '', $message = '')
	{

		$this->layout = '/layouts/popup-iframe';

		if (isset($_POST['Callback']) && $_POST['Callback']) {

			$callback  = $_POST['Callback'];
			$email     = $callback['email'];
			$telephone = $callback['telephone'];

			$criteria = new CDbCriteria();
			$criteria->compare('cli_email', $email);
			$criteria->with = ['telephones'];
			$client         = Client::model()->find($criteria);

			if ($client) {

				if (!$telephone) {
					$telephone = $client->telephones[0]->tel_number;
				} else {
					if ($client->telephones[0]->tel_number != $telephone) {
						$telExist = false;
						$tels     = $client->telephones;
						foreach ($tels as $tel) {
							if ($tel->tel_number == $telephone) {
								$telExist = true;
								break;
							}
						}
						if (!$telExist) {
							$telephone             = new Telephone();
							$telephone->tel_type   = 'Other';
							$telephone->tel_number = $telephone;
							$telephone->tel_cli    = $client->cli_id;
							$telephone->save();
						}
					}
				}
			}
			$message = $this->sendEmailForRegisteredClient($email, $telephone);
			echo json_encode($message);
		} else {

			$this->render('_callbackForm', [
					'email'     => $email,
					'telephone' => $telephone,
					'message'   => $message
			]);
		}
	}
}