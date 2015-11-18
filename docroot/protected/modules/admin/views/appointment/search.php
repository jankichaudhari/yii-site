<?php
/**
 * @var $this  CController
 * @var $model Appointment
 * @var $form  AdminFilterForm
 */
$form = $this->beginWidget('AdminFilterForm', array(
												   'model'                => $model,
												   'id'                   => 'appointment-filter-form',
												   'enableAjaxValidation' => false,
												   'ajaxFilterGrid'       => 'appointment-list',
												   												   'storeInSession' => false,
												   'focus'                => [$model, 'searchString']
											  ));
?>
<fieldset>
	<div class="block-header">
		SEARCH APPOINTMENT
	</div>
	<div class="content">

		<?= $form->beginControlGroup($model, 'searchString'); ?>
		<label class="control-label">Search</label>

		<div class="controls">
			<?php echo $form->textField($model, 'searchString') ?>
		</div>
		<?= $form->endControlGroup(); ?>

		<?= $form->beginControlGroup($model, 'app_user'); ?>
		<?= $form->controlLabel($model, 'app_user'); ?>
		<div class="controls">
			<?php
			echo $form->dropDownList($model,
									 'app_user',
									 CHtml::listData(User::model()->alphabetically()->onlyActive()->findAll(), 'use_id', 'fullName'),
									 ['empty' => 'all'])
			?>
			<span class="text" style="margin-left: 7px; border-bottom: 1px dashed #555; cursor: pointer;"
				  id="select-user">me</span>
		</div>
		<?= $form->endControlGroup(); ?>
		<?= $form->beginControlGroup($model, 'app_start'); ?>
		<?= $form->controlLabel($model, 'app_start'); ?>
		<div class="controls">
			<?php
			$appStart = $model->app_start ? Date::formatDate('d/m/Y',$model->app_start) : '';
			$appStartVal = $model->app_start ? Date::formatDate('d-m-Y',$model->app_start) : '';
			?>
						<?php echo $form->textField($model, 'app_start', [
																		 'class' => 'datepicker', 'placeholder' => 'dd-mm-yyyy'
																		 ])
			?>
		</div>
		<?= $form->endControlGroup(); ?>
		<?= $form->beginControlGroup($model, 'app_end'); ?>
		<?= $form->controlLabel($model, 'app_end'); ?>
		<div class="controls">
			<?php
			$appEnd = $model->app_end ? Date::formatDate('d/m/Y',$model->app_end) : '';
			$appEndVal = $model->app_end ? Date::formatDate('d-m-Y',$model->app_end) : '';
			?>
						<?php echo $form->textField($model, 'app_end', ['class' => 'datepicker', 'placeholder' => 'dd-mm-yyyy']) ?>
		</div>
		<?= $form->endControlGroup(); ?>


		<?= $form->beginControlGroup($model, 'app_type'); ?>

		<label class="control-label" for="Appointment_app_type">
			<input type="checkbox" id="note-type-trigger">
			Type
		</label>

		<div class="controls">
			<?php echo $form->checkBoxListWithSelectOnLabel($model, 'app_type', Appointment::getTypes(), [
																										 'separator' => ' ',
																										 'class'     => 'note-types'
																										 ]) ?>
		</div>
		<?= $form->endControlGroup(); ?>
		<?= $form->beginControlGroup($model, 'app_notetype', ['id' => 'notetypes', 'style' => 'display: none']); ?>
		<label class="control-label">Note type</label>

		<div class="controls">
			<?php echo $form->checkBoxListWithSelectOnLabel($model, 'app_notetype', Appointment::getNoteTypes(), ['separator' => ' ']) ?>
		</div>
		<?= $form->endControlGroup(); ?>
	</div>
</fieldset>
<?php
$this->endWidget();
$this->widget('AdminGridView', array(
									'dataProvider' => $dataProvider,
									'title'        => 'SEARCH APPOINTMENT',
									'id'           => 'appointment-list',
									'columns'      => array(
										array(
											'class'    => 'CButtonColumn',
											'header'   => 'Actions',
											'template' => '{edit}',
											'buttons'  => array(
												'edit' => array(
													'label'    => 'Edit',
													'url'      => function (Appointment $data) {

														return AppointmentController::createEditLink($data->app_id);
													},
													'imageUrl' => Icon::EDIT_ICON,
												)
											),
										),
										array(
											'name'        => 'app_status',
											'htmlOptions' => ['style' => 'width: 80px;',],
										),
										'app_start' => array(
											'name'        => 'app_start',
											'header'      => 'start',
											'htmlOptions' => ['style' => 'width: 130px;',],
											'value'       => function (Appointment $data) {

												return Date::formatDate('d/m/Y H:i', $data->app_start);
											}
										),
										'app_type',
										'app_notetype',
										'app_subject',
										array(
											'name'   => 'user.fullName',
											'header' => 'User',
											'value'  => function ($data) {

												return $data->user ? $data->user->fullName : 'unassigned';
											}
										),
										array(
											'name'   => 'client',
											'header' => 'Clients',
											'type'   => 'raw',
											'value'  => function (Appointment $data) {

												$t = [];
												foreach ($data->clients as $key => $client) {
													$t[] = CHtml::link($client->getFullName(), Yii::app()->createUrl('admin4/client/update', ['id' => $client->cli_id]));
												}
												return implode(', ', $t);

											}
										),
										array(
											'header' => 'Address',
											'name'   => 'address',
											'type'   => 'raw',
											'value'  => function (Appointment $data) {

												$str = [];
												foreach ($data->addresses as $key => $value) {
													$str[] = $value->getFullAddressString(', ');
												}
												return implode('<br>', $str);

											}
										)

									)
							   )
);

?>
<script type="text/javascript">
	$(".datepicker").datepicker({
		'showOn': 'focus',
		'dateFormat' : 'dd-mm-yy',
		onSelect: function () {
			$(this).trigger('keyup');
		}
	});

	$('#select-user').on('click', function () {
		$('#Appointment_app_user [value="<?php echo Yii::app()->user->id ?>"]').attr('selected', 'selected');
		$('#Appointment_app_user').trigger('change');
	});

	$('#note-type-trigger').on('change', function () {
		$('.note-types').attr('checked', $(this).is(':checked'));
		if ($(this).is(':checked')) {
			$('#notetypes').show();
		} else {
			$('#notetypes').hide();
		}
	});

	$('#Appointment_app_type_5').on('change', function () {
		if ($(this).is(':checked')) {
			$('#notetypes').show();
		} else {
			$('#notetypes').hide();
		}
	});


</script>

