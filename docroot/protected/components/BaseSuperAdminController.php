<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BaseSuperAdminController extends AdminController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout = '//layouts/adminDefault';

	public function __construct($id, $module = null)
	{

		parent::__construct($id, $module);
	}

	public function accessRules()
	{

		return array(
			array('allow', 'users'=> array("@")),
			array('deny'),
		);
	}

	public function filters()
	{

		return CMap::mergeArray(parent::filters(),
								array('SuperAdminOnly',)
		);
	}

	public function filterSuperAdminOnly(CFilterChain $filterChain)
	{

		if (!Yii::app()->user->is("SuperAdmin")) {
			throw new CHttpException("You don't have super admin privileges to access this page");
		}
		$filterChain->run();
	}

}