<?php
/**
 * @var Branch[] $branches
 * @var          $this     ContactUs
 * @var          $form     CActiveForm
 * @var          $model    ContactUsForm
 * @var          $offices  Office[]
 * @var          $isMobile bool
 */
?>
<div class="top-widget-container narrow">
	<div class="inner-padding">
		<div class="row-fluid">
			<div class="form-header">Contact Us</div>
		</div>
		<?php $form = $this->beginWidget('CActiveForm') ?>
		<?php echo $form->errorSummary($model, 'In order for us to deal with your enquiry please make sure the following information is correct.', null, array(
				'class' => 'message bold red'
		)) ?>
		<?php echo $this->successMessage ? '<div class="successBox">' . $this->successMessage . '<div style="text-align: center; margin-top:5px;"><img src="/images/sys/Message-Sent-Icon.png" alt=""></div></div>' : "" ?>
		<div class="row">
			<div class="cell">
				<?php echo $form->labelEx($model, 'to', ['class' => 'block-label']) ?>
				<div class="input-wrapper">
					<?php echo $form->dropDownList($model, 'to', $offices, array(
							'empty' => [
									Yii::app()->params['contactUs']['general_email'] => 'General Enquiry'
							]
					)) ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="cell">
				<?php echo $form->labelEx($model, 'name', ['class' => 'block-label']) ?>
				<div class="input-wrapper">
					<?php echo $form->textField($model, 'name') ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="cell">
				<?php echo $form->labelEx($model, 'email', ['class' => 'block-label']) ?>
				<div class="input-wrapper">
					<?php echo $form->textField($model, 'email') ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="cell">
				<?php echo $form->labelEx($model, 'telephone', ['class' => 'block-label']) ?>
				<div class="input-wrapper">
					<?php echo $form->textField($model, 'telephone') ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="cell">
				<?php echo $form->labelEx($model, 'message', ['class' => 'block-label']) ?>
				<div class="input-wrapper">
					<?php echo $form->textArea($model, 'message') ?>
				</div>
			</div>
		</div>

		<?php if (CCaptcha::checkRequirements()): ?>
			<div class="row">
				<div class="cell">
					<?php echo $form->labelEx($model, 'verifyCode', ['class' => 'block-label']) ?>
					<div class="input-wrapper">
						<?php $this->widget('CCaptcha', array(
								'buttonOptions' => array(
										'style' => 'float:right; line-height: 50px;'
								)
						)) ?>
						<?php echo $form->textField($model, 'verifyCode') ?>
					</div>
				</div>
			</div>
		<?php endif ?>

		<div class="row">
			<div class="cell right">
				<input type="submit" value="SEND" name="<?php echo $this->name ?>[send]" class="btn half-width"/>
			</div>
		</div>
		<?php $this->endWidget() ?>
	</div>
</div>
