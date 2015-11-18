<?php
/**
 * @var $this   ClientController
 * @var $model  Client
 * @var $form   AdminFilterForm
 * @var $gridId String
 * @var $id     String
 * @var $title  String
 */
$form = $this->beginWidget('AdminFilterForm', array(
												   'id'                   => isset($id) && $id ? $id : 'client-filter-form',
												   'enableAjaxValidation' => false,
												   'model'                => [$model, $model->telephones[0]],
												   'ajaxFilterGrid'       => isset($gridId) && $gridId ? $gridId : 'client-list',
												   'storeInSession'       => false,
												   'focus'                => [$model, 'fullName'],
											  ));
?>
<div class="block-header"><?php echo $title ?></div>
<fieldset>
	<div class="content">
		<?php echo $form->labelEx($model, 'fullName') ?>
		<?php echo $form->textField($model, 'fullName') ?>
		<?php echo CHtml::label('Telephone', 'Telephone_tel_number') ?>
		<?php echo $form->textField($model->telephones[0], 'tel_number') ?>
	</div>
</fieldset>
<?php $this->endWidget() ?>
