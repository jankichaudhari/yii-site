<?php
/**
 * @var $this         UserController
 * @var $dataProvider CActiveDataProvider
 * @var $model        User
 */
?>
<style type="text/css">
	#user-list .items tr {
		cursor : default;
	}
</style>
<?php
$self = $this;
$buttonColumn = array(
	'header'   => 'Actions',
	'class'    => 'CButtonColumn',
	'template' => '{view} {resetPassword}',
	'buttons'  => array(
		'view'          => array(
			'label'    => 'Edit User',
			'url'      => function ($data) use ($self) {

				return $self->createUrl('update', array('id' => $data->use_id));
			},
			'imageUrl' => Icon::EDIT_ICON
		),
		'resetPassword' => array(
			'label'    => 'Reset Password',
			'options'  => ['class' => 'resetPassword'],
			'url'      => function ($data) use ($self) {

				return $self->createUrl('User/UpdatePassword/', array('id' => $data->use_id));
			},
			'imageUrl' => Icon::RESET_PASSWORD_ICON,
		),
	)
);?>
<div class="row-fluid">
	<div class="span12">
		<?php $form = $this->beginWidget('AdminFilterForm', array(
																 'id'                   => 'user-filter-form',
																 'enableAjaxValidation' => false,
																 'model'                => $model,
																 'ajaxFilterGrid'       => 'user-list',
																 'storeInSession'       => false,
															)); ?>
		<fieldset>
			<div class="block-header">Search</div>
			<div class="content">
				<?= $form->beginControlGroup($model, 'use_fname'); ?>
				<?= $form->controlLabel($model, 'use_fname'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'use_fname'); ?>
					<?php echo $form->labelEx($model, 'use_sname'); ?>
					<?php echo $form->textField($model, 'use_sname'); ?>
				</div>
				<?= $form->endControlGroup(); ?>
				<?= $form->beginControlGroup($model, 'use_branch'); ?>
				<?= $form->controlLabel($model, 'use_branch'); ?>
				<div class="controls">
					<?php echo $form->dropDownList($model, 'use_branch', CHtml::listData(Branch::model()->active()->findAll(), 'bra_id', 'bra_title'), array('empty' => '')); ?>
				</div>
				<?= $form->endControlGroup(); ?>
				<?= $form->beginControlGroup($model, 'defaultCalendarID'); ?>
				<?= $form->controlLabel($model, 'defaultCalendarID'); ?>
				<div class="controls">
					<?php echo $form->dropDownList($model, 'defaultCalendarID', CHtml::listData(Branch::model()->active()
																										->findAll(), 'bra_id', 'bra_title'), array('empty' => '')); ?>
				</div>
				<?= $form->endControlGroup(); ?>

				<?= $form->beginControlGroup($model, 'use_scope'); ?>
				<?= $form->controlLabel($model, 'use_scope'); ?>
				<div class="controls">
					<?php echo $form->checkBoxList($model, 'use_scope', $model->getPossibleScope(), array('separator' => '')); ?>
				</div>
				<?= $form->endControlGroup(); ?>
				<?= $form->beginControlGroup($model, 'use_status'); ?>
				<?= $form->controlLabel($model, 'use_status'); ?>
				<div class="controls">
					<?php echo $form->checkBoxList($model, 'use_status', $model->getPossibleUserStatus(), array('separator' => '', 'defaultValue')); ?>
				</div>
				<?= $form->endControlGroup(); ?>
			</div>
			<div class="block-buttons force-margin"><?php echo $form->filterResetButton('Reset', ['class' => 'btn']) ?></div>
		</fieldset>
		<?php $this->endWidget() ?>

		<?php $this->widget('AdminGridView', array(
												  'id'           => 'user-list',
												  'dataProvider' => $model->search(),
												  'title'        => 'Users List',
												  'actions'      => array('add' => array($this->createUrl("Create"))),
												  'columns'      => array(
													  array(
														  'header' => '', 'value' => function ($data) {

														  if (!empty($data->use_colour)) {
															  return '<span style="background-color:#' . $data->use_colour . ';display:block;height:10px;width:100%;border:1px solid #000000"></span>';
														  }
													  },
														  'type'   => 'raw'
													  ),
													  $buttonColumn,
													  array('header' => 'Username', 'name' => 'use_username'),
													  array(
														  'header' => 'User\'s Full Name', 'value' => function ($data) {

														  return $data->getFullUsername();
													  }
													  ),
													  array('header' => 'Extension', 'name' => 'use_ext'),
													  array('header' => 'Mobile', 'name' => 'use_mobile'),
													  array('header' => 'Default Branch', 'name' => 'branch.bra_title'),
													  array('header' => 'Default Scope', 'name' => 'use_scope'),
													  array('header' => 'Default Calendar', 'name' => 'defaultCalendar.bra_title'),

													  array('header' => 'User Status', 'name' => 'use_status'),
													  $buttonColumn,
												  )
											 )); ?></div>
</div>
<script type="text/javascript">
	$("#user-list .items tr").on('dblclick', function (event)
	{
		var url = $(this).children('.button-column').children('.view').attr("href");
		location.href = url;
	});

	$('#user-list .resetPassword').live("click", function ()
	{
		var thisUrl = $(this).attr("href");
		var password = prompt("Please enter your password", "");

		if (password != null && password.length != 0) {
			$.post(thisUrl, { 'password' : password }, function (data)
			{
				if (data.length != 0) {
					alert("Your Password has been changed successfully...");
				} else {
					alert("Error!! Your Password has not been changed!!!")
				}
			});
		}
		return false;
	});
</script>