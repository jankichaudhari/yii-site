<?php
class PublicModule extends CWebModule
{
	/** @var ViewStatistic */
	protected $viewStatistic;

	public function init()
	{

		parent::init();
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		Yii::app()->setComponents(array(
									   'errorHandler'=> array(
										   'errorAction'=> 'public/publicSite/page/view/notFound',
									   ),
								  ));
		// import the module-level models and components
		$this->setImport(array(
							  'public.models.*',
							  'public.components.*',
						 ));
	}

	public function beforeControllerAction($controller, $action)
	{

		$this->viewStatistic = new ViewStatistic();
		if (parent::beforeControllerAction($controller, $action)) {
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		} else {
			return false;
		}
	}

	public function afterControllerAction($controller, $action)
	{

		$this->viewStatistic->run();
		parent::afterControllerAction($controller, $action);
	}

}
