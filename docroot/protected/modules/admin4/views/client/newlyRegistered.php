<?php
/**
 * @var $this         ClientController
 * @var $model        Client
 * @var $form         AdminFilterForm
 */
?>

<div class="row-fluid">
	<div class="span12">
		<?php
		$form = $this->beginWidget('AdminFilterForm', array(
														   'model'          => $model,
														   'storeInSession' => false,
														   'ajaxFilterGrid' => 'client-list',
													  ));
		?>
		<fieldset>
			<div class="block-header">
				NEWLY REGISTERED CLIENTS
			</div>
			<div class="block-buttons">

			</div>
			<?= $form->beginControlGroup($model, 'cli_branch'); ?>
			<label class="control-label">
				Branch
				<input type="checkbox" id="branch-trigger" checked>
			</label>

			<div class="controls">
				<?php
				$branches = CHtml::listData(Branch::model()->active()->findAll(), 'bra_id', 'bra_title');
				echo CHtml::checkBoxList('Client[cli_branch]', array_keys($branches), $branches, [
																								 'separator' => ' ',
																								 'class'     => 'branch-checkbox'
																								 ])
				?>
			</div>
			<?= $form->endControlGroup(); ?>
		</fieldset>
		<?php $this->endWidget(); ?>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<?php $this->renderPartial('_listing_with_edit', [
														 'dataProvider' => $model->newlyRegistered(),
														 'title' => 'Newly Registered client list',
														 'addButton' => false,
														 ]) ?>

	</div>
</div>

<script type="text/javascript">
	$('#branch-trigger').on('change', function () {
		$('.branch-checkbox').attr('checked', $(this).is(':checked'));
	});
</script>