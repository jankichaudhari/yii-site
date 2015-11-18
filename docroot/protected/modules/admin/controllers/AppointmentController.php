<?php

class AppointmentController extends AdminController
{
	public static function createEditLink($id)
	{

		return '/v3.0/live/admin/appointment_edit.php?app_id=' . $id;
	}

	public function actionIndex()
	{

		$this->actionCreate();
	}

	public function actionCreate()
	{

		$model = new Appointment();

		$this->render('_appointment', array('model' => $model));
	}

//	public function actionUpdate($id)
//	{
//
//		$this->redirect('/v3.0/live/admin/appointment_edit.php?app_id=' . $id);
//		$model = Appointment::model()->findByPk($id);
//		$this->render('_appointment', array('model' => $model));
//	}

	public function actionUpdate($id)
	{
		$model = Appointment::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, "appointment [id = " . $id . "] was not found");
		}

		if (isset($_POST['Appointment'])) {
			$model->attributes = $_POST['Appointment'];

			if ($model->save()) {
				$this->redirect(['update', 'id' => $id]);
			}
		}

		$this->render('edit', compact('model'));
	}

	public static function createAppointmentUpdateLink($appId)
	{

		return '/v3.0/live/admin/appointment_edit.php?app_id=' . $appId;
	}

	public function actionSearch()
	{

		$model = new Appointment('search');
		if (!$model->app_type) {
			$model->app_type = [Appointment::TYPE_VIEWING, Appointment::TYPE_VALUATION];
		}

		if (!$model->app_notetype) {
			$model->app_notetype = Appointment::getNoteTypes();
		}

		if (!$model->app_start) {
			$model->app_start = date('d-m-Y', strtotime('-1 year'));
		}

		if (isset($_GET['Appointment']) && $_GET['Appointment']) {
			$model->attributes = $_GET['Appointment'];
		}

		$dataProvider                                                 = $model->search();
		$dataProvider->getCriteria()->select                          = ['t.app_status', 't.app_start', 't.app_type', 't.app_notetype', 't.app_subject', 't.app_user'];
		$dataProvider->getCriteria()->with['clients']['select']       = ['clients.cli_fname', 'clients.cli_sname'];
		$dataProvider->getCriteria()->with['_instructions']['select'] = ['_instructions.dea_id'];
		$dataProvider->getCriteria()->with['user']['select']          = ['user.use_fname', 'user.use_sname'];

		$this->render('search', compact('model', 'dataProvider'));
	}

	public function actionFeedback($id)
	{

		/** @var $model LinkDealToAppointment */
		$model = LinkDealToAppointment::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Feedback [id=' . $id . '] is not found');
		}

		if (isset($_POST['LinkDealToAppointment']) && $_POST['LinkDealToAppointment']) {
			$model->attributes = $_POST['LinkDealToAppointment'];
			if ($model->save()) {
				if (isset($_POST['submitOffer']) && $_POST['submitOffer']) {
					$this->redirect(['Offer/create', 'feedbackId' => $model->d2a_id]);
				}
			}
		}
		$this->render('feedback', ['model' => $model]);
	}

	public function actionView($id)
	{

		/** @var $model Appointment */
		$model = Appointment::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Appointment [id = ' . $id . '] is not found.');
		}

		if (isset($_POST['delete'])) {
			$model->deactivate();
			$this->redirect(['View', 'id' => $model->app_id]);
		}
		if (isset($_POST['restore'])) {
			$model->activate();
			$this->redirect(['View', 'id' => $model->app_id]);
		}

		if (isset($_POST['Appointment']) && $_POST['Appointment']) {
			$model->attributes = $_POST['Appointment'];

			if ($_POST['startDay']) {
				$model->app_start = Date::formatDate("Y-m-d", $_POST['startDay']);
			}
			if ($_POST['startTime']) {
				$model->app_start = date("Y-m-d H:i:s", strtotime($model->app_start . " " . $_POST['startTime']));
			}

			if ($_POST['endDay']) {
				$model->app_end = Date::formatDate("Y-m-d", $_POST['endDay']);
			}
			if ($_POST['endTime']) {
				$model->app_end = date("Y-m-d H:i:s", strtotime($model->app_end . " " . $_POST['endTime']));
			}

			if ($model->save()) {
				Yii::app()->user->setFlash('appointment-success', 'Updated!');
				$this->redirect(['View', 'id' => $model->app_id]);
			}
		}

		$this->render('view', compact('model'));
	}

	public function getTimeIntervals()
	{
		$time     = new DateTime("00:00");
		$interval = new DateInterval('PT15M');
		$result   = [];
		for ($i = 0; $i < 32; $i++) {
			$time = $time->add($interval);
			$str  = '';
			if ($h = (int)$time->format('H')) {
				$str .= $h . " " . ($h === 1 ? "hour" : ' hours') . " ";
			}
			if ($m = (int)$time->format('i')) {
				$str .= $m . ' minutes';
			}
			$result[$h * 60 + $m] = $str;
		}
		return $result;
	}
}