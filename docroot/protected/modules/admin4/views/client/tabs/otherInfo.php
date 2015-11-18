<?php
/**
 * @var    $this      ClientController
 * @var    $model     Client
 * @var    $form      AdminForm
 */
?>
<div class="content">

	<?php echo $form->beginControlGroup($model, 'cli_id') ?>
	<?php echo $form->controlLabel($model, 'cli_id'); ?>
	<div class="controls">
		<?php echo $model->cli_id ? $model->cli_id : "N/A" ?>
	</div>
	<?php echo $form->endControlGroup() ?>

	<?php echo $form->beginControlGroup($model, 'cli_created') ?>
	<label class="control-label" for="cli_created">Registered Since</label>

	<div class="controls">
		<?php echo Date::formatDate('dS F Y', $model->cli_created) ?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'cli_regd') ?>
	<label class="control-label" for="cli_regd">Registered by</label>

	<div class="controls">
		<?php echo $model->registrator ? $model->registrator->fullName : "N/A" ?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'cli_method') ?>
	<label class="control-label" for="cli_method">Initial Contact Method</label>

	<div class="controls">
		<?php echo $model->cli_method ? $model->cli_method : "N/A" ?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'cli_source') ?>
	<label class="control-label" for="cli_source">Referrer</label>

	<div class="controls">
		<?php
		if ($model->cli_source) {
			echo $model->source->getTitle();
		} else {
			echo CHtml::dropDownList("Client[sourceParent]", "", CHtml::listData(Source::model()->getTypes(0), 'sou_id', 'sou_title'), ['id' => 'sourceParentType', 'empty' => '']);
			echo $form->dropDownList($model, "cli_source", [], ['id' => 'sourceType']);
		}
		?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'cli_neg') ?>
	<label class="control-label" for="cli_neg">Assigned Negotiator</label>

	<div class="controls">
		<?php echo $form->dropDownList($model, "cli_neg", CHtml::listData(User::model()->onlyActive()->alphabetically()
																			  ->findAll(), 'use_id', 'fullName'), ['empty' => 'Unassigned']) ?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'cli_branch') ?>
	<label class="control-label" for="cli_branch">Assigned Branch</label>

	<div class="controls">
		<?php echo $form->dropDownList($model, 'cli_branch', CHtml::listData(Branch::model()->active()->findAll(), 'bra_id', 'bra_title'), ['empty' => 'Unassigned']); ?>
	</div>
	<?php echo $form->endControlGroup(); ?>
</div>

<script type="text/javascript">
	(function ()
	{
		$('#sourceParentType').change(function ()
									  {
										  var parentType = $(this).val();
										  $.getJSON("/admin4/client/getSourceType", {'parentType' : parentType}, function (result)
										  {
											  $('#sourceType').html('');
											  for (i in result) {
												  $('#sourceType').append(
														  $('<option></option>').val(i).html(result[i])
												  );
											  }
										  });
									  });
	})();

</script>