<?php
/**
 * @var    $this      ClientController
 * @var    $model     Client
 * @var    $form      AdminForm
 * @var    $types     PropertyType[]
 */
$types = PropertyType::model()->getTypes();
?>
<div class="content">
	<div id="sales-requirements">
		<div class="control-group">
			<label class="control-label">Budget</label>

			<div class="controls">
				<?php echo $form->dropDownList($model, 'budget', $this->getBudgetValues($model), ['empty' => 'No Budget', 'class' => 'input-xsmall']) ?>
			</div>
		</div>
		<?php echo $form->beginControlGroup($model, 'cli_salebed') ?>
		<?php echo $form->controlLabel($model, 'cli_salebed') ?>
		<div class="controls">
			<?php echo $form->dropDownList($model, 'cli_salebed', ['Studio', 1, 2, 3, 4, 5, 6, 7], ['class' => 'input-xsmall']) ?>
		</div>
		<?php echo $form->endControlGroup() ?>
		<div class="control-group">
			<label class="control-label">Property Types</label>
		</div>
		<?php foreach ($types as $value): ?>
			<div class="control-group">
				<label class="control-label">
					<?php echo $value->pty_title ?>
					<?php echo $form->checkBox($model, 'propertyTypesIds[]', array(
							'uncheckValue' => null,
							'value'        => $value->pty_id,
							'checked'      => in_array($value->pty_id, $model->getPropertyTypesIds()),
							'class'        => 'property-type-main',
							'data-id'      => $value->pty_id
					)) ?>
				</label>

				<div class="controls">
					<?php echo $form->checkBoxList($model, 'propertyTypesIds', CHtml::listData(PropertyType::model()->getTypes($value->pty_id), 'pty_id', 'pty_title'), array(
							'separator'                 => ' ',
							'uncheckValue'              => null,
							'data-parent-property-type' => $value->pty_id,
							'class'                     => 'property-type-checkbox',
							'baseID'                    => 'propertyTypesIds_' . $value->pty_id
					)) ?>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="control-group">
			<label class="control-label">Property Categories</label>

			<div class="controls">
				<?php echo $form->checkBoxList($model, 'propertyCategoryIds', CHtml::listData(PropertyCategory::model()->matchClients()->findAll(), 'id', 'title'), ['uncheckValue' => null]) ?>
			</div>
		</div>

	</div>
</div>

<script type="text/javascript">
	(function ()
	{
		$('.property-type-main').on('change', function ()
		{
			var $this = $(this);
			$('[data-parent-property-type=' + $this.data('id') + ']').attr('checked', $this.is(':checked'));
		});

		$('.property-type-checkbox').on('change', function ()
		{
			var $this = $(this);
			var parentId = $this.data('parent-property-type');
			if ($this.is(':checked')) {
				$('.property-type-main[data-id=' + parentId + ']').attr('checked', true);
			}
		});


	})();
</script>