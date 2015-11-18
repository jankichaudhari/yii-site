<?php
/**
 * @var $this  MailshotTypeController
 * @var $model MailshotType
 * @var $form  AdminForm
 */
$form = $this->beginWidget('AdminForm');
?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header"><?php echo $model->isNewRecord ? 'Create' : 'Update' ?> Mailshot Type</div>
			<?php echo $form->errorSummary($model) ?>
			<div class="content">
				<div class="control-group">
					<label class="control-label">Name</label>

					<div class="controls">
						<?php echo $form->textField($model, 'name') ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Description</label>

					<div class="controls">
						<?php echo $form->textField($model, 'description') ?>
					</div>
				</div>


				<div class="control-group">
					<label class="control-label">Subject</label>

					<div class="controls">
						<?php echo $form->textField($model, 'subject') ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Html Template Path</label>

					<div class="controls">
						<?php echo $form->textField($model, 'templatePath') ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Html Template</label>

					<div class="controls">
						<?php echo $form->textArea($model, 'htmlTemplate') ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Text Template</label>

					<div class="controls">
						<?php echo $form->textArea($model, 'textTemplate') ?>
					</div>
				</div>

			</div>

			<div class="block-buttons force-margin">
				<input type="submit" value="Save" class="btn" />
			</div>
		</fieldset>

	</div>
</div>
<?php $this->endWidget() ?>
