<?php
/**
 * @var $value        Deal
 * @var $model        Deal
 * @var $form         AdminFilterForm
 * @var $clientScript CClientScript
 */
$availableStatuses = array_merge([$model->dea_status => $model->dea_status], $model->getNextAvailableStatuses());
$model->dea_exchdate = !$model->dea_exchdate ? '' : Date::formatDate('d/m/Y', $model->dea_exchdate);
$model->dea_compdate = !$model->dea_compdate ? '' : Date::formatDate('d/m/Y', $model->dea_compdate);
?>
<div class="content">
	<div class="control-group">
		<label class="control-label">
			<?php echo $form->controlLabel($model, 'dea_status'); ?>
		</label>

		<div class="controls">
			<?php echo $form->dropDownList($model, 'dea_status', $availableStatuses, ['class' => 'input-xsmall']); ?>
		</div>
	</div>
	<div class="control-group statusDate">
		<label class="control-label">
			<?php echo $form->controlLabel($model, 'dea_exchdate'); ?>
		</label>

		<div class="controls">
			<?php echo $form->textField($model, 'dea_exchdate', ['class' => 'input-xsmall datepicker', 'placeholder' => 'dd/mm/yyyy']); ?>
		</div>
	</div>
	<div class="control-group statusDate">
		<label class="control-label">
			<?php echo $form->controlLabel($model, 'dea_compdate'); ?>
		</label>

		<div class="controls">
			<?php echo $form->textField($model, 'dea_compdate', ['class' => 'input-xsmall datepicker', 'placeholder' => 'dd/mm/yyyy']); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-group">
			<label class="control-label">
				<?php echo $form->controlLabel($model, 'noNewProperty'); ?>
			</label>

			<div class="controls">
				<?php echo $form->checkBox($model, 'noNewProperty'); ?>
			</div>
		</div>
	</div>
	<div class="control-group">
		<div class="control-group">
			<label class="control-label">
				<?php echo $form->controlLabel($model, 'displayOnWebsite'); ?>
			</label>

			<div class="controls">
				<?php echo $form->checkBox($model, 'displayOnWebsite'); ?>
			</div>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<?php $this->renderPartial("application.modules.admin4.views.note.addNote", array(
					'noteTypeId'   => $model->dea_id,
					'noteType'     => Note::TYPE_SOT,
					'title'        => 'State of trade  note(s)',
					'textBoxTitle' => 'State of trade  Note'
			)) ?>
		</div>
	</div>
	<div class="control-group" id="sotTable">
		<?php if ($model->dealSOT): ?>

			<label class="control-label">Status History</label>
			<div class="controls">
				<table class="small-table">
					<?php foreach ($model->dealSOT as $sot): ?>
						<tr>
							<td><?php echo $sot->sot_status ?></td>
							<td><?php echo date('d/m/Y H:i', strtotime($sot->sot_date)) ?></td>
							<td><?php echo $sot->creator ? $sot->creator->fullName : '' ?> </td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		<?php endif ?>
	</div>
</div>