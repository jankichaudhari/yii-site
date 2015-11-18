<?php
/**
 * @var $this         InstructionController
 * @var $value        Deal
 * @var $model        Deal
 * @var $form         AdminFilterForm
 * @var $clientScript CClientScript
 */
?>

<div class="content">
	<div class="control-group">
		<label class="control-label">Valuation Price</label>

		<div class="controls">
			<?php echo $model->dea_valueprice ? : 'N/A' ?> - <?php echo $model->dea_valuepricemax ? : 'N/A' ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Valuation Date</label>

		<div class="controls">
			<?php echo $model->valuationDate ? : 'N/A' ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'dea_marketprice'); ?></label>

		<div class="controls">
			<?php echo $form->textField($model, 'dea_marketprice', ['class' => 'input-xsmall', 'value' => Locale::formatCurrency($model->dea_marketprice, true, false)]); ?>
			<?php echo $form->dropDownList($model, 'dea_qualifier', Deal::getQualifiers(), ['class' => 'input-xsmall']); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'dea_commission'); ?></label>

		<div class="controls">
			<?php echo $form->textField($model, 'dea_commission', ['class' => 'input-xsmall']); ?>
			<?php echo $form->dropDownList($model, 'dea_commissiontype', Util::enumItem($model, 'dea_commissiontype'), ['class' => 'input-xsmall']); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'dea_share'); ?></label>

		<div class="controls">
			<?php echo $form->dropDownList($model, 'dea_share', Util::enumItem($model, 'dea_share'), ['class' => 'input-xsmall']); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'dea_chainfree'); ?></label>

		<div class="controls">
			<?php echo $form->radioButtonList($model, 'dea_chainfree', Util::enumItem($model, 'dea_chainfree'), ['separator' => ' ']); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Category</label>

		<div class="controls">
			<?php
			/** @var $categories PropertyCategory[] */
			$categories = PropertyCategory::model()->active()->findAll();
			foreach ($categories as $category) {
				echo '<label>';
				echo CHtml::checkBox('Deal[category][' . $category->id . ']', $model->instructionBelongsToCategory($category->id));
				echo CHtml::label($category->title, 'Deal_category_' . $category->id);
				echo '</label>';
			}
			?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Tenure</label>

		<div class="controls text">
			<?php echo $model->dea_tenure ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Lease Expires</label>

		<div class="controls text">
			<?php echo $model->dea_leaseend ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Service Charge</label>

		<div class="controls text">
			<?php echo $model->dea_servicecharge ? : '—' ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Ground Rent</label>

		<div class="controls text">
			<?php echo $model->dea_groundrent ? : '—' ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"></label>

		<div class="controls">
			<?php $pdfSettingBtn = $this->createUrl('instruction/editPdfSettings', ['instructionId' => $model->dea_id]); ?>
			<?php echo
			CHtml::button('PDF Settings', [
					'onClick' => "popupWindow('" . $pdfSettingBtn . "')",
					'class'   => 'btn btn-gray'
			]); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<!--HIP Notes-->
			<?php $this->renderPartial("application.modules.admin4.views.note.addNote", array(
					'noteTypeId'   => $model->dea_id,
					'noteType'     => Note::TYPE_HIP,
					'title'        => 'HIP note(s)',
					'textBoxTitle' => 'HIP Note'
			)) ?>
			<!--HIP Notes-->
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'dea_contract'); ?></label>

		<div class="controls">
			<?php echo $form->dropDownList($model, 'dea_contract', Util::enumItem($model, 'dea_contract')); ?>
			<?php
			$contractChangesUrl = $this->createUrl('instruction/showChangeLogs/', ['instructionId' => $model->dea_id, 'columnName' => 'dea_contract']);
			echo CHtml::button('History', [
					'onClick' => "popupWindow('" . $contractChangesUrl . "')",
					'class'   => 'btn btn-gray'
			]);
			?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'dea_board'); ?></label>

		<div class="controls">
			<?php echo $form->dropDownList($model, 'dea_board', Util::enumItem($model, 'dea_board'), ['class' => 'input-xsmall']); ?>
			<?php echo $form->dropDownList($model, 'dea_boardtype', Util::enumItem($model, 'dea_boardtype'), ['class' => 'input-xsmall']); ?>
			<?php
			$boardChangesUrl = $this->createUrl('instruction/showChangeLogs', ['instructionId' => $model->dea_id, 'columnName' => 'dea_boardtype']);
			echo CHtml::button('History', [
					'onClick' => "popupWindow('" . $boardChangesUrl . "')",
					'class'   => 'btn btn-gray'
			]);
			?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'dea_branch'); ?></label>

		<div class="controls">
			<?php echo $form->dropDownList($model, 'dea_branch', CHtml::listData(Branch::model()->active()->findAll(), 'bra_id', 'bra_title')); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'dea_neg'); ?></label>

		<div class="controls">
			<?php $negotiatorList = (CHtml::listData(User::model()->onlyActive()->alphabetically()->findAll(), 'use_id', 'fullName')); ?>
			<?php echo $form->dropDownList($model, 'dea_neg', $negotiatorList, ['empty' => 'Unassigned']); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'dea_hip'); ?></label>

		<div class="controls">
			<?php echo $form->dropDownList($model, 'dea_hip', Util::enumItem($model, 'dea_hip')); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'dea_featured'); ?></label>

		<div class="controls">
			<?php echo $form->radioButtonList($model, 'dea_featured', Util::enumItem($model, 'dea_featured'), ['separator' => ' ']); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'underTheRadar'); ?></label>

		<div class="controls">
			<?php echo $form->checkBox($model, 'underTheRadar'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo $form->controlLabel($model, 'noPortalFeed'); ?></label>

		<div class="controls">
			<?php echo $form->checkBox($model, 'noPortalFeed'); ?>
		</div>
	</div>
</div>