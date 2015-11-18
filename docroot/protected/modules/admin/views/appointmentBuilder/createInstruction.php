<?php
/**
 * @var $this  AppointmentBuilderController
 * @var $model Deal
 * @var $form  AdminForm
 */
$form = $this->beginWidget('AdminForm', array(
											 'id' => 'client-edit-form',
										));
?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header">Specify instruction information</div>
				<div class="content">
					<?= $form->beginControlGroup($model, 'dea_type'); ?>
					<label class="control-label">Select instruction's type</label>

					<div class="controls">
						<?php echo $form->radioButtonList($model, 'dea_type', array(
																				   Deal::TYPE_LETTINGS => Deal::TYPE_LETTINGS,
																				   Deal::TYPE_SALES    => Deal::TYPE_SALES,
																			  )); ?>
					</div>
					<?= $form->endControlGroup(); ?></div>

				<div class="block-buttons">
					<input type="Submit" class="btn" value="Finish">
				</div>
			</fieldset>
		</div>

	</div>
<?php $this->endWidget() ?>