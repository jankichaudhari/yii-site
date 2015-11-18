<?php
/**
 * @var $this             ClientController
 * @var $model            Client
 * @var $form             AdminFilterForm
 * @var $instructionModel Deal
 * @var $minPrices        array
 * @var $maxPrices        array
 */

$propertyCategories = PropertyCategory::model()->matchClients()->findAll();

$form = $this->beginWidget('AdminFilterForm', array(
		'id'                   => 'client-filter-form',
		'enableAjaxValidation' => false,
		'model'                => [$model, $model->telephones[0]],
		'ajaxFilterGrid'       => 'client-list',
		'focus'                => [$model, 'fullName'],
		'storeInSession'       => false,
));
?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="content">
					<?= $form->beginControlGroup($model, 'fullName'); ?>
					<label class="control-label">Search</label>

					<div class="controls">
						<?php echo $form->textField($model, 'fullName') ?>
					</div>
					<?= $form->endControlGroup(); ?>
				</div>
				<?php $this->renderPartial('filter/preferences', compact('model', 'form', 'instructionModel', 'minPrices', 'maxPrices')) ?>
				<div class="control-group">
					<label class="control-label">Property Categories</label>

					<div class="controls">
						<?php echo $form->checkBoxList($model, 'propertyCategoryIds', CHtml::listData($propertyCategories, 'id', 'title')) ?>
					</div>
				</div>

				<?php $this->renderPartial('filter/areas', compact('model', 'form', 'instructionModel')) ?>

			</fieldset>
		</div>
	</div>
<?php $this->endWidget() ?>