<?php

class AdminFilterForm extends AdminForm
{

	public $model;
	public $filterId;
	public $ajaxFilterGrid;
	public $onAfterAjaxFilter;
	public $onBeforeAjaxFilter;

	public $storeInSession = true;

	public $method = 'get'; // filtering by default goes by GET

	public $methodVar;

	public function init()
	{

		$methodVar = $this->getMethodVar();

		$filterId  = $this->getFilterId();
		$filterVar = 'filter_' . $filterId;

		if (isset($methodVar['resetFilter_' . $filterId . ''])) {
			Yii::app()->session->remove($filterVar);

			unset($_GET['resetFilter_' . $filterId . '']);
			foreach ($this->getModel() as $key => $value) {
				if (isset($_GET[get_class($value)])) {
					unset($_GET[get_class($value)]);
				}
			}

			$str = Yii::app()->request->getBaseUrl() . '/' . Yii::app()->request->getPathInfo() . '?' . http_build_query($_GET);
			Yii::app()->request->redirect($str);
			Yii::app()->end();

		}
		$filterData = array();

		if (isset($methodVar[$filterVar])) {
			if ($this->storeInSession) {
				Yii::app()->session->add($filterVar, serialize($methodVar));
			}
			$filterData = $methodVar;
		} else {
			/** @var $session CHttpSession */
			$session = Yii::app()->session;
			if ($this->storeInSession && !isset($methodVar['resetFilter_' . $filterId]) && $session[$filterVar]) {
				$filterData = unserialize($session[$filterVar]);
			}
		}

		if (isset($filterData) && $filterData) {
			$this->loadFilterDataToModel($filterData);
		}

		parent::init();
		echo CHtml::hiddenField($filterVar, true);
		$this->registerScripts();

	}

	public function getFilterId()
	{

		return $this->getId();
	}

	public function registerScripts()
	{

		/** @var $cs CClientScript */
		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile('/js/AdminFilterForm.js');

		if ($this->ajaxFilterGrid) {
			$cs->registerScript('ajaxFilterForm_' . $this->getId(), 'AdminFilterForm("' . $this->getId() . '", "' . $this->ajaxFilterGrid . '", "' . $this->getFilterId() . '").init()', CClientScript::POS_END);
			if ($this->onBeforeAjaxFilter) {
				$cs->registerScript('beforeAjaxFilter_' . $this->getId(), 'AdminFilterForm("' . $this->getId() . '").attachEvent("onBeforeAjaxFilter", ' . $this->onBeforeAjaxFilter . ')', CClientScript::POS_END);
			}
			if ($this->onAfterAjaxFilter) {
				$cs->registerScript('afterAjaxFilter_' . $this->getId(), 'AdminFilterForm("' . $this->getId() . '").attachEvent("onAfterAjaxFilter", ' . $this->onAfterAjaxFilter . ')', CClientScript::POS_END);
			}
		}
	}

	public function filterResetButton($string = 'Reset filter', $htmlOptions = array())
	{

		$htmlOptions = CMap::mergeArray(array('onclick' => 'AdminFilterForm("' . $this->getId() . '").reset()'), $htmlOptions);
		return CHtml::button($string, $htmlOptions);
	}

	private function loadFilterDataToModel($data)
	{

		$models = $this->getModel();
		/** @var $model CActiveRecord */
		/** @var $model Filterable */
		foreach ($models as $model) {
			if (isset($data[get_class($model)]) && $data[get_class($model)]) {
				foreach ($data[get_class($model)] as $key => $value) {
					if (isset($model->$key)) {
						$model->$key = $value;
					}
				}

			}
		}

		if (isset($data['excluded']) && $data['excluded']) {
			if (is_array($data['excluded'])) {
				foreach ($models as $model) {
					if ($model instanceof Filterable) {
						if (isset($data['excluded'][get_class($model)]) && $data['excluded'][get_class($model)]) {
							$criteria = new CDbCriteria();
							$criteria->addNotInCondition($model->tableSchema->primaryKey, explode(",", $data['excluded'][get_class($model)]));
							$model->setFilterCriteria($criteria);

						}
					}
				}
			} else {
				$model = reset($models);
				if ($model instanceof Filterable) {
					$criteria = new CDbCriteria();
					$criteria->addNotInCondition($model->tableSchema->primaryKey, explode(",", $data['excluded']));

					$model->setFilterCriteria($criteria);
				}
			}
		}
	}

	/**
	 * returns $_REQUEST; unfortunately it's hard to controll which type of request it was.
	 * when it's ajax it's always POST because JQuery.load() function sends post requests when data is passed
	 * if ajax is not used we use $method. by default it's get.
	 *
	 * At the end of the day that does not matter. we keep flag indicating that it was filter form submission.
	 *
	 * @return array
	 */
	private function getMethodVar()
	{

		return $_REQUEST;
	}

	/**
	 * Always returns an array of models; even if originally passed a model instance
	 *
	 * @return array
	 */
	private function getModel()
	{

		return is_array($this->model) ? $this->model : array($this->model);
	}

	public static function generateResetParam($name)
	{

		return '?resetFilter_' . $name;
	}

}
