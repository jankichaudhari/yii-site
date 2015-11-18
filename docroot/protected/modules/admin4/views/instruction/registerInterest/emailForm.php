<?php
/**
 * @var $this         InstructionController
 * @var $client       Client
 * @var $model        Deal
 * @var $owner        Client
 *
 */
$text = "{$client->getFullName()} wants to view your property. Please call them to arrange a time and date.\nThey can be contacted on " . ($client->getPrimaryPhoneNumber() ? Locale::formatPhone($client->getPrimaryPhoneNumber()) : '') . " or at {$client->email}. Regards, W&S.";
$id = uniqid();
?>
<fieldset id="<?php echo $id ?>">
	<div class="block-header">Email</div>
	<div class="content">
		<form class="email-notification">
			<div class="classssss"></div>
			<div class="control-group">
				<label class="control-label">To</label>

				<div class="controls">
					<?php echo CHtml::textField('to_name', $owner->getFullName()) ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Email</label>

				<div class="controls">
					<?php echo CHtml::textField('to_email', $owner->email) ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Subject</label>

				<div class="controls">
					<?php echo CHtml::textField('subject', 'Client wants to view your property') ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">Body</label>

				<div class="controls">
					<?php echo CHtml::textArea('body', $text, ['class' => 'input-xlarge']) ?>
				</div>
			</div>
			<div class="block-buttons force-margin">
				<div class="row-fluid">
					<div class="span6">
						<button class="btn btn-primary btn-send-email">Send</button>
						<div class="sending"></div>
						<div class="sent flash success">Email sent</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</fieldset>
<script type="text/javascript">
	(function ()
	{
		var dealId = <?php echo $model->dea_id ?>;
		var context = $('#<?php echo $id ?>');
		var clientId = <?php echo $client->cli_id ?>;

		context.find('.email-notification').on('submit', function (event)
		{

			var $this = $(this);
			context.find('.btn-send-email').hide();
			context.find('.sending').show();

			$.post('/admin4/email/send', $this.serialize(), function (res)
			{
				context.find('.sending').hide();
				context.find('.sent').show();
				$.post('/admin4/instruction/interestVendorNotified/', {type : 'email', dealId : dealId, clientId : clientId}, function (res)
				{

				});
			})
			event.preventDefault()
		})

	})();
</script>