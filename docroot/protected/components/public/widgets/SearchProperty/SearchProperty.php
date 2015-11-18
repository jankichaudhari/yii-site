<?php

class SearchProperty extends CWidget
{
	public $view = '';
	public $type = 'sales';

	public function init()
	{

		parent::init();
	}

	public function run()
	{

		parent::run();

		$model = new Deal();

		$this->render($this->view ? $this->view : 'default', array(
																  'model'     => $model,
																  'type'      => $this->type,
																  'minPrices' => Util::getPropertyPrices("minimum"),
																  'maxPrices' => Util::getPropertyPrices("maximum"),
																  'isMobile'    => Yii::app()->device->isDevice('mobile')
															 ));
	}

}