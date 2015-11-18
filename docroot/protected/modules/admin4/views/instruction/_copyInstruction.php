<?php
/**
 * @var $this  InstructionController
 * @var $model Deal
 * @var $savedInstructions
 * @var $form  AdminForm
 */
$maxCopies = 10;
$form = $this->beginWidget('AdminForm', array(
											 'id' => 'copy-instruction-form',
										));
?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">Specify instruction information</div>
			<div class="control-group">The status of these copies will be set to Production</div>
			<div class="control-group">
				<label class="control-label">Select instruction's type</label>

				<div class="controls">
					<?php $model->dea_type = $model->dea_type ? $model->dea_type : Deal::TYPE_SALES ?>
					<?php echo $form->radioButtonList($model, 'dea_type', array(
																			   Deal::TYPE_LETTINGS => Deal::TYPE_LETTINGS,
																			   Deal::TYPE_SALES    => Deal::TYPE_SALES,
																		  )); ?>
				</div>
			</div>

			<div class="block-buttons">
				<input type="Submit" class="btn" value="Create">
			</div>
		</fieldset>
	</div>

</div>
<?php $this->endWidget() ?>
