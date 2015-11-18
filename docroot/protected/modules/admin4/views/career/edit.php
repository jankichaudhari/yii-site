<?php
/**
 * @var CareerController $this
 * @var Career           $model
 * @var AdminForm        $form
 */
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js');
?>
<?php $form = $this->beginWidget('AdminForm', array(
												   'id'                   => 'career-form',
												   'enableAjaxValidation' => false,
											  )); ?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header"><?php echo $model->isNewRecord ? "Create new Career" : "Update Career" ?></div>
			<?php if ($model->hasErrors()): ?>
				<?php echo $form->errorSummary($model); ?>
			<?php endif ?>
			<div class="content">
				<?= $form->beginControlGroup($model, 'name'); ?>
				<?= $form->controlLabel($model, 'name'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'name'); ?>
				</div>
				<?= $form->endControlGroup(); ?>
			<?= $form->beginControlGroup($model, 'email'); ?>
			<?= $form->controlLabel($model, 'email'); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'email') ?>
			</div>
			<?= $form->endControlGroup(); ?>
			<?= $form->beginControlGroup($model, 'listOrder'); ?>
			<?= $form->controlLabel($model, 'listOrder'); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'listOrder', ['class' => 'input-xsmall']) ?>
			</div>
			<?= $form->endControlGroup(); ?>
			<?= $form->beginControlGroup($model, 'description'); ?>
			<?= $form->controlLabel($model, 'description'); ?>
			<div class="controls">
				<?php echo $form->textArea($model, 'description', ['class' => 'input-large']) ?>
			</div>
			<?= $form->endControlGroup(); ?>
			<?= $form->beginControlGroup($model, 'skillsRequired'); ?>
			<?= $form->controlLabel($model, 'skillsRequired'); ?>
			<div class="controls">
				<?php echo $form->textArea($model, 'skillsRequired', ['class' => 'input-large']) ?>
			</div>
			<?= $form->endControlGroup(); ?>
			<?= $form->beginControlGroup($model, 'isActive'); ?>
			<?= $form->controlLabel($model, 'isActive'); ?>
			<div class="controls">
				<?php echo $form->checkBox($model, 'isActive'); ?>
			</div>
			<?= $form->endControlGroup(); ?>
			</div>

			<div class="block-buttons force-margin">
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => 'btn']); ?>
			</div>

		</fieldset>
	</div>

</div>
<?php $this->endWidget(); ?>
<script type="text/javascript">
	CKEDITOR.replace('Career_description', {
		toolbar : 'Basic',
		uiColor : 'transparent',
		width   : $('#Career_description').width(),
		height  : $('#Career_description').height(),
		skin    : 'v2'
	});
	CKEDITOR.replace('Career_skillsRequired', {
		toolbar : 'Basic',
		uiColor : 'transparent',
		width   : $('#Career_skillsRequired').width(),
		height  : $('#Career_skillsRequired').height(),
		skin    : 'v2'
	});
</script>