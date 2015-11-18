<?php

class SiteController extends PublicController
{
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{

		$this->redirect("/v3.0/live/admin/index.php"); // temporary redirect to old admin, we will remove that in future
		$this->render('index');
	}

	public function filters()
	{
		return ['accessControl'];
	}

	public function accessRules()
	{
		return array(
			['allow', 'actions' => ['guestLogin', 'emailSentSuccess', 'error', 'layoutExample'], 'users' => ["@"]],
			['deny', 'actions' => ['emailSendSuccess', 'error', 'layoutExample']],
			['allow', 'users' => ['@']],
			['allow', 'actions' => ['login'], 'users' => ['*']],
			['deny', 'users' => ['*']],
		);
//		return parent::accessRules();
	}

	public function actionGuestLogin()
	{
		echo 123;
		exit;
		$this->layout = "/layouts/guestLogin";
		$this->render('guestHome');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{

		if ($error = Yii::app()->errorHandler->error) {
			if (Yii::app()->request->isAjaxRequest) {
				echo $error['message'];
			} else {
				file_put_contents(Yii::app()->params['logDirPath'] . '/site-error-' . date("Y-m-d_h_i") . '-' . (isset(Yii::app()->user->id) ? Yii::app()->user->id : "NO_USER") . '.log', print_r($error, true));
				$this->render('error', ['error' => $error]);
			}
		}
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{

		$model = new LoginForm;

		// if it is ajax validation request
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if ($model->validate() && $model->login()) {
				if (Yii::app()->user->is('guest')) {
					$this->redirect('guestLogin');
				}
				if (isset($_GET['ref']) && $_GET['ref'] && urldecode($_GET['ref']) != "/admin/") {
					$this->redirect(urldecode($_GET['ref']));
				} elseif (strpos(Yii::app()->user->returnUrl, '/admin4') !== false) {
					$this->redirect(Yii::app()->user->returnUrl);
				}

				$this->redirect("/v3.0/live/admin/index.php");
			}
		}
		// display the login form
		$this->renderPartial('login', ['model' => $model]);
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{

		Yii::app()->user->logout();
		$this->redirect('Login');
	}

	/**
	 * temporary method to display success message when email is sent via matching properties.
	 */
	public function actionEmailSentSuccess()
	{

		$this->layout = '//layouts/adminDefault';
		$this->render('emailSentSuccess');

	}

	public function actionTools()
	{
		$this->layout = '//layouts/adminDefault';
		$this->render('tools');
	}

}