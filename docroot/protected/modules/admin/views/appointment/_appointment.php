<?php
Yii::app()->getClientScript()->registerCssFile(Yii::app()->getBaseUrl(true) . "/css/grid-view/style.css");
/**
 * @var $this  AdminController
 * @var $model Appointment
 * @var $form  CActiveForm
 */
?>
<div class="form wide">
	<?php $form = $this->beginWidget('CActiveForm', array(
														 'id'                  => 'appointment-form',
														 'enableAjaxValidation'=> false,

													)); ?>
	<fieldset>
		<legend><?php echo $model->isNewRecord ? "Book new appointment" : "Update appointment" ?></legend>
		<div class="row">
			<?php echo $form->labelEx($model, 'app_subject'); ?>
			<?php echo $form->textArea($model, 'app_subject'); ?>
		</div>
        <div class="row">
			<?php echo $form->labelEx($model, 'calendarID'); ?>
			<?php echo $form->dropDownList($model, 'calendarID', CHtml::listData(Branch::model()->findAll(['scopes'=>'active']), 'bra_id', 'bra_title')); ?>
        </div>
		<div class="row">
			<?php echo $form->labelEx($model, 'app_user'); ?>
			<?php echo $form->dropDownList($model, 'app_user', CHtml::listData(User::model()->findAll("use_status='Active'"), 'use_id', 'fullName')); ?>
		</div>
		<div class="row">
			<label>Attendees</label>
			<input type="button" value="Select attendees" id="selectAttendees">
		</div>
		<div class="row">
			<div class="grid-view minWidth" style="margin-left: 160px; ">
				<table class="items" id="attendee-list" style="display: none;">
					<tr>
						<th></th>
						<th>Name</th>
						<th></th>
					</tr>
				</table>
			</div>
		</div>
		<div class="row"></div>
		<div class="row">
			<?php echo $form->labelEx($model, 'app_start'); ?>
			<?php echo $form->textField($model, 'app_start', array('size' => 20)); ?>
			<?php echo $form->textField($model, 'startTime', array('placeholder' => 'hh:mm')); ?>

		</div>
		<div class="row">
			<?php echo $form->labelEx($model, 'app_end'); ?>
			<?php echo $form->textField($model, 'app_end', array('size' => 20)); ?>
			<?php echo $form->textField($model, 'endTime', array('placeholder' => 'hh:mm')); ?>
		</div>
		<div class="row">
			<label>Property</label>
			<input type="button" value="Select property" id="selectProperty">
		</div>

	</fieldset>
	<?= CHtml::submitButton('Save') ?>
	<?php $this->endWidget(); ?>
</div>
<script type="text/javascript" src="/js/mustache.js"></script>
<script type="tempalte/attendee" id="attendee-template">
	{{#users}}
	<tr id="attendee-{{use_id}}">
		<td>{{use_id}}</td>
		<td>{{fullName}}</td>
		<td>
			<img src="/images/sys/admin/icons/cross-icon.png" alt="" onclick="removeAttendee('{{use_id}}')">
			<input type="hidden" name="Appointment[attendees][]" value="{{use_id}}" id="Appointment[attendees][{{use_id}}]">
		</td>
	</tr>
	{{/users}}
</script>
<script type="text/javascript">
	User.init();

	var removeAttendee = function (id)
	{
		var el = document.getElementById("attendee-" + id + "");
		el.parentNode.removeChild(el);
		console.log(document.getElementById("attendee-list").rows.length);
		toogleAttendeeList();
	}

	var toogleAttendeeList = function ()
	{
		if (document.getElementById("attendee-list").rows.length <= 1) {
			$("#attendee-list").hide();
		} else {
			$("#attendee-list").show();
		}
	}
	toogleAttendeeList();

	User.attachEvent('onSelect', function (userIds)
	{
		User.getDataById(userIds, function (data)
		{
			var o = Mustache.render(document.getElementById("attendee-template").innerHTML, {users : data});
			$('#attendee-list').append(o);
			$('#attendee-list').show();
		});
	});

	$("#Appointment_app_start").datepicker();
	$("#Appointment_startTime").timepicker();
	$("#Appointment_app_end").datepicker();
	$("#Appointment_endTime").timepicker();

	$("#selectAttendees").on('click', function ()
	{
		User.openSelectScreen();
	});

	$("#selectProperty").on('click', function ()
	{
//		User.openSelectScreen();
	});


</script>