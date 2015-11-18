<style type="text/css">

</style>
<?php
/**
 * @var $model      PropertyCategory [ ]
 * @var $this       PropertyCategoryController
 * @var $clientScript
 * @var $form       AdminForm
 * @var $tabbedView TabbedLayout
 */
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js');
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jscolor/jscolor.js');
$form = $this->beginWidget('AdminForm', ['id' => $model->isNewRecord ? 'property-category' : 'property-category-' . $model->id]);
?>

<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">
				<?php echo CHtml::link('Property Category List', ['propertyCategory/index'], ['style' => 'color: #555']) ?> » <?php echo $model->isNewRecord ? 'Create ' : 'Update ' ?>Property Category
			</div>
			<div class="content">
				<?php echo $model->isNewRecord ? '' : $model->title ?>
			</div>
		</fieldset>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<?php
		$tabbedView = $this->beginWidget('TabbedLayout', array(
															   'id'        => ($model->isNewRecord ? 'property-category' : 'property-category-' . $model->id),
															   'activeTab' => 'general'
													   )
		);
		?>

		<?php $tabbedView->beginTab("General", ['id' => 'general']) ?>
		<div class="content">
			<div class="control-group">
				<div class="controls force-margin">
					<div class="flash danger input-large"><?php echo $form->errorSummary($model) ?></div>
					<?php if (Yii::app()->user->hasFlash('success')) : ?>
						<div class="flash success remove input-xlarge"><?php echo Yii::app()->user->getFlash('success') ?></div>
					<?php endif ?>
				</div>
			</div>
			<?php echo $form->beginControlGroup($model, 'title'); ?>
			<?php echo $form->controlLabel($model, 'title'); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'title'); ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<?php echo $form->beginControlGroup($model, 'description'); ?>
			<?php echo $form->controlLabel($model, 'description'); ?>
			<div class="controls">
				<?php echo $form->textArea($model, 'description', ['style' => 'height:395px;width:750px;']); ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<?php echo $form->beginControlGroup($model, 'status'); ?>
			<?php echo $form->controlLabel($model, 'status'); ?>
			<div class="controls">
				<?php echo $form->radioButtonList($model, 'status', Lists::model()->getList("propertyCategoryStatus"), ['separator' => '']); ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<?php echo $form->beginControlGroup($model, 'displayOnHome'); ?>
			<?php echo $form->controlLabel($model, 'displayOnHome'); ?>
			<div class="controls">
				<?php echo $form->checkBox($model, 'displayOnHome') ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<?php echo $form->beginControlGroup($model, 'displayInMenu'); ?>
			<?php echo $form->controlLabel($model, 'displayInMenu'); ?>
			<div class="controls">
				<?php echo $form->checkBox($model, 'displayInMenu') ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<?php echo $form->beginControlGroup($model, 'matchClients'); ?>
			<?php echo $form->controlLabel($model, 'matchClients'); ?>
			<div class="controls">
				<?php echo $form->checkBox($model, 'matchClients') ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<div class="block-buttons force-margin">
				<input type="submit" class="btn" value="Save">
			</div>
		</div>

		<?php $tabbedView->endTab(); ?>

		<?php $tabbedView->beginTab("Homepage Styles", ['id' => 'manageStyles']); ?>
		<div class="content">

			<p style="color: red">
				This style will be overwritten by photos. To apply only colour(s), remove photo(s) from next tab for
				respective style.
			</p>
			<?php echo $form->beginControlGroup($model, 'displayName'); ?>
			<?php echo $form->controlLabel($model, 'displayName', ['class' => 'control-label long']); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'displayName'); ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<?php echo $form->beginControlGroup($model, 'bgColour'); ?>
			<?php echo $form->controlLabel($model, 'bgColour', ['class' => 'control-label long']); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'bgColour', ['class' => "color {pickerPosition:'right'}"]); ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<?php echo $form->beginControlGroup($model, 'textColour'); ?>
			<?php echo $form->controlLabel($model, 'textColour', ['class' => 'control-label long']); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'textColour', ['class' => "color {pickerPosition:'right'}"]); ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<?php echo $form->beginControlGroup($model, 'hoverBgColour'); ?>
			<?php echo $form->controlLabel($model, 'hoverBgColour', ['class' => 'control-label long']); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'hoverBgColour', ['class' => "color {pickerPosition:'right'}"]); ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<?php echo $form->beginControlGroup($model, 'hoverTextColour'); ?>
			<?php echo $form->controlLabel($model, 'hoverTextColour', ['class' => 'control-label long']); ?>
			<div class="controls">
				<?php echo $form->textField($model, 'hoverTextColour', ['class' => "color {pickerPosition:'right'}"]); ?>
			</div>
			<?php echo $form->endControlGroup(); ?>

			<div class="block-buttons force-margin">
				<input type="submit" class="btn" value="Save">
			</div>

		</div>
		<?php $tabbedView->endTab(); ?>

		<?php $tabbedView->beginTab("Manage Photos", ['id' => 'managePhotos']); ?>
		<div class="content">
			<iframe src="<?php echo $this->createUrl('PropertyCategory/ManagePhotos', ['id' => $model->id]); ?>"
					id="uploadPhoto" name="uploadPhoto" width="99%" frameborder="0" scrolling="no"
					onload='javascript:iframeHeight("uploadPhoto");'></iframe>
		</div>
		<?php $tabbedView->endTab(); ?>

		<?php $this->endWidget() ?>

	</div>
</div>

<?php $this->endWidget() ?>

<script type="text/javascript">
	(function ()
	{
		function iframeHeight(iframeId)
		{
			$("#" + iframeId).height($("#" + iframeId).contents().find("html").height());
		}

		$(window).resize(function ()
						 {
							 iframeHeight("uploadPhoto");
						 });

		CKEDITOR.replace('PropertyCategory_description', {
			width  : $('#PropertyCategory_description').width(),
			height : $('#PropertyCategory_description').height()
		});
	})();

</script>