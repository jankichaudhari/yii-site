<?php
/**
 * @var $this       CController
 * @var $model      User
 * @var $form       AdminForm
 * @var $tabbedView TabbedLayout
 */
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jscolor/jscolor.js');
$tabWidgetId = 'user-preferences-' . $model->use_id;
?>

<?php
$form = $this->beginWidget('AdminForm', array(
											 'id'                   => 'user-preferences-form',
											 'enableAjaxValidation' => false,
										));
?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header">
					<?php echo $model->use_username ?>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<?php
			$tabbedView = $this->beginWidget('TabbedLayout', array(
																  'id'        => $tabWidgetId,
																  'activeTab' => 'systemInfo'
															 ));
			?>

			<?php
			$tabbedView->beginTab("System Information", ['id' => 'systemInfo']);
			echo '<div class="content">';
			include('tabs/systemInfo.php');
			echo '</div>';
			$tabbedView->endTab();
			?>

			<?php
			$tabbedView->beginTab("Email Alerts", ['id' => 'userEmailAlerts']);
			echo '<div class="content">';
			include('tabs/userEmailAlerts.php');
			echo '</div>';
			$tabbedView->endTab();
			?>

			<?php $this->endWidget(); ?>

			<div class="block-buttons">
				<?php echo CHtml::submitButton('Save', ['class' => 'btn']) ?>
			</div>
		</div>
	</div>

<?php $this->endWidget(); ?>