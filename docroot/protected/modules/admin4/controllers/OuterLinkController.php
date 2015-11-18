<?php
/**
 * Created by JetBrains PhpStorm.
 * User: janki.chaudhari
 * Date: 17/06/13
 * Time: 11:45
 * To change this template use File | Settings | File Templates.
 */

class OuterLinkController extends AdminController
{

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
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{

		return array(
			array(
				'allow', // allow authenticated user to perform 'create' and 'update' actions
				'users' => array('@'),
			),
			array(
				'allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions' => array('admin', 'delete'),
				'users'   => array('admin'),
			),
			array(
				'deny', // deny all users
				'users' => array('*'),
			),
		);
	}

	private function loadModel($id)
	{

		if (!$id) {
			throw new CException("Outer Link id must be passed");
		}
		$model = OuterLink::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	public function actionIndex()
	{

		$dataProvider = new CActiveDataProvider('OuterLink',
												array(
													 'pagination' => array('pageSize' => 30)
												));

		$this->render('index', array(
									'dataProvider' => $dataProvider
							   ));
	}

	private function edit(OuterLink $model)
	{

		if (isset($_POST['OuterLink'])) {
			$model->attributes = $_POST['OuterLink'];
			if ($model->save()) {
				$this->redirect(array('update', 'id' => $model->id));
			}
		}
	}

	public function actionCreate()
	{

		$model = new OuterLink();
		$this->edit($model);

		$this->render('create', array(
									 'model' => $model
								));
	}

	public function actionUpdate($id)
	{

		$model = $this->loadModel($id);
		$photo = $model->image ? $model->image : new OuterLinkImage();
		$this->edit($model);

		$this->render('update', array(
									 'model' => $model,
									 'photo' => $photo
								));
	}

	public function actionOuterLinkPhotos($id)
	{

		$this->layout = '//layouts/new/main';
		$model        = $this->loadModel($id);
		if (isset($_POST['uploadOuterLinkImage']) && $_POST['uploadOuterLinkImage']) {
			$images = CUploadedFile::getInstancesByName('OuterLinkImage');
			if ($images) {
				foreach ($images as $num => $pic) {
					$photo           = new OuterLinkImage();
					$photo->recordId = $id;
					$photo->file     = $pic;
					if (!$photo->save()) {
						echo "<pre style='color:blue' title='" . __FILE__ . "'>" . basename(__FILE__) . ":" . __LINE__ . "<br>";
						print_r($photo->getErrors());
						echo "</pre>";
					}
				}
			}
		}

		$this->render('photos', array(
									 'model' => $model
								));

	}
}