<?php
/**
 * @var $this  InstructionController
 * @var $model Deal
 * @var $form  AdminForm
 */
$form = $this->beginWidget('AdminForm', [
										'id'          => 'custom-mailshot-form',
										'htmlOptions' => array('enctype' => 'multipart/form-data')
										]);
$propTitle = $model->property->getShortAddressString(', ', true);
$price = Locale::formatPrice($model->dea_marketprice, $model->dea_type == 'Sales' ? false : true);
?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">Send custom mailshot</div>
			<div class="clearfix"></div>
			<div class="control-group">
				<label class="control-label" for="">mailshot type</label>

				<div class="controls"><?php echo CHtml::dropDownList('mailshot[type]', 'new', Deal::getMailshotTypes()) ?></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="">mailshot Text</label>

				<div class="controls"><?php echo CHtml::textArea('mailshot[body]', '', array('class' => 'input-xxlarge')) ?></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="">attachement</label>

				<div class="controls"><?php echo CHtml::fileField('mailshot[file]') ?></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="">Debug email</label>

				<div class="controls">
					<?php echo CHtml::textField('mailshot[debugEmail]') ?>
					<span class="hint">If you want that mailshot to be sent just to one email(for test purpose) please type it here</span>
				</div>
			</div>
			<?php if (!$model->underTheRadar): ?>
				<div class="control-group">
					<label class="control-label" for="">Include link</label>

					<div class="controls"><?php echo CHtml::checkBox('mailshot[include_link]', true) ?></div>
				</div>
			<?php endif ?>
			<div class="control-group form-buttons shaded">
				<div class="controls force-margin"><input type="submit" class="btn"></div>
			</div>
		</fieldset>
	</div>
</div>
<div class="row-fluid" id="text-preview">
	<div class="span12">
		<fieldset>
			<div class="block-header">Email text preview</div>
			<div class="clearfix"></div>
			<div class="control-group">
				<div class="controls force-margin">
					<div id="mailPreview" class="text"></div>
				</div>
			</div>
		</fieldset>
	</div>
</div>
<?php $this->endWidget() ?>
<script type="text/javascript">
	var detailsText = '<?php echo $model->dea_strapline ?><br><?php echo $propTitle ?><br><?php echo $price ?>';

	var mailshotTexts = {
		new: '',
		'reduced': '',
		'back': ''
	}
	var previewMail = function () {
		var t = $("#mailshot_type").val();

		var body = '';
		if ($("#mailshot_body").val()) {
			body = $("#mailshot_body").val().replace(/([^>])\n/g, '$1<br/>') + "<br><br>";
		}

		var text = mailshotTexts[t] + body;

		if ($("#mailshot_include_link").is(":checked")) {
			text += detailsText;
			text += "<br><a href='#'> http://www.woosterstock.co.uk/mailshot.php?id={dealid}&c={clientId}</a><br><br>";
		}

		text += 'To unsubscribe from all future mailings, please follow this link:<br>';
		text += "<a href='#'>http://www.woosterstock.co.uk/mailshot.php?a=unsub&id={deal_id}&e=clientEmail@example.com&c={clientId}</a>";

		$('#mailPreview').html(text);
	};
	$('#mailshot_type, #mailshot_include_link, #mailshot_body').on('change', previewMail);

	$('#mailshot_body').on('keyup', previewMail);
	previewMail();
</script>
