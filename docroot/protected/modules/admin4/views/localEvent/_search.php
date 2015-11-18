<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'strapline'); ?>
		<?php echo $form->textField($model,'strapline',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'heading'); ?>
		<?php echo $form->textField($model,'heading',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dateFrom'); ?>
		<?php echo $form->textField($model,'dateFrom'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dateTo'); ?>
		<?php echo $form->textField($model,'dateTo'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'timeFrom'); ?>
		<?php echo $form->textField($model,'timeFrom'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'timeTo'); ?>
		<?php echo $form->textField($model,'timeTo'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'url'); ?>
		<?php echo $form->textField($model,'url',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'addressID'); ?>
		<?php echo $form->textField($model,'addressID',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'createdBy'); ?>
		<?php echo $form->textField($model,'createdBy'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->