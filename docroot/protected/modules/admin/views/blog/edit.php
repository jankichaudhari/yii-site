<?php
/**
 * @var $this         BlogController
 * @var $model        Blog
 * @var $form         AdminForm
 * @var $clientScript CClientScript
 */
//$action = $model->isNewRecord ? $this->createUrl('create') : $this->createUrl('update', ['id' => $model->id]); // this is required to get rid of preview url?

$clientScript = Yii::app()->getClientScript();
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js');
$form = $this->beginWidget('AdminForm', ['htmlOptions' => ['enctype' => 'multipart/form-data']]);
?>
<div class="row-fluid">
	<div class="span9">
		<fieldset>
			<div class="block-header"><?php echo $model->isNewRecord ? 'Create' : 'Update' ?> Post</div>
			<div class="content">
				<div class="control-group">
					<label class="control-label">Post Title</label>

					<div class="controls">
						<?php echo $form->textField($model, 'title') ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Post status</label>

					<div class="controls">
						<?php echo $form->dropDownList($model, 'status', Blog::getStatuses()) ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Strapline</label>

					<div class="controls">
						<?php echo $form->textArea($model, 'strapline') ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Post Body</label>

					<div class="controls">
						<?php echo $form->textArea($model, 'body', ['id' => 'post-body']) ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Featured Image</label>

					<div class="controls">
						<?php echo CHtml::fileField('upload') ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Current Image</label>

					<div class="controls">
						<?php echo $model->featuredImageModel ? CHtml::image($model->featuredImageModel->getUrl(), 'featured image', [
																																	 'width' => '230', 'height' => '130'
																																	 ]) : 'No Image Uploaded' ?>
					</div>
				</div>

			</div>
			<div class="block-buttons force-margin">
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn']) ?>
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Create & Preview' : 'Update & Preview', ['class' => 'btn', 'name' => 'preview']) ?>
			</div>
		</fieldset>
	</div>
</div>
<?php $this->endWidget() ?>
<script type="text/javascript">
	(function ()
	{
		CKEDITOR.replace('post-body', {
			height               : 600,
			toolbar              : 'placeToolbar',
			toolbar_placeToolbar : [
				{ name : 'basicstyles', items : ['Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', 'Image'] }
			],

			resize_enabled               : true,
			filebrowserUploadUrl         : '<?php echo $this->createUrl('uploadImage') ?>',
			filebrowserImageWindowWidth  : '1000',
			filebrowserImageWindowHeight : '1000'
		});

		var preview = <?php echo isset($_GET['preview']) && $_GET['preview'] ? 'true' : 'false' ?>;
		if (preview) {
			window.open('<?php echo $this->createUrl('/blog/view', ['id' => $model->id]) ?>', 'popUpWin', 'status=1,scrollbars=1,menubar=1,resizable=1,width=1200,height=1000');
		}
	})();
</script>