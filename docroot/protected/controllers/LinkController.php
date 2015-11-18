<?php

class LinkController  extends PublicController
{
	/**
	 * @var string
	 */
	public $layout = "/layouts/default";

	/**
	 *
	 */
	public function actionIndex()
	{

		$dataProvider = new CActiveDataProvider('OuterLink');
		$this->render("index", array('dataProvider' => $dataProvider));
	}
}