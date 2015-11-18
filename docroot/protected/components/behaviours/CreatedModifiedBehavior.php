<?php
class CreatedModifiedBehavior extends CActiveRecordBehavior
{

	public $createdField = 'created';
	public $createdByField = 'createdBy';
	public $modifiedField = 'modified';
	public $modifiedByField = 'modifiedBy';

	public function beforeSave($event)
	{

		$model = $this->owner;

		if (!$model->hasAttribute($this->createdField)) {
			throw new InvalidArgumentException('Field ' . $this->createdField . ' is not found in a model of class ' . get_class($model));
		}

		if (!$model->hasAttribute($this->createdByField)) {
			throw new InvalidArgumentException('Field ' . $this->createdByField . ' is not found in a model of class ' . get_class($model));
		}

		if (!$model->hasAttribute($this->modifiedField)) {
			throw new InvalidArgumentException('Field ' . $this->modifiedField . ' is not found in a model of class ' . get_class($model));
		}

		if (!$model->hasAttribute($this->modifiedByField)) {
			throw new InvalidArgumentException('Field ' . $this->modifiedByField . ' is not found in a model of class ' . get_class($model));
		}

		if ($model->isNewRecord) {
			$model->{$this->createdField}   = date("Y-m-d H:i:s");
			$model->{$this->createdByField} = (isset(Yii::app()->user->id) ? Yii::app()->user->id : 0);
		}

		$model->{$this->modifiedField}   = date("Y-m-d H:i:s");
		$model->{$this->modifiedByField} = (isset(Yii::app()->user->id) ? Yii::app()->user->id : 0);

		parent::beforeSave($event);
	}

}