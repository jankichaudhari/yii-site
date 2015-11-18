<?php
class AdminActiveForm extends CActiveForm {
	public function labelEx($model, $attribute, $htmlOptions = array())
	{
		$htmlOptions = CMap::mergeArray(array('class' => 'control-label'), $htmlOptions);
		return parent::labelEx($model, $attribute, $htmlOptions);
	}

}