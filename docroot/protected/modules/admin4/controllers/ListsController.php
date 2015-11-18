<?php

class ListsController extends AdminController
{
	public $layout = "//layouts/admin_big_table";

	public function actionEdit()
	{
		$this->render('edit');
	}

	public function actionIndex()
	{

//		$dataProvider = new CActiveDataProvider('Lists', array(
//															  'pagination' => false/*array('pageSize' => 40)*/,
//															  'sort' => array('multiSort' => true),
//														 ));
		$model = new Lists('search');

		if(isset($_GET['Lists'])) {
			$model->attributes = $_GET['Lists'];
		}

		$dataProvider = $model->search();
		$dataProvider->pagination = false;
		$dataProvider->sort = array('multiSort' => true);
		$this->render('index', array('dataProvider'=>$dataProvider));
	}

	/*
	 * Used for TabbedLayout component
	 * Storing active tab id in session
	 */
	public function  actionSetSessionValue($key,$value){
		Yii::app()->session[$key] = $value;
	}
	/*
	 * Used for TabbedLayout component
	 */
}