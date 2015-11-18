<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class PublicController extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout = '//layouts/main';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu = array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs = array();

	/** @var ViewStatistic */
	protected $viewStatistic;

	public function actions()
	{
		return array(
			'captcha' => array(
				'class'     => 'CCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	protected function beforeAction($action)
	{
		if (!isset($_COOKIE['cookies_accepted'])) {
			setcookie('cookies_accepted', 'no', pow(2, 31));
			$_COOKIE['cookies_accepted'] = 'no';
		} elseif ($_COOKIE['cookies_accepted'] === 'no') {
			setcookie('cookies_accepted', 'yes', pow(2, 31));
			$_COOKIE['cookies_accepted'] = 'yes';
		}
		header('Access-Control-Allow-Origin: http://test.woosterstock.co.uk');
		$this->viewStatistic = new ViewStatistic();
		return parent::beforeAction($action); // TODO: Change the autogenerated stub
	}

	protected function afterAction($action)
	{

		$this->viewStatistic->run();
		parent::afterAction($action); // TODO: Change the autogenerated stub
	}

}