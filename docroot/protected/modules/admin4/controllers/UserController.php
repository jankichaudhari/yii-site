<?php

class UserController extends AdminController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
//	public $layout = '//layouts/admin_big_table';
	public $layout = "//layouts/adminDefault";

	/**
	 * @return array action filters
	 */
	public function filters()
	{

		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{

		$model = new User;
		$this->edit($model);
		$this->render('create', array(
									 'model' => $model,
								));
	}

	/*
	 * Add new roles and delete old roles for the user in update operation
	 */
	public function edit(User $model)
	{

		if (isset($_POST['User'])) {
			$model->attributes = $_POST['User'];

			if ($model->save()) {
				LinkUserToRole::model()->deleteAll("u2r_use=" . $model->use_id);
				if (isset($_POST['User']['role'])) {
					$userRoles = $_POST['User']['role'];
					foreach ($userRoles as $userRoleKey => $userRoleValue) {
						if ($userRoleValue == 1) {
							$user2role = new LinkUserToRole();
							$user2role->unsetAttributes();
							$user2role->u2r_use = $model->use_id;
							$user2role->u2r_rol = $userRoleKey;
							$user2role->save();
						}
					}
				}

				$this->saveEmailAlerts($model);

				$this->redirect(array('update', 'id' => $model->use_id));
			}
		}
	}

	/**
	 * @param User $model
	 * Add new email alerts and delete older
	 */
	private function saveEmailAlerts(User $model)
	{

		if (isset($_POST['User'])) {
			UserConfig::model()
			->deleteAll("userId=" . $model->use_id . " AND configType ='" . UserConfig::TYPE_EMAIL_ALERT . "' AND configKey='" . UserConfig::KEY_EMAIL_ALERT_DEAL_STATUS . "'");
			if (isset($_POST['User']['emailAlertForDealStatus'])) {
				$emailAlerts = $_POST['User']['emailAlertForDealStatus'];
				foreach ($emailAlerts as $k => $v) {
					if ($v == 1) {
						$userConfig = new UserConfig();
						$userConfig->unsetAttributes();
						$userConfig->userId      = $model->use_id;
						$userConfig->configType  = UserConfig::TYPE_EMAIL_ALERT;
						$userConfig->configKey   = UserConfig::KEY_EMAIL_ALERT_DEAL_STATUS;
						$userConfig->configValue = $k;
						$userConfig->save();
					}
				}
			}
		}
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{

		$this->layout = '//layouts/adminDefault';
		$model        = $this->loadModel($id);
		$model->setScenario('update');
		$this->edit($model);
		$this->render('update', array(
									 'model' => $model,
								));
	}

	public function actionUserPreferences()
	{

		$this->layout  = '//layouts/adminDefault';
		$currentUserId = Yii::app()->user->getId();
		$model         = $this->loadModel($currentUserId);
		if (isset($_POST['User'])) {

			$model->attributes = $_POST['User'];

			if ($model->save()) {
				$this->saveEmailAlerts($model);
				$this->redirect(array('UserPreferences'));
			}
		}

		$this->render('preferences', array(
										  'model' => $model,
									 ));
	}

	public function actionUpdatePassword($id)
	{

		/** @var  $user User [ ] */
		if (isset($_POST['password']) && $_POST['password'] && $id) {
			$user = User::model()->findByPk($id);
			unset($user->use_password);
			$user->use_password = $_POST['password'];
			echo $user->save();
		}
	}

	/**
	 * Action to display User selection window
	 * should accept some kind of a callback to call in parent window(opener) on selection
	 */
	public function actionSelect()
	{

		$this->layout = "//layouts/selectScreen";

		$model = new User('search');
		$this->render('select', array('model' => $model));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{

		if (Yii::app()->request->isPostRequest) {
			if (!isset($_GET['ajax'])) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}
		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{

		$this->layout = '//layouts/adminDefault';
		$dataProvider = new CActiveDataProvider('User',
												array(
													 'pagination' => array('pageSize' => 30),
													 'criteria'   => array(
														 'scopes' => array('onlyActive')
													 ),
												));

		$model = new User('search');
		if (!empty($_GET['resetFilter_user_index_user-filter-form'])) {
			$model->use_scope  = array(' ', ' ');
			$model->use_status = array(' ', ' ');
		} else {
			$model->use_scope = array('Sales', 'Lettings');
		}

		$this->render('index', array(
									'dataProvider' => $dataProvider,
									'model'        => $model
							   ));
	}

	public function actionGetJSON()
	{

		$models = User::model()->findAllByPk($_GET['User']);
		$result = array();
		foreach ($models as $value) {
			$result[] = $value->toArray();
		}
		echo json_encode($result);

	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param $id
	 * @throws CHttpException
	 * @return \CActiveRecord
	 */
	public function loadModel($id)
	{

		$model = User::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{

		if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionStaff()
	{

		$model = new User('search');
		$this->render('staff', compact('model'));
	}
}
