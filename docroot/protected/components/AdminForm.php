<?php
class AdminForm extends CActiveForm
{
	public function controlLabel($model, $attribute, $htmlOptions = array())
	{

		$temp                      = CHtml::$afterRequiredLabel;
		CHtml::$afterRequiredLabel = '';
		$htmlOptions               = CMap::mergeArray(array('class' => 'control-label'), $htmlOptions);
		$label                     = parent::labelEx($model, $attribute, $htmlOptions);
		CHtml::$afterRequiredLabel = $temp;
		return $label;
	}

	public function labelEx($model, $attribute, $htmlOptions = array())
	{

		return parent::labelEx($model, $attribute, $htmlOptions);
	}

	public function beginControlGroup(CModel $model, $attributes, $htmlOptions = array())
	{

		if (!isset($htmlOptions['class'])) {
			$htmlOptions['class'] = 'control-group';
		}
		if (!is_array($attributes)) {
			$attributes = explode(',', str_replace(' ', '', $attributes));
		}
		foreach ($attributes as $attr) {
			if ($model->hasErrors($attr)) {

				$htmlOptions['class'] .= ' error';
			}
		}

		return CHtml::tag('div', $htmlOptions, false, false);
	}

	public function endControlGroup()
	{

		return '</div>';
	}

	public function separator()
	{

		return '<div class="separator"></div>';
	}

	public function checkBoxListWithSelectOnLabel($model, $attribute, $data, $htmlOptions = array())
	{

		$htmlOptions['data-attribute'] = $attribute;

		if (isset($htmlOptions['labelOptions']['class']) && $htmlOptions['labelOptions']['class']) {
			$htmlOptions['labelOptions']['class'] .= ' checkbox-enabler attribute-' . $attribute;
		} else {
			$htmlOptions['labelOptions']['class'] = 'checkbox-enabler attribute-' . $attribute;
		}

		/** @var $cs CClientScript */
		$cs = Yii::app()->getClientScript();
		$cs->registerScript('checkBoxListWithSelectOnLabel-' . $attribute, "
				$('.checkbox-enabler.attribute-" . $attribute . "').on('click', function(e) {
					e.preventDefault();
					$('[data-attribute=\"" . $attribute . "\"]').attr('checked', false);
					$('#' + $(this).attr('for')).attr('checked', true);
					$('#' + $(this).attr('for')).trigger('change');
				});
			");
		return parent::checkBoxList($model, $attribute, $data, $htmlOptions);
	}

}