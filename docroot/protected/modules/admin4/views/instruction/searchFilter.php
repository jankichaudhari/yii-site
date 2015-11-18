<?php
/**
 * @var $this  InstructionController
 * @var $form  AdminFilterForm
 * @var $model Deal
 */
?>
<div class="block-header">Search</div>
<div class="content">
	<div class="row-fluid">
		<div class="span8">
			<div class="control-group">
				<label class="control-label">
					Search
				</label>

				<div class="controls">
					<?php echo $form->textField($model, 'searchString', array('size' => 30, 'class' => 'input-xlarge')) ?>
					<span class="hint">Any part of address or owners name</span>
				</div>
			</div>
			<?= $form->beginControlGroup($model, 'minPrice'); ?>
			<?= $form->controlLabel($model, 'minPrice'); ?>
			<div class="controls">
				<?php echo $form->dropDownList($model, 'minPrice', Util::getPropertyPrices("minimum"), ['class' => 'input-xsmall', 'empty' => 'Min']) ?>
				<?php echo $form->dropDownList($model, 'maxPrice', Util::getPropertyPrices("maximum"), ['class' => 'input-xsmall', 'empty' => 'Max']) ?>
			</div>
			<?= $form->endControlGroup(); ?>
			<?= $form->beginControlGroup($model, 'minBedrooms'); ?>
			<?= $form->controlLabel($model, 'minBedrooms'); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'minBedrooms', ['class' => 'input-xxsmall', 'placeholder' => 'Min']) ?>
				<?php echo $form->textField($model, 'maxBedrooms', ['class' => 'input-xxsmall', 'placeholder' => 'max']) ?>
			</div>
			<?= $form->endControlGroup(); ?>
			<div class="control-group">
				<label class="control-label">
					Status
					<input type="checkbox" id="status-trigger">
				</label>

				<div class="controls">
					<?php $x = 0; ?>
					<table style="border-collapse: collapse;">
						<tr>

							<?php foreach ($model->getStatusesList() as $key => $status): ?>
								<td>
									<?php echo CHtml::checkBox('Deal[dea_status][' . $x . ']', in_array($key, (array)$model->dea_status), [
											'value'        => $key,
											'uncheckValue' => '',
											'class'        => 'status-checkbox attr-status',
									]) ?>
									<label class="checkbox-enabler attr-status" for="Deal_dea_status_<?php echo $x ?>"
										   data-key="<?php echo $x ?>"><?php echo $status ?></label>
								</td>
								<?php echo ++$x % 6 == 0 ? '</tr><tr>' : '' ?>
							<?php endforeach; ?>
						</tr>
					</table>
				</div>
			</div>
			<?= $form->beginControlGroup($model, 'dea_ptype'); ?>
			<?= $form->controlLabel($model, 'dea_ptype'); ?>
			<div class="controls">
				<?php echo $form->checkBoxListWithSelectOnLabel($model, 'dea_ptype', Chtml::listData(PropertyType::model()->getTypes(), 'pty_id', 'pty_title'), ['separator' => ' ']) ?>
				<label style="display:inline-block; margin-left:28px;float:none; margin-right:12px; font-weight: bold">Branch</label>
				<?php echo $form->checkBoxListWithSelectOnLabel($model, 'dea_branch', CHtml::listData(Branch::model()->active()->findAll(), 'bra_id', 'bra_title'), ['separator' => ' ']); ?>
			</div>
			<?= $form->endControlGroup(); ?>

			<div class="control-group">
				<label class="control-label">
					Negotiator
				</label>

				<div class="controls">
					<label><?php echo $form->radioButton($model, 'dea_neg', ['value' => Yii::app()->user->id, 'uncheckValue' => null]) ?> Me</label>
					<label><?php echo $form->radioButton($model, 'dea_neg', ['value' => '', 'uncheckValue' => null]) ?>All</label>
				</div>
			</div>
		</div>
		<div class="span4">
			<div class="control-group">
				<label class="control-label">
					Type
				</label>

				<div class="controls">
					<?php echo $form->checkBoxList($model, 'dea_type', [Deal::TYPE_LETTINGS => Deal::TYPE_LETTINGS, Deal::TYPE_SALES => Deal::TYPE_SALES], ['separator' => ' ']) ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">DIY properties</label>

				<div class="controls">
					<?php echo $form->checkBoxList($model, 'DIY', array_combine($t = [Deal::DIY_NONE, Deal::DIY_DIY, Deal::DIY_DIT], $t), ['separator' => ' ']) ?>
					<div>
						<span class="DIY-property" style="border: 1px solid #dedede; width: 10px; height: 10px; display: inline-block;"></span>DIY
						<span class="DIT-property" style="border: 1px solid #dedede; width: 10px; height: 10px; display: inline-block;"></span>DIT
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="block-buttons force-margin">
	<input type="submit" class="btn btn-small" value="search">
	<?php echo $form->filterResetButton('Reset', ['class' => 'btn btn-red']) ?>
</div>
