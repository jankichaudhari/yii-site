<?php
/**
 * @var $value Deal
 * @var $model Deal
 * @var $form  AdminFilterForm
 */
/** @var $deal Deal */


$model        = new Deal('search');
$model->owner = array($owner = new Client('search'));
$sql = '';
$criteria = new CDbCriteria(array('scopes' => array('complexSearchScope')));
?>
<div class="form-inline">
	<?php $form = $this->beginWidget('AdminFilterForm', array(

															 'id'                  => 'deal-filter-form',
															 'enableAjaxValidation'=> false,
															 'model'               => array($model, $owner),
															 'ajaxFilterGrid'      => 'deal-list',
														)); ?>
	<fieldset>
		<div class="row">
			<?php echo $form->labelEx($model, 'dea_id') ?>
			<?php echo $form->textField($model, 'dea_id') ?>
			<?php echo $form->labelEx($owner, 'cli_fname') ?>
			<?php echo $form->textField($owner, 'cli_fname') ?>
			<?php echo $form->labelEx($owner, 'cli_sname') ?>
			<?php echo $form->textField($owner, 'cli_sname') ?>
		</div>
	</fieldset>
	<?php $this->endWidget() ?>
</div>

<?php
$this->widget('AdminGridView', array(
									'id'           => 'deal-list',
									'dataProvider' => $model->search($criteria),
									'columns'      => array(

										'dea_id',
										array(
											'value' => function(Deal $data)
											{

												return $data->ownersNames;
											}
										),
										array(
											'value' => function(Deal $data)
											{

												return $data->property->getFullAddressString();
											}
										)
									)
							   ));