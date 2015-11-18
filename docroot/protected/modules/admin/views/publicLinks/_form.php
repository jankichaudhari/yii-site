<?php
/**
 * @var $form  CActiveForm
 * @var $model CActiveRecord
 * @var $this  CController
 */
?>
<div class="form wide">
	<?php $form = $this->beginWidget('CActiveForm', array(
														 'id'                  => 'public-links-form',
														 'enableAjaxValidation'=> false,
														 'htmlOptions' =>array('enctype'=> 'multipart/form-data',)
													)); ?>
	<fieldset>
		<div class="block-header">Create new public link</div>
		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<?php echo $form->errorSummary($model); ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'title'); ?>
			<?php echo $form->textField($model, 'title', array('size'     => 60,
															   'maxlength'=> 255)); ?>
			<?php echo $form->error($model, 'title'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model, 'link'); ?>
			<?php echo $form->textField($model, 'link', array('size'=> 60)); ?>
			<?php echo $form->error($model, 'link'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model, 'description'); ?>
			<?php echo $form->textArea($model, 'description', array('rows'=> 6,
																	'cols'=> 50)); ?>
			<?php echo $form->error($model, 'description'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model, 'image'); ?>
			<?php echo $form->fileField($model, 'image') ?>
			<?php echo $form->error($model, 'image'); ?>
		</div>
		<div class="row buttons">
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
		</div>
	</fieldset>

	<?php $this->endWidget(); ?>

</div><!-- form -->