<?php
/**
 * @var $model QuickReport
 * @var $form  CActiveForm
 * @var $this  QuickReportController
 */
?>

<?php $form = $this->beginWidget("AdminForm", ['id' => 'quick-report-edit-form']);
?>
<fieldset>
	<div class="block-header"><?php echo $model->isNewRecord ? 'Create new Report' : 'Update report' ?></div>
	<div class="content">
		<?php echo $form->errorSummary(array($model)); ?>
		<div class="control-group">
			<label class="control-label">
				<?php echo $form->label($model, 'title') ?>
			</label>

			<div class="controls">
				<?php echo $form->textField($model, 'title') ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">
				<?php echo $form->label($model, 'actionLink'); ?>
			</label>

			<div class="controls">
				<?php echo $form->textField($model, 'actionLink'); ?>
			</div>
		</div>


		<div class="control-group">
			<label class="control-label">
				<?php echo $form->label($model, 'description'); ?>
			</label>

			<div class="controls">
				<?php echo $form->textField($model, 'description'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">
				<?php echo $form->label($model, 'keyField'); ?>
			</label>

			<div class="controls">
				<?php echo $form->textField($model, 'keyField'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">
				<?php echo $form->label($model, 'query'); ?>
			</label>

			<div class="controls">
				<?php echo $form->textArea($model, 'query', ['cols' => 45, 'rows' => 7]); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">
				<?php echo $form->label($model, 'isActive'); ?>
			</label>

			<div class="controls">
				<?php echo $form->checkBox($model, 'isActive'); ?>
			</div>
		</div>
	</div>

	<div class="block-buttons force-margin">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => 'btn btn-primary']); ?>
	</div>
</fieldset>
<?php $this->endWidget() ?>
</div>