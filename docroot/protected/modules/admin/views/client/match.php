<?php
/**
 * @var $this             ClientController
 * @var $clientModel      Client
 * @var $instructionModel Deal
 * @var $form             AdminFilterForm
 */
$form = $this->beginWidget('AdminFilterForm', array(
		'model'          => [$instructionModel, $clientModel],
		'storeInSession' => false,
		'ajaxFilterGrid' => 'client-list',
));
?>
<fieldset>
	<div class="block-header">MATCH CLIENTS</div>
	<div class="content">
		<?php if ($instructionModel->address): ?>
			<div class="control-group">
				<label class="control-label">Address</label>

				<div class="controls text">
					<?php echo $instructionModel->address->toString() ?>
				</div>
			</div>
		<?php endif ?>
		<div class="control-group">
			<label class="control-label">Price</label>

			<div class="controls">
				<?php echo $form->textField($instructionModel, 'dea_marketprice') ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Bedrooms</label>

			<div class="controls">
				<?php echo $form->textField($instructionModel, 'dea_bedroom') ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">PropertyType</label>

			<div class="controls">
				<?php echo $form->checkBoxList($instructionModel, 'dea_ptype', CHtml::listData(PropertyType::model()->getTypes(), 'pty_id', 'pty_title'), ['separator' => ' ']) ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Receive Emails</label>

			<div class="controls">
				<?php echo $form->checkBox($clientModel, 'cli_saleemail', ['value' => 'Yes', 'uncheckValue' => '']) ?>
			</div>
		</div>

	</div>
</fieldset>
<?php $this->endWidget(); ?>

<div class="row-fluid">
	<div class="span12">
		<?php $this->renderPartial('_listing_with_edit', [
				'dataProvider' => $clientModel->searchAgainstInstruction($instructionModel),
				'title'        => 'Matched Client List',
				'addButton'    => false,
		]) ?>

	</div>
</div>