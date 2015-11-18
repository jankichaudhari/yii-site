<?php
/**
 * @var $this  SmsController
 * @var $model Appointment
 * @var $form  AdminForm
 * @var $cs    CClientScript
 */
$cs = Yii::app()->getClientScript();
$cs->registerCssFile("/css/grey-smooth/sms-messages.css");
?>
<?php $form = $this->beginWidget('AdminForm', ['method' => 'post', 'id' => 'messages-form']) ?>
<div class="row-fluid">
	<div class="span12">
		<?php if (Yii::app()->user->hasFlash('messages-sent')): ?>
			<div class="flash success"><?php echo Yii::app()->user->getFlash('messages-sent') ?></div>
		<?php endif ?>
		<fieldset>
			<div class="block-header">Send SMS confrmation</div>
			<table class="people-table">
				<tr>
					<?php foreach ($model->instructions as $key => $instruction): ?>
						<?php if ($instruction->confirmed !== Deal::CONFIRMED) continue; ?>
						<td>
							<?php echo CHtml::link($instruction->address->getFullAddressString(', '), ['Deal/update', 'id' => $instruction->dea_id]) ?>
							<?php foreach ($instruction->owner as $owner): ?>
								<?php $this->renderPartial('client-form', array(
																			   'client'      => $owner,
																			   'model'       => $model,
																			   'instruction' => $instruction,
																			   'fieldKey'    => "vendors"
																		  )) ?>
							<?php endforeach; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			</table>
			<div class="clearfix"></div>
			<?php if ($model->app_type === Appointment::TYPE_VIEWING): ?>
				<div class="block-header dark-grey">Viewers</div>
				<table class="people-table">
					<tr>
						<?php foreach ($model->clients as $key => $client): ?>
							<?php $this->renderPartial('client-form', array(
																		   'client'   => $client,
																		   'model'    => $model,
																		   'fieldKey' => "clients"
																	  )) ?>
						<?php endforeach; ?>
					</tr>
				</table>
				<div class="clearfix"></div>
			<?php endif ?>
			<div class="block-buttons">
				<button type="submit" class="send-btn btn btn-primary">Send</button>
				<span style="font-weight: bold; display: none; padding-left: 10px;" class="sending">Sending...</span>
			</div>
		</fieldset>
	</div>
</div>
<?php $this->endWidget() ?>
<script type="text/javascript">
	(function ()
	{
		var cancelEdit = function ()
		{
			$(this).closest('.input-toggle-edit').find('.input').hide();
			$(this).closest('.input-toggle-edit').find('.value').show();

		}

		$('.input-toggle-edit').find('.value').on('click', function ()
		{
			var $this = $(this);
			var $parent = $this.parent();
			$this.hide();
			$parent.find('.input').show();
			$parent.find('input').focus();
		});
		$('.input-toggle-edit').find('.input').find('.confirm').on('click', function ()
		{
			var $this = $(this);
			var $parent = $this.parent().parent();
			if (url = $parent.data('url')) {
				$.post(url, $parent.find('input').serialize(), function (data)
				{
					data = $.parseJSON(data)

					if ($parent.hasClass('creating')) {
						$parent.siblings('[name="phones[]"]').val(data['tel_id']);
						$parent.siblings('.send-to-radio').val(data['tel_id']).css('display', 'inline-block');
					}
					for (var key in data) {
						$parent.find('[name=' + key + ']').val(data[key]);
						$parent.find('.value[data-name=' + key + ']').html(data[key]);
					}
				});
			}
			$this.parent().hide();
			$parent.find('.value').show();
		});
		$('.input-toggle-edit').find('input').on('keydown', function (event)
		{
			if (event.keyCode === 13) {
				$(this).parent().find('.confirm').trigger('click');
				event.preventDefault();
			}
			if (event.keyCode === 27) {
				cancelEdit.call(this);
				event.preventDefault();
			}
		});
//		$('.input-toggle-edit').find('input').on('blur', cancelEdit);

		$('.send-to-checkbox').each(function ()
									{
										var $this = $(this);
										var $parent = $this.closest('.client-form');
										$this.on('change', function ()
										{
											var selected = $this.is(':checked');
											$parent.find('input').attr('disabled', !selected);
											$parent.find('textarea').attr('disabled', !selected);
											$this.attr('disabled', false);
										});
										$this.trigger('change');
									});
		var recalculateSymbolCount = function ()
		{
			var $this = $(this);
			var $parent = $this.closest('.client-form');
			var val = $this.val();

			var el = $parent.find('.symbol-count');
			var length = val.length;

			el.removeClass('danger').html('Symbols count: ' + length).addClass(length > 160 ? 'danger' : '');
		}
		$('.text-message').on('keyup', recalculateSymbolCount);
		$('.text-message').each(recalculateSymbolCount);

		$('#messages-form').on('submit', function ()
		{
			var proceed = true;
			$('.text-message').each(function ()
									{
										if ($(this).val().length > 160 && !$(this).is(':disabled')) {
											proceed = false;
										}
									});
			if (!proceed) {
				alert('Sorry but one or more messages cannot be sent as they exceed maximum of 160 symbols');
				return false;
			} else {
				$('.send-btn').hide();
				$('.sending').show();
			}
		})
	})();
</script>