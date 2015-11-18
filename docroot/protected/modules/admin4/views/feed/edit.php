<?php
/**
 * @var $this  FeedController
 * @var $form  AdminForm
 * @var $model FeedPortal
 */
$form = $this->beginWidget('AdminForm', array(
											 'id' => 'edit-feed-form'
										));
?>
	<div class="row-fluid">
		<div class="span8">
			<fieldset>
				<div class="block-header"><?php echo $model->isNewRecord ? 'Create New Feed Configuration' : 'Edit Feed Configuration' ?></div>
				<div class="content">
					<div class="flash success remove"><?php echo Yii::app()->user->getFlash('success'); ?></div>
					<?php echo $form->beginControlGroup($model, 'portal_name'); ?>
					<?php echo $form->controlLabel($model, 'portal_name'); ?>
					<div class="controls">
						<?php echo $form->textField($model, 'portal_name'); ?>
					</div>
					<?= $form->endControlGroup(); ?>
					<?php echo $form->beginControlGroup($model, 'ftp_server'); ?>
					<?php echo $form->controlLabel($model, 'ftp_server'); ?>
					<div class="controls">
						<?php echo $form->textField($model, 'ftp_server') ?>
					</div>
					<?= $form->endControlGroup(); ?>

					<?php echo $form->beginControlGroup($model, 'ftp_username'); ?>
					<?php echo $form->controlLabel($model, 'ftp_username'); ?>
					<div class="controls">
						<?php echo $form->textField($model, 'ftp_username') ?>
					</div>
					<?= $form->endControlGroup(); ?>
					<?php echo $form->beginControlGroup($model, 'ftp_password'); ?>
					<?php echo $form->controlLabel($model, 'ftp_password'); ?>
					<div class="controls">
						<?php echo $form->textField($model, 'ftp_password') ?>
					</div>
					<?= $form->endControlGroup(); ?>

					<?php echo $form->beginControlGroup($model, 'ftp_dest_folder'); ?>
					<?php echo $form->controlLabel($model, 'ftp_dest_folder'); ?>
					<div class="controls">
						<?php echo $form->textField($model, 'ftp_dest_folder') ?>
					</div>
					<?= $form->endControlGroup(); ?>

					<?php echo $form->beginControlGroup($model, 'filename'); ?>
					<?php echo $form->controlLabel($model, 'filename'); ?>
					<div class="controls">
						<?php echo $form->textField($model, 'filename') ?>
					</div>
					<?= $form->endControlGroup(); ?>
				</div>
				<div class="block-buttons force-margin">
					<input type="submit" value="Save" class="btn btn-primary" />
				</div>
			</fieldset>
		</div>
	</div>
<?php
$this->endWidget();