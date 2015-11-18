<?php
/**
 * @var $this            ClientController
 * @var $model           Client
 * @var $form            AdminFilterForm
 * @var $appointmentDate String
 * @var $appointmentType String
 *
 */
?>

<?php $form = $this->beginWidget('AdminFilterForm', array(
		'id'                   => 'client-filter-form',
		'enableAjaxValidation' => false,
		'model'                => [$model, $model->telephones[0]],
		'ajaxFilterGrid'       => 'client-list',
		'focus'                => [$model, 'fullName'],
		'storeInSession'       => false,
)); ?>
	<fieldset>
		<div class="block-header">Client Search</div>
		<div class="content">
			<div class="row-fluid">
				<div class="span6">
					<?= $form->beginControlGroup($model, 'fullName'); ?>
					<label class="control-label">Name/Email</label>

					<div class="controls">
						<?php echo $form->textField($model, 'fullName') ?>
					</div>
					<?= $form->endControlGroup(); ?>

					<?= $form->beginControlGroup($model->telephones[0], 'tel_number'); ?>
					<label class="control-label" for="Telephone_tel_number">Telephone</label>

					<div class="controls">
						<?php echo $form->textField($model->telephones[0], 'tel_number') ?>
					</div>
					<?= $form->endControlGroup(); ?>
					<div class="control-group">
						<label class="control-label">Budget</label>

						<div class="controls">
							<?php echo $form->dropDownList($model, 'minPrice', $this->getBudgetValues(), ['class' => 'input-xsmall', 'empty' => 'No Minimum']) ?>
							<?php echo $form->dropDownList($model, 'maxPrice', $this->getBudgetValues(), ['class' => 'input-xsmall', 'empty' => 'No Maximum']) ?>
							<label><?php echo $form->checkBox($model, 'searchNoBudget', ['value' => true, 'uncheckValue' => false]) ?>
								<span class="hint">include clients without budget</span></label>
						</div>
					</div>
					<?php echo $form->beginControlGroup($model, 'cli_salebed') ?>
					<?php echo $form->controlLabel($model, 'cli_salebed') ?>
					<div class="controls">
						<?php echo $form->dropDownList($model, 'cli_salebed', ['Studio', 1, 2, 3, 4, 5, 6, 7], ['class' => 'input-xsmall', 'empty' => 'all']) ?>
					</div>
					<?php echo $form->endControlGroup() ?>
					<div class="control-group">
						<label class="control-label">
							Registered in
						</label>

						<div class="controls">
							<?php echo $form->radioButtonList($model, 'cli_created', array(
									date('Y-m-d', strtotime('-1 week'))   => 'past week',
									date('Y-m-d', strtotime('-1 month'))  => 'past month',
									date('Y-m-d', strtotime('-6 months')) => 'past 6 months',
									date('Y-m-d', strtotime('-1 year'))   => 'past year',
							), ['separator' => '', 'empty' => 'anytime']) ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">
							Negotiator
						</label>

						<div class="controls">
							<?php echo $form->dropDownList($model, 'cli_neg', CHtml::listData(User::model()->onlyActive()->findAll(), 'use_id', 'fullName'), ['empty' => 'All']) ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">
							Branch
						</label>

						<div class="controls">
							<?php echo $form->checkBoxList($model, 'cli_branch', CHtml::listData(Branch::model()->active()
																									   ->findAll(), 'bra_id', 'bra_title'), ['separator' => ' ']) ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="control-group">
						<label class="control-label">
							Current Status
						</label>

						<div class="controls">
							<?php echo $form->checkBoxList($model, 'cli_salestatus', CHtml::listData(ClientStatus::model()->sales()->findAll(), 'cst_id', 'cst_title')) ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
<?php $this->endWidget() ?>