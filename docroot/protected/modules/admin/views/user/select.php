<?php
/**
 * @var $this            UserController
 * @var $form            AdminFilterForm
 * @var $model           User
 */

?>
<div class="form-inline">
	<?php $form = $this->beginWidget('AdminFilterForm', array(
															 'id'                  => 'user-filter-form',
															 'enableAjaxValidation'=> false,
															 'model'               => $model,
															 'ajaxFilterGrid'      => 'user-list',
														)); ?>
	<fieldset>
		<div class="row">
			<?php echo $form->labelEx($model, 'use_fname'); ?>
			<?php echo $form->textField($model, 'use_fname', array('size'     => 30,)); ?>
			<?php echo $form->labelEx($model, 'use_sname'); ?>
			<?php echo $form->textField($model, 'use_sname', array('size'     => 30,)); ?>
			<?php echo $form->labelEx($model, 'branch'); ?>
			<?php echo $form->dropDownList($model, 'use_branch', CHtml::listData(Branch::model()->active()->findAll(), 'bra_id', 'bra_title'), array('empty'=> '')); ?>
			<?php echo $form->labelEx($model, 'use_scope'); ?>
			<?php echo $form->checkBoxList($model, 'use_scope', $model->getPossibleScope(), array('separator' => '')); ?>
			<?php echo $form->filterResetButton() ?>
		</div>
	</fieldset>
	<?php $this->endWidget() ?>
</div>
<?php

$this->widget('AdminGridView', array(
									'dataProvider'     => $model->search(),
									'columns'          => array(
										array(
											'class' => 'CCheckBoxColumn'
										),
										array(
											'name'        => 'use_color',
											'header'      => 'Color',
											'type'        => 'raw', 'value'=> function(User $data)
										{

											return '<div style="height:12px;width:100%; border: #555 1px solid; background:#' . $data->use_colour . '"></div>';
										},
											'htmlOptions' => array('style' => 'width:20px;')
										),
										array(
											'name'        => 'use_id',
											'htmlOptions' => array('style' => 'width:30px;')
										),
										'fullName',
										'use_scope',
										array('name' => 'branch.bra_title', 'header' => 'Branch'),
									),
									'id'               => 'user-list',
									'selectableRows'   => 1000,
									'selectionChanged' => 'rowSelected'
							   )) ?>
<div class="form-inline" style="margin-top: 10px;">
	<fieldset>
		<div class="row buttons">
			<input type="button" onclick="useSelected(true)" value="Use selected and close window">
			<input type="button" onclick="useSelected()" value="Use selected">
			<input type="button" onclick="window.close()" value="Close">
		</div>
	</fieldset>
</div>
<script type="text/javascript">
	User.init(window.opener);

	var rowSelected = function (id)
	{

	}

	var useSelected = function (close)
	{

		var selectedRows = $.fn.yiiGridView.getSelection('user-list');
		User.select(selectedRows);

		if (close) {
			window.opener.focus();
			window.close();
		}
	}
</script>

