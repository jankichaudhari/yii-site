<?php
/**
 * This filter is disabled now because of some very strange error in CMysqlSchema class (line 231)
 */
class LogUserActionFilter extends CFilter
{
	/**
	 * @param CFilterChain $filterChain
	 * @return bool
	 */
	protected function preFilter($filterChain)
	{

		return parent::preFilter($filterChain);
		/**
		 * code beyond crashes from time to time because of some mistake in either yii or php regexp engine.
		 */

		if (Yii::app()->request->getIsAjaxRequest()) {
			return parent::preFilter($filterChain);
		}
		/** @var $session CHttpSession */
		$session = Yii::app()->session;

		$action                   = new LogUserAction();
		$action->userId           = Yii::app()->user->id;
		$action->method           = Yii::app()->request->getRequestType();
		$action->get_data         = serialize($_GET);
		$action->post_data        = serialize($_POST);
		$action->session          = Yii::app()->session->sessionID;
		$action->request          = Yii::app()->request->getRequestUri();
		$action->controller       = Yii::app()->controller->id;
		$action->action           = Yii::app()->controller->action->id;
		$action->ip               = Yii::app()->request->getUserHostAddress();
		$action->referer          = Yii::app()->request->getUrlReferrer();
		$action->previousActionId = $session->contains('lastActionId') ? $session->get('lastActionId') : '0';
		$action->save(false);
		$session->add('lastActionId', $action->id);
		return parent::preFilter($filterChain);
	}

}