<?php
/**
 * @var $this ClientController
 */
$this->beginWidget('AdminForm', ['method' => 'post']);
?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">Select mailshot type</div>
			<div class="content">
				<div class="control-group">
					<label class="control-label">Mailshot Type</label>

					<div class="controls">
						<?php echo CHtml::dropDownList('MailshotType', null, CHtml::listData(MailshotType::model()->findAll(), 'name', 'subject')) ?>
					</div>
				</div>

				<?php if (Yii::app()->user->is(UserRole::SUPER_ADMIN)): ?>
					<div class="control-group">
						<label class="control-label">Run with test API key</label>

						<div class="controls">
							<?php echo CHtml::checkBox('test', false, ['value' => 'test']) ?>
						</div>
					</div>
				<?php endif ?>
			</div>
			<div class="block-buttons force-margin">
				<input type="submit" value="Send Mailshot" class="btn" name="send" />
			</div>
		</fieldset>
	</div>
</div>
<?php $this->endWidget() ?>
