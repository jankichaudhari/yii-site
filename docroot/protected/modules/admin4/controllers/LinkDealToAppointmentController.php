<?php

class LinkDealToAppointmentController extends AdminController
{


	public static function createAppointmentLinkUsingDeal($instructionId, $appStartDate, $appType = Appointment::TYPE_VIEWING)
	{

		if (!$instructionId || !$appStartDate) {
			return false;
		}

		$appointmentsByDeal = LinkDealToAppointment::model()->findAllByAttributes(['d2a_dea' => $instructionId]);
		$appIdsByDeal       = [];
		foreach ($appointmentsByDeal as $value) {
			/** @var $value LinkDealToAppointment[ ] */
			if ($value->appointment->app_start == $appStartDate && $value->appointment->app_type == $appType) {
				$appIdsByDeal [] = $value->appointment->app_id;
			}
		}

		return AppointmentController::createAppointmentUpdateLink(reset($appIdsByDeal));
	}
}