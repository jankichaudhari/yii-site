<?php
/**
 * @var $this  NoteController
 * @var $form  Adminform
 * @var $model Note
 */
?>
<div class="container-fluid">
	<?php $form = $this->beginWidget('AdminForm') ?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header">Edit Note</div>
				<?php if (Yii::app()->user->hasFlash('note-updated')) : ?>
					<div class="flash success remove"><?php echo Yii::app()->user->getFlash('note-updated') ?></div>
				<?php endif ?>
				<?php if (Yii::app()->user->hasFlash('note-restored')) : ?>
					<div class="flash success remove"><?php echo Yii::app()->user->getFlash('note-restored', 'Note is restored!') ?></div>
				<?php endif ?>
				<?php if (Yii::app()->user->hasFlash('note-deleted') || $model->not_status == 'Deleted') : ?>
					<div class="flash danger"><?php echo Yii::app()->user->getFlash('note-deleted', 'Note is deleted!') ?></div>
				<?php endif ?>
				<div class="content">
					<div class="control-group">
						<label class="control-label">Created</label>

						<div class="controls">
							<span class="text"><?php echo Date::formatDate('d/m/Y H:i', $model->not_date) . ' By ' . $model->creator->getFullName() ?></span>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Last edited</label>

						<div class="controls">
							<span class="text"><?php echo Date::formatDate('d/m/Y H:i', $model->not_edited) ?></span>
						</div>
					</div>
					<?php echo $form->beginControlGroup($model, 'not_blurb') ?>
					<label class="control-label" for="Note_not_blurb">Note</label>

					<div class="controls">
						<?php echo $form->textArea($model, 'not_blurb') ?>
					</div>
					<?php echo $form->endControlGroup() ?>
				</div>
				<div class="block-buttons force-margin">
					<input type="submit" class="btn" value="Save">
					<input type="submit" class="btn" value="Save & Close" name="close">
					<?php if ($model->not_status == 'Deleted'): ?>
						<input type="submit" class="btn btn-success" value="restore" name="restore">
					<?php else: ?>
						<input type="submit" class="btn btn-danger" value="Delete" name="delete">
					<?php endif ?>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header">History</div>
				<?php foreach ($model->changes as $key => $change): ?>
					<div class="control-group">
						<span class="control-label"><?php echo $change->creator->getFullName() ?><br><?php echo Date::formatDate('d/m/Y H:i', $change->cha_datetime) ?></span>

						<div class="controls text">
							<?php echo nl2br($change->cha_new) ?>
						</div>
					</div>
					<?php echo $form->separator() ?>
				<?php endforeach; ?>

			</fieldset>
		</div>
	</div>
	<?php $this->endWidget() ?>
</div>