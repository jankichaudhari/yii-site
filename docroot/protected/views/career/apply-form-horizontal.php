<?php
/**
 * @var                  $this         CareerController
 * @var PublicCareerForm $model
 * @var CActiveForm      $form
 * @var $messages
 */
?>
<div class="top-widget-container wide">
	<div class="inner-padding">
		<div class="row-fluid">
			<div class="form-header">Apply Now</div>
		</div>
		<?php $form = $this->beginWidget('CActiveForm', array(
															 'id'                  => 'career-apply-form',
															 'enableAjaxValidation'=> false,
															 'htmlOptions'         => array('enctype' => 'multipart/form-data'),
														)); ?>

		<?php
		if($messages) :
			if($messages['type']=='success'){
				$model = new PublicCareerForm();
			}
			?>
			<div class="message bold">
				<div class="<?= $messages['type']=='error' ? 'red' : 'green' ?>">
					<?= $messages['value'] ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="row">
			<div class="half-cell">
				<label class="block-label">Apply for</label>
				<div class="input-wrapper">
					<?php $career = isset($_POST['PublicCareerForm']['career']) ? $_POST['PublicCareerForm']['career'] : '' ?>
					<?php echo CHtml::dropDownList('PublicCareerForm[career]',$career,CHtml::listData(Career::model()->onlyActive()->findAll(), 'id', 'name'),['empty'=>'']) ?>
				</div>
			</div>
			<div class="half-cell">
				<?php echo $form->labelEx($model, 'name',['class' => 'block-label']); ?>
				<div class="input-wrapper">
					<?php echo $form->textField($model, 'name', array('size'     => 6, 'maxlength'=> 255)); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="half-cell">
				<?php echo $form->labelEx($model, 'email',['class' => 'block-label']); ?>
				<div class="input-wrapper">
					<?php echo $form->textField($model, 'email', array('size'     => 60, 'maxlength'=> 255)); ?>
				</div>
			</div>

			<div class="half-cell">
				<?php echo $form->labelEx($model, 'telephone',['class' => 'block-label']); ?>
				<div class="input-wrapper">
					<?php echo $form->textField($model, 'telephone', array('size'     => 60, 'maxlength'=> 255)); ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="cell">
				<?php echo $form->labelEx($model, 'message',['class' => 'block-label']); ?>
				<div class="input-wrapper">
					<?php echo $form->textArea($model, 'message', array('cols' => '44', 'rows' => '4')); ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="half-cell">
				<?php echo $form->labelEx($model, 'Upload CV',['class' => 'block-label']); ?>
				<div class="input-wrapper">
					<?php echo $form->fileField($model, 'cv'); ?>
				</div>
			</div>

			<div class="half-cell right" style="margin-left: 0">
				<label class="block-label" for="">&nbsp;</label>
				<div class="input-wrapper">
					<?php echo CHtml::submitButton('SEND', array('class' => 'btn half-width','id'    => 'apply-submit-button')) ?>
				</div>
			</div>
		</div>
		<?php  $this->endWidget() ?>
	</div>
</div>