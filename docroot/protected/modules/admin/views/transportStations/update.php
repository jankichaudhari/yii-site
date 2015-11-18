<?php
/*
 * @var TransportStations $model
 *
 */
?>
<div class="form" xmlns="http://www.w3.org/1999/html">

	<?php $form=$this->beginWidget('CActiveForm', array(
													   'id'=>'transport-stations-form',
													   'enableAjaxValidation'=>false,
												  )); ?>

	<fieldset>
		<div class="block-header"><?php echo $model->isNewRecord ? "Create new Transport Station" : "Update Transport Station <span style='color:#000000;'>".$model->title ?></span></div>
		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<?php echo $form->errorSummary($model); ?>

		<div class="row">
			<?php /** @var $types TransportTypes[] */
			$types = TransportTypes::model()->findAll();
			foreach ($types as $type) {
				echo CHtml::checkBox(
					'TransportStations[type][' . $type->id . ']',
					$model->transportStationBelongsToTypes($type->id),
					array()
				);
				echo CHtml::label($type->title, 'TransportStations_type_' . $type->id,['style'=>'margin-right:7px;'] );
			}
			?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'title'); ?>
			<?php echo $form->textField($model,'title',array('size'=>50,'maxlength'=>255)); ?>
			<?php echo $form->error($model,'title'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'description'); ?>
			<?php echo $form->textArea($model,'description',array('rows'=>5, 'cols'=>50)); ?>
			<?php echo $form->error($model,'description'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'latitude'); ?>
			<?php echo $form->textField($model,'latitude'); ?>
			<?php echo $form->error($model,'latitude'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'longitude'); ?>
			<?php echo $form->textField($model,'longitude'); ?>
			<?php echo $form->error($model,'longitude'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'statusId'); ?>
			<?= $form->dropDownList($model,'statusId',CHtml::listData(Lists::model()->findAllByAttributes(['ListName'=>'TransportStationsStatus'],['order'=>'ListOrder DESC']),'ListItemID','ListItem')) ?>
			<?php echo $form->error($model,'statusId'); ?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton('Save'); ?>&nbsp;&nbsp;<?php echo CHtml::button('Cancel',['onclick'=>'window.parent.closeMarkerDetails();']); ?>
		</div>

		<?php $this->endWidget(); ?>
	</fieldset>
</div>


<!-- form -->
<?php if($recordSave) { ?>
<script type="text/javascript">
	window.parent.closeMarkerDetails('<?= $model->id ?>');
</script>
<?php } ?>