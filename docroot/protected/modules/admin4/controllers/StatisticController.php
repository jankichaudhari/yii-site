<?php

class StatisticController extends AdminController
{
	public $layout = "//layouts/admin_big_table";

	public function actionIndex()
	{
		$model = new PageViewStatistic('search');

		if(isset($_GET['PageViewStatistic'])) {
			$model->attributes = $_GET['PageViewStatistic'];
		}

		$criteria        = new CDbCriteria();
		$criteria->order = "page";

//		$dataProvider = new CActiveDataProvider('PageViewStatistic', array('criteria'   => $criteria,
//																		   'pagination' => array('pageSize' => 37)
//																	 ));
		$dataProvider = $model->search($criteria);
		$this->render("statisticIndex", array('model' => $model, 'dataProvider' => $dataProvider));
	}
}