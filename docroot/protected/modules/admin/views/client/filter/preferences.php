<?php
/**
 * @var    $this      ClientController
 * @var    $model     Client
 * @var    $form      AdminForm
 * @var    $types     PropertyType[]
 * @var    $minPrices array
 * @var    $maxPrices array
 */
$types = PropertyType::model()->getTypes();

?>
<div id="sales-requirements">
	<div class="control-group">
		<label class="control-label">Price</label>

		<div class="controls">
			<?php echo $form->dropDownList($model, 'minPrice', $minPrices, ['class' => 'input-xsmall', 'empty' => 'No Minimum']) ?>
			<?php echo $form->dropDownList($model, 'maxPrice', $maxPrices, ['class' => 'input-xsmall', 'empty' => 'No Maximum']) ?>
			<label><?php echo $form->checkBox($model, 'searchNoBudget', ['value' => true, 'uncheckValue' => false]) ?>
				<span class="hint">include clients without budget</span></label>
		</div>
	</div>
	<?php echo $form->beginControlGroup($model, 'cli_salebed') ?>
	<?php echo $form->controlLabel($model, 'cli_salebed') ?>
	<div class="controls"><?php echo $form->dropDownList($model, 'cli_salebed', ['Studio', 1, 2, 3, 4, 5, 6, 7], ['class' => 'input-xsmall', 'empty' => 'all']) ?></div>
	<?php echo $form->endControlGroup() ?>
	<div class="control-group">
		<label class="control-label">
			Property type
		</label>

		<div class="controls">
			<?php echo $form->checkBoxList($model, 'propertyTypesIds', CHtml::listData($types, 'pty_id', 'pty_title'), ['separator' => ' ', 'uncheckValue' => null]) ?>
		</div>
	</div>
</div>
