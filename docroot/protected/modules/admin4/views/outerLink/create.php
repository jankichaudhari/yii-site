<?php
/**
 * @var $model OuterLink
 * @var $this  OuterLinkController
 * @var $form  AdminForm
 */
?>
<div class="row">
	<div class="span12">

		<?php $form = $this->beginWidget('AdminForm', array(
														   'id'                   => 'outer-link-form',
														   'enableAjaxValidation' => false,
													  )); ?>

		<fieldset>
			<div class="block-header">
				<?php echo $model->isNewRecord ? 'Create Link' : 'Update Link ' . $model->title ?>
			</div>
			<div class="block-buttons">
				<?php echo CHtml::link('« Back', $this->createUrl('Index'), ['class' => 'btn btn-red']) ?>
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

			<div class="row">
				<div class="span12">
					<?php include('tabs/form.php'); ?>
				</div>
			</div>

		</fieldset>

		<?php $this->endWidget(); ?>

	</div>
</div>