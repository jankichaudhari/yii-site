<?php

class CronMoveFollowUpAppointmentsCommand extends CConsoleCommand
{
	private $outputLog = false;
	private $_log = [];

	public function run($args)
	{
		if (in_array('verbose', $args)) {
			$this->outputLog = true;
		}
		parent::run($args);
	}

	public function actionIndex($execute = true)
	{
		/** @var Deal $d */
		$d              = Deal::model()->with(['followUpAppointment' => ['together' => true]]);
		$missedFollowUp = $d->missedFollowUp()
							->findAll('followUpAppointment.app_start >= :rollOverStartingDate', ['rollOverStartingDate' => Yii::app()->params['followUpAppointments']['rollOverStartingDate']]);
		$i              = 0;
		foreach ($missedFollowUp as $key => $value) {
			/** @var Appointment $appointment */
			if ($appointment = $value->followUpAppointment) {
				$this->log("DealID: {$value->dea_id}, APP_ID:" . $appointment->app_id);
				$this->log("Current appointment start time: " . $appointment->app_start);
				$this->log("Current appointment end time: " . $appointment->app_end);
				$appointment->app_start = date("Y-m-d") . " " . date('H:i:s', strtotime($appointment->app_start));
				$appointment->app_end   = date("Y-m-d") . " " . date('H:i:s', strtotime($appointment->app_end));
				$this->log("Updated appointment start time: " . $appointment->app_start);
				$this->log("Updated appointment end time: " . $appointment->app_end);
				if ($execute) {
					$appointment->update(['app_start', 'app_end']);
				}
				$i++;
			}
		}

		$this->log('Appointments updated: ' . $i);
		if (!$execute) {
			$this->log('IT WAS A TEST RUN. NO APPS WERE UPDATED');
		}
	}

	public function log($message)
	{
		$this->_log[] = $message;
	}

	public function afterAction($action, $params, $exitCode = 0)
	{
		if ($this->outputLog) {
			echo implode("\n", $this->_log) . "\n";
		}
		array_unshift($this->_log, 'Launched at: ' . date("Y-m-d H:i:s"));
		file_put_contents(Yii::app()->params['logDirPath'] . '/moveFollowUpAppointment.log', implode("\n", $this->_log), FILE_APPEND);
		return parent::afterAction($action, $params, $exitCode);
	}
}