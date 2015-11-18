<style type="text/css">
	.tabElements {
		background-color: #FFEBBD;
		margin: 10px 0px;
		padding: 10px 0px;
	}

	.goToElement {
		background-color: #FF9900;
	}
</style>
<?php
/**
 *
 * @package application.LocalEvent.views
 *
 * @var LocalEvent     $model
 * @var CActiveForm    $form
 * @var                $this LocalEventController
 * @var Location       $address
 * @var Image[]        $images
 * @var $clientScript CClientScript
 */
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/adminUtilHead.js', CClientScript::POS_HEAD);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/adminUtil.js', CClientScript::POS_END);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js');

$saveButton = CHtml::button($model->isNewRecord ? 'Create' : 'Save', [
																	 'class'   => 'btn',
																	 'onClick' => "stopPopupPreview('#local-event-form')"
																	 ]);
$previewButton = CHtml::button('Preview', [
										  'class'   => 'btn btn-gray',
										  "onClick" => "popupPreview('#local-event-form','/local-event/" . $model->id . "')"
										  ]);
?>

<?php $form = $this->beginWidget('AdminForm', array(
												   'id'                   => 'local-event-form',
												   'enableAjaxValidation' => false,
												   'htmlOptions'          => array('enctype' => 'multipart/form-data'),
											  )); ?>

<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-buttons">
				<?php echo $saveButton ?>
				<?php echo $previewButton ?>
				<a href="#eventSummary" class="btn btn-gray">Events summary</a>
				<a href="#eventAddress" class="btn btn-gray">Events Address</a>
				<a href="#managePhoto" class="btn btn-gray">Manage Photo(s)</a>
			</div>
		</fieldset>
	</div>
</div>

<?php if ($model->hasErrors()): ?>
	<div class="content">
		<div class="flash danger"><?php echo $form->errorSummary(array($model, $address)); ?></div>
	</div>
<?php endif;
if (Yii::app()->user->hasFlash('success')) : ?>
	<div class="content">
		<div class="flash success remove"><?php echo Yii::app()->user->getFlash('success'); ?></div>
	</div>
<?php endif;
if (Yii::app()->user->hasFlash('error')) : ?>
	<div class="content">
		<div class="flash danger"><?php echo Yii::app()->user->getFlash('error'); ?></div>
	</div>
<?php endif; ?>

<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header" id="eventSummary">Events summary</div>
			<div class="content">
				<div class="control-group">
					<label class="control-label"><?= $form->controlLabel($model, 'heading'); ?></label>

					<div class="controls">
						<?= $form->textField($model, 'heading', ['maxlength' => 255, 'class' => 'input-halfblock']); ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $form->controlLabel($model, 'url'); ?></label>

					<div class="controls">
						<?= $form->textField($model, 'url', ['maxlength' => 255, 'class' => 'input-halfblock']); ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $form->controlLabel($model, 'strapline'); ?></label>

					<div class="controls">
						<?=
						$form->textArea($model, 'strapline', [
															 'rows' => 3, 'cols' => 60, 'class' => 'input-xxlarge'
															 ]); ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $form->controlLabel($model, 'description'); ?></label>

					<div class="controls">
						<?=
						$form->textArea($model, 'description', [
															   'class' => 'input-xxlarge', 'style' => 'height:500px'
															   ]); ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $form->controlLabel($model, 'dateFrom'); ?></label>

					<div class="controls">
						<?=
						$form->textField($model, 'dateFrom', array(
																  'placeholder' => 'dd/mm/yyyy',
																  'class'       => 'input-xsmall',
																  'value'       => $model->dateFrom ? date("d/m/Y", strtotime($model->dateFrom)) : ''
															 )); ?>
						<?=
						$form->textField($model, 'timeFrom', array(
																  'placeholder' => 'hh:mm',
																  'class'       => 'input-xsmall',
															 )); ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $form->controlLabel($model, 'dateTo'); ?></label>

					<div class="controls">
						<?=
						$form->textField($model, 'dateTo', array(
																'placeholder' => 'dd/mm/yyyy',
																'class'       => 'input-xsmall',
																'value'       => $model->dateTo ? date("d/m/Y", strtotime($model->dateTo)) : ""
														   )); ?>
						<?=
						$form->textField($model, 'timeTo', array(
																'placeholder' => 'hh:mm',
																'class'       => 'input-xsmall',
														   )); ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $form->controlLabel($model, 'status'); ?></label>

					<div class="controls">
						<?= $form->dropDownList($model, 'status', Lists::model()->getList("LocalEventStatus")); ?>
					</div>
				</div>
			</div>

		</fieldset>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header" id="eventAddress">Events Address</div>
			<div class="content"><?php $this->renderPartial("application.modules.admin4.views.location._location_form", array(
																															 'model'       => $address,
																															 'form'        => $form,
																															 'parentModel' => $model,
																															 'parentField' => 'addressID'
																														)) ?></div>
		</fieldset>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header" id="managePhoto">Manage Photo(s)</div>
			<div class="content">
				<iframe src="<?php echo $this->createUrl("LocalEvent/localEventPhotos", ['id' => $model->id]) ?>"
						id="uploadPhoto" name="uploadPhoto" width="100%" frameborder="0" scrolling="no"></iframe>
			</div>
		</fieldset>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-buttons">
				<?php echo $saveButton ?>
				<?php echo $previewButton ?>
				<a href="#eventSummary" class="btn btn-gray">Events summary</a>
				<a href="#" class="btn btn-gray">Events Address</a>
				<a href="#" class="btn btn-gray">Manage Photo(s)</a>
			</div>
		</fieldset>
	</div>
</div>
<?php $this->endWidget(); ?>
<script type="text/javascript">

	/*function iframeHeight(iframeId) {
	 $("#" + iframeId).height($("#" + iframeId).contents().find("html").height());
	 }

	 function popUpPreview() {
	 var thisSelector = $('#local-event-form');
	 thisSelector.attr('action', "/local-event/" + <?php //echo $model->id ?>);
	 var t = window.open('about:blank', "popUpWindow", "status=1,scrollbars=1,menubar=1,resizable=1,width=1200,height=1000");
	 thisSelector.attr('target', "popUpWindow");
	 thisSelector.submit();
	 return false;
	 }

	 function stopPopUp() {
	 var thisSelector = $('#local-event-form');
	 thisSelector.attr('action', "");
	 thisSelector.attr('target', "_self");
	 thisSelector.submit();
	 return true;
	 }*/


	$("#LocalEvent_dateFrom").datepicker();
	$("#LocalEvent_timeFrom").timepicker();
	$("#LocalEvent_dateTo").datepicker();
	$("#LocalEvent_timeTo").timepicker();

	CKEDITOR.replace('LocalEvent_description', {
		width: $('#LocalEvent_description').width(),
		height: $('#LocalEvent_description').height()
	});

	$('#local-event-form').on('submit', function () {
		if (($('#local-event-form').attr('action')) == "_self") {
			$("#uploadPhoto").contents().find('#uploadMainImage').trigger('click');
			$("#uploadPhoto").contents().find('#uploadImages').trigger('click');
		}
	});
</script>
