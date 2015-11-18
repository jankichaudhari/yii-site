<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class AdminController extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout = '//layouts/adminDefault';
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

	/**
	 * Link address to go back
	 * @var string
	 */
	public $backLink = '';

	public function __construct($id, $module = null)
	{

		parent::__construct($id, $module);
	}

	public function accessRules()
	{

		return array(
			array('deny', 'expression' => 'Yii::app()->user->is("guest")'),
			array('allow', 'users' => array("@")),
			array('deny'),
		);
	}

	public function filters()
	{

		return array(
			'accessControl', // perform access control for CRUD operations
			array('application.components.filters.LogUserActionFilter'),
		);
	}

	public function filterSuperAdminOnly(CFilterChain $filterChain)
	{

		if (!Yii::app()->user->is("SuperAdmin")) {
			throw new CHttpException("You don't have super admin privileges to access this page");
		}
		$filterChain->run();
	}

	public function filterGuestView(CFilterChain $filterChain)
	{
		if (Yii::app()->user->is("guest")) {
			$this->layout='/layouts/guestLogin';
		}
		$filterChain->run();
	}

}