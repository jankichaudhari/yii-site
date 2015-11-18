<?php
/**
 * @var $this       InstructionController
 * @var $client     Client
 * @var $model      Deal
 *
 */
$callback = 'window.textMessageSentCallback';
?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header"></div>
			<div class="content">
				<div class="flash warning"><?php echo Yii::app()->user->getFlash('warning') ?></div>
				<?php $title = $model->address ? $model->address->getFullAddressString(', ') : 'No address provided. ' ?>
				<h6>View registered intereset for <?php echo CHtml::link($title, ['instruction/summary', 'id' => $model->dea_id]) ?></h6>
				<h6>Client: <?php echo CHtml::link($client->getFullName(), ['client/update', 'id' => $client->cli_id]) ?></h6>
			</div>
			<div class="block-buttons">
				<button class="btn btn-primary" id="send-all">Send all</button>
			</div>
		</fieldset>
	</div>
</div>
<?php foreach ($model->owner as $owner): ?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="row-fluid">
					<div class="span4">
						<?php $this->renderPartial('registerInterest/textForm', compact('owner', 'model', 'client', 'callback')) ?>
					</div>
					<div class="span8">
						<?php $this->renderPartial('registerInterest/emailForm', compact('owner', 'client', 'model')) ?>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
<?php endforeach; ?>
<script type="text/javascript">


	(function ()
	{

		var dealId = <?php echo $model->dea_id ?>;
		var clientId = <?php echo $client->cli_id ?>;

		window.textMessageSentCallback = function (res)
		{
			if (res.id) {
				$.post('/admin4/instruction/interestVendorNotified/', {type : 'text', dealId : dealId, clientId : clientId}, function (res)
				{

				});
			}
		}

		$('#send-all').on('click', function ()
		{
			$(document.forms).submit();
		})


	})();
</script>