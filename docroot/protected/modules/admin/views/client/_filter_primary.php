<?php
/**
 * @var $this   ClientController
 * @var $model  Client
 * @var $form   AdminFilterForm
 * @var $gridId String
 * @var $id     String
 *
 */
?>

<?php $form = $this->beginWidget('AdminFilterForm', array(

														 'id'                   => isset($id) && $id ? $id : 'client-filter-form',
														 'enableAjaxValidation' => false,
														 'model'                => array($model, $model->telephones[0]),
														 'ajaxFilterGrid'       => isset($gridId) && $gridId ? $gridId : 'client-list',
//														 'storeInSession'       => false,
														 'focus'                => [$model, 'fullName'],
													)); ?>
<fieldset>
	<div class="block-header">Search Client</div>
	<div class="content"><?php echo $form->labelEx($model, 'fullName') ?>
		<?php echo $form->textField($model, 'fullName', array('size' => 30)) ?>
		<?php echo CHtml::label('Telephone', 'Telephone_tel_number') ?>
		<?php echo $form->textField($model->telephones[0], 'tel_number', array('size' => 30)) ?></div>
</fieldset>
<?php $this->endWidget() ?>
