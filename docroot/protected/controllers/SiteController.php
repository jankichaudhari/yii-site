<?php

class SiteController extends PublicController
{
	public $layout = "//layouts/default";

	public function actionLogin()
	{

		$this->redirect('/admin4/site/login');
	}

	public function actions()
	{

		return CMap::mergeArray(parent::actions(), array(
														'page' => array(
															'class' => 'CViewAction',
														),
												   ));
	}

	public function actionIndex()
	{

		$videoCr         = new CDbCriteria();
		$videoCr->order  = 'displayOrder ASC';
		$videoCr->scopes = ['publicAvailableInstruction'];
		$videoCr->limit  = 6;

		$this->render('index',
					  array(
						   'latestProperties'   => Deal::model()->getLatest(6),
						   'featuredVideo'      => InstructionVideo::model()->with('instruction')->findByAttributes(['featuredVideo' => 1]),
						   'mostViewed'         => Deal::model()->getMostViewed(1, date("Y-m-d H:i:s", strtotime("-15 days"))),
						   'instructionVideos'  => InstructionVideo::model()->with('instruction')->findAllByAttributes(['displayOnSite' => 1], $videoCr),
						   'propertyCategories' => PropertyCategory::model()->active()->displayOnHome()->findAll()
					  )
		);

	}

	public function actionProp()
	{

		$model = new Deal();
		$this->render('_salesSearch', ['model' => $model, 'type' => 'sales']);
	}

	public function actionLunacinema($clientId = null, $goto = null)
	{

		$redirect             = new Redirect();
		$redirect->clientId   = $clientId ? : 0;
		$redirect->redirected = $goto ? : 'index';
		$redirect->url        = Yii::app()->getRequest()->getRequestUri();
		$redirect->comment    = 'redirect for luna cinema';
		$redirect->save();

		$this->redirect($goto ? : $this->createUrl('site/index'));

	}

	public function actionSetDevice($type)
	{

		/** @var $device  \Device */
		Yii::app()->device->setDevice($type);
		header('Location:' . $_SERVER['PHP_SELF']);
	}
}
