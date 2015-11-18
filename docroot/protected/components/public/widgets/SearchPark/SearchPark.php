<?php

class SearchPark extends CWidget
{
	public $view = '';

	public function init()
	{
		parent::init();
	}


	public function run()
	{
		/** @var  $detector \Device */
		$isMobile = Yii::app()->device->isDevice('mobile') ? true : false;

		parent::run();

		$model = new Place();

		$this->render($this->view ? $this->view : 'default',[
									 'model'=>$model,
									 'mobile'=>$isMobile
								]);
	}

}