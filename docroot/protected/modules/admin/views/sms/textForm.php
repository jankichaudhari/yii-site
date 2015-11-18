<?php
/**
 * @var $this        CController
 * @var $client      Client
 * @var $defaultText String
 *
 * So far this one can be only one per page.
 */
/** @var CClientScript $cc */
$cc = Yii::app()->getClientScript();
$cc->registerCssFile('/css/grey-smooth/text-messaging.css');
$id = isset($id) ? $id : uniqid('form_');
$multiple = isset($multiple) ? true : false;
?>
<form class="reply-form" id="<?php echo $id ?>">
	<?php foreach ($client->telephones as $key => $phone): ?>
		<?php if (!Locale::isMobile($phone->tel_number)) continue ?>
		<label class="telephone">
			<input type="radio" name="to" value="<?php echo $phone->tel_number ?>" />
			<?php echo $phone->tel_number ?>
		</label>
	<?php endforeach; ?>
	<textarea name="text" class="reply-field"><?php echo isset($defaultText) ? $defaultText : "" ?></textarea>
	<input type="hidden" name="clientId" value="<?php echo $client->cli_id ?>" />

	<div class="symbol-count">Symbol count: 0/160</div>
	<div>
		<button class="btn btn-primary btn-large send-message-button">Send</button>

		<div class="sending"></div>
		<div class="sent flash success">Message sent</div>
	</div>
</form>
<script type="text/javascript">
	(function ()
	{
		var context = $('#<?php echo $id ?>')
		var multiple = <?php echo $multiple ? 'true' : 'false' ?>

				$('.reply-field', context).on('keyup',function ()
				{
					$('.symbol-count', context).removeClass('exceed');
					var $this = $(this);
					var symbolCount = $this.val().length;
					if (symbolCount > 160) {
						$('.symbol-count', context).addClass('exceed');
					}
					$('.symbol-count', context).html('Symbol count: ' + symbolCount + '/160');
				}).trigger('keyup');


		var callback = function (res)
		{
			<?php if(isset($callback) && $callback) echo "$callback(res)"; ?>

			$('.sending', context).hide();
			if (multiple) {
				$('.send-message-button', context).show()
			} else {
				$('.sent', context).show()
			}
		}


		$('[name=to]', context).first().attr('checked', true);

		$(context).on('submit', function (event)
		{
			var $this = $(this);
			var data = $this.serialize()
			$('.send-message-button', context).hide();
			$('.sending', context).show()
			$.post('<?php echo $this->createUrl('sms/send') ?>', data, callback)
			event.preventDefault()
		});
	})();
</script>