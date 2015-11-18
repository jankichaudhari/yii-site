<?php
/**
 * @var    $this            ClientController
 * @var    $model           Client
 * @var    $title           String
 * @var    $minPrices       array
 * @var    $maxPrices       array
 */

?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">Search</div>
			<div class="control-group">
				<label class="control-label"></label>

				<div class="controls"><?php echo $title ?></div>
			</div>
		</fieldset>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<?php $this->renderPartial('_filter_detailed', compact('model', 'minPrices', 'maxPrices')) ?>
		<fieldset>
			<div class="block-buttons force-margin">
				<button class="btn btn-small" id="sendButton">Send mailshot</button>
			</div>
		</fieldset>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<?php $this->renderPartial('_listing_with_edit', ['dataProvider' => $model->search(), 'title' => 'Clients', 'addButton' => false]) ?>
	</div>
</div>
<script type="text/javascript">
	(function ()
	{
		$('#sendButton').on('click', function (data)
		{
			var form = $('#client-filter-form');
			form.attr('action', '<?php echo $this->createUrl('sendMailshot', ['instructionId' => $_GET['instructionId']]) ?>');
			form.attr('method', 'GET');
			form.submit();
		})
	})();
</script>