<?php

class LunaCinemaCommand extends CConsoleCommand
{
	private function log($msg)
	{
		file_put_contents(Yii::app()->params['logDirPath'] . '/lunacinema.log', $msg . "\n", FILE_APPEND);
	}

	public function actionIndex()
	{
		require_once 'Zend/Mail.php';

		$sql     = "SELECT * FROM client where (cli_saleemail = 'yes' or cli_letemail  = 'yes') and cli_email != '' and cli_email is not null";
		$data    = Yii::app()->db->createCommand($sql)->queryAll();
		$exclude = [];

		if (file_exists('luna/exclude')) {
			$exclude = file('luna/exclude');
		}

		foreach ($data as $client) {

			if (in_array($client['cli_email'], $exclude)) {
				$this->log('excluded ' . $client['cli_email']);
				continue;
			}

			try {
				$mail = new Zend_Mail("UTF-8");
				$mail->addTo($client['cli_email']);
				$mail->setFrom('admin@woosterstock.co.uk');
				$mail->setSubject('Win 2 tickets to the Luna Outdoor Cinema');
				$mail->setBodyHtml($this->renderFile(Yii::getPathOfAlias('application.commands.luna') . '/template.php', compact('client'), true));
				$this->log('Sending mail to ' . $client['cli_fname'] . ' [' . $client['cli_email'] . ']');
				$mail->send();
				$this->log('Mail sent to ' . $client['cli_fname'] . ' [' . $client['cli_email'] . ']');
			} catch (Exception $e) {
				$this->log('ERROR : Mail NOT sent to ' . $client['cli_fname'] . ' [' . $client['cli_email'] . ']');
			}
		}

	}
}
