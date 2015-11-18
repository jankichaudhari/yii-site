<?php
/**
 * @var $this         QuickReportController
 * @var $data         Array
 * @var $dataProvider CArrayDataProvider
 * @var $model        HmrcForm
 * @var $form         CActiveForm
 */
?>
<?php $form = $this->beginWidget('CActiveForm', ['id' => 'hmrc-filter-form', 'method' => 'get', 'action' => $this->createUrl('hmrc')]); ?>
	<fieldset>
		<div class="block-header">Filter</div>
		<div class="content">
			<div class="control-group">
				<label class="control-label"><?php echo $form->labelEx($model, 'dateFrom') ?></label>

				<div class="controls">
					<?php echo $form->textField($model, 'dateFrom') ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo $form->labelEx($model, 'dateTo') ?></label>

				<div class="controls">
					<?php echo $form->textField($model, 'dateTo') ?>
					<span class="hint">Please follow the format of YYYY-MM-DD</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo $form->labelEx($model, 'timeBetweenApps') ?></label>

				<div class="controls">
					<?php echo $form->textField($model, 'timeBetweenApps') ?>
					<span class="hint">time between two following apps when neg will not go back to office</span>
				</div>
			</div>
		</div>
		<div class="block-buttons force-margin">
			<input type="submit" class="btn" value="Filter" />
			<input type="submit" class="btn" value="Export to CSV" name="export" />
		</div>
	</fieldset>
<?php $this->endWidget(); ?>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
												 'dataProvider' => $dataProvider,
												 'cssFile'      => "/css/grey-smooth/grid-view/style.css",
												 'pager'        => array(
													 'cssFile' => "/css/grey-smooth/pager.css"
												 ),
												 'columns'      => array(
													 'app_id::app',
													 'user::User',
													 'address::Address',
													 'coming_from::Coming From',
													 'going_to::Going To',
													 'distanceFromPrevPlace::Distance',
													 'distanceToOffice::Return',
													 'time::App Starts',
													 'end_time::App Ends',
												 )
											));

