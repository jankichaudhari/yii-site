<?php
/**
 * @var $this       UserController
 * @var $model      User
 * @var $form       AdminForm
 * @var $tabbedView TabbedLayout
 */
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jscolor/jscolor.js');
$tabWidgetId = 'user-' . $model->use_id;
?>

<?php
$form = $this->beginWidget('AdminForm', array(
											 'id'                   => 'user-form',
											 'enableAjaxValidation' => false,
										));
?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header">
					<?php echo "Update User " . $model->use_username ?>
				</div>
				<div class="block-buttons">
					<?php echo CHtml::link('« Back', $this->createUrl('Index'), ['class' => 'btn btn-red']) ?>
					<?php echo CHtml::submitButton('Save', ['class' => 'btn']); ?>
				</div>

				<div class="row-fluid">
					<div class="span12">
						<div class="content">
							<?php
							if ($model->hasErrors()) {
								echo '<div class="flash danger">';
								echo $form->errorSummary($model);
								echo '</div>';
							}
							?>
						</div>
					</div>
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
			$tabbedView->beginTab("Personal Information", ['id' => 'personalInfo']);
			echo '<div class="content">';
			include('tabs/personalInfo.php');
			echo '</div>';
			$tabbedView->endTab();
			?>

			<?php
			$tabbedView->beginTab("Roles", ['id' => 'userRoles']);
			echo '<div class="content">';
			include('tabs/userRoles.php');
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