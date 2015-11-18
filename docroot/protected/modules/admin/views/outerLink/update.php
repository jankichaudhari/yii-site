<?php
/**
 * @var $model      OuterLink
 * @var $this       OuterLinkController
 * @var $form       AdminForm
 * @var $tabbedView TabbedLayout
 */
?>
<div class="row">
	<div class="span12">

		<?php $form = $this->beginWidget('AdminForm', array(
														   'id'                   => 'outer-link-form',
														   'enableAjaxValidation' => false,
													  )); ?>

		<fieldset>
			<div class="block-header">
				<?php echo $model->isNewRecord ? 'Create Link' : 'Update Link ' . $model->title ?>
			</div>
			<div class="block-buttons">
				<?php echo CHtml::link('« Back', $this->createUrl('Index'), ['class' => 'btn btn-red']) ?>
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

		<fieldset>
			<?php
			$tabbedView = $this->beginWidget('TabbedLayout', array(
																  'id'        => 'outer-link-' . $model->id,
																  'activeTab' => 'linkInfo'
															 ));
			?>

			<?php
			$tabbedView->beginTab("Link Information", ['id' => 'linkInfo']);
			echo '<div class="content">';
			include('tabs/form.php');
			echo '</div>';
			$tabbedView->endTab();
			?>

			<?php
			$tabbedView->beginTab("Manage Photos", ['id' => 'managePhotos']);
			echo '<div class="content">';
			?>
			<iframe src="<?php echo $this->createUrl("OuterLink/OuterLinkPhotos", ['id' => $model->id]) ?>"
					id="uploadPhoto" name="uploadPhoto" width="100%" frameborder="0" scrolling="no"
					onload='javascript:iframeHeight("uploadPhoto");'></iframe>
			<?php echo '</div>';
			$tabbedView->endTab();
			?>

			<?php $this->endWidget(); ?>
		</fieldset>

		<?php $this->endWidget(); ?>

	</div>
</div>

<script type="text/javascript">
	function iframeHeight(iframeId) {
		$("#" + iframeId).height($("#" + iframeId).contents().find("html").height());
	}
</script>