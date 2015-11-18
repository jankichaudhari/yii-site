<?php
class Admin4Module extends CWebModule
{

		public function init()
	{
		parent::init();
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		Yii::app()->urlManager->urlSuffix = '';

		Yii::app()->setComponents(array(
									   'errorHandler'=> array(
										   'errorAction'=> 'admin4/Site/error',
									   ),
								  ));
		// import the module-level models and components
		$this->setImport(array(
							  'admin4.models.*',
							  'admin4.components.*',
							  'admin4.controllers.*',
						 ));
	}

}
