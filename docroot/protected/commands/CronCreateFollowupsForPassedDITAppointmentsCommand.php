<?php

class CronCreateFollowupsForPassedDITAppointmentsCommand extends ConsoleCommand
{
	public function actionIndex()
	{
		$apps = Appointment::model()->DIT(Appointment::DIT_BOOKED)->passed()->findAll();
		foreach ($apps as $app) {
			$this->log('Booking follow up for app id=' . $app->app_id);
			$followUp = $app->bookDITFollowUp();
			$this->log('newly created follow up id=' . $followUp->app_id);
		}
	}
}
