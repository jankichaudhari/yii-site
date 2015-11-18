<?php
/**
 * @var $this  UserController
 * @var $model User
 * @var $form  AdminForm
 */
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jscolor/jscolor.js');
?>

<div class="row-fluid">
	<div class="span12">
		<?php $form = $this->beginWidget('AdminForm', array(
														   'id'                   => 'user-form',
														   'enableAjaxValidation' => false,
													  )); ?>
		<fieldset>
			<div class="block-header">
				<?php echo "Create new User" ?>
			</div>
			<div class="block-buttons">
				<?php echo CHtml::link('« Back', $this->createUrl('Index'), ['class' => 'btn btn-red']) ?>
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => 'btn']); ?>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="content">
						<?php
						if ($model->hasErrors()) {
							echo '<div class="flash danger">';
							echo $form->errorSummary($model);
							echo '</div>';
						}
						?>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="block-header">System Information</div>
					<div class="content">
						<?php include('tabs/systemInfo.php'); ?>
					</div>
				</div>
				<div class="span6">
					<div class="block-header">Personal Information</div>
					<div class="content">
						<?php include('tabs/personalInfo.php'); ?>
						<?= $form->beginControlGroup($model, 'use_password'); ?>
						<?= $form->controlLabel($model, 'use_password'); ?>
						<div class="controls">
							<?php echo $form->textField($model, 'use_password'); ?>
						</div>
						<?= $form->endControlGroup(); ?>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="block-header">Roles</div>
					<div class="content">
						<?php include('tabs/userRoles.php'); ?>
					</div>
				</div>
				<div class="span6">
					<div class="block-header">Email Alerts</div>
					<div class="content">
						<?php include('tabs/userEmailAlerts.php'); ?>
					</div>
				</div>
			</div>

			<div class="block-buttons">
				<?php echo CHtml::link('« Back', $this->createUrl('Index'), ['class' => 'btn btn-red']) ?>
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => 'btn']); ?>
			</div>

		</fieldset>
		<?php $this->endWidget(); ?>
	</div>
</div>