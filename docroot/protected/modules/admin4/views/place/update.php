<?php
/**
 * @var                $this         PlaceController
 * @var Place          $model
 * @var Location       $location
 * @var Image[]        $images
 * @var Location       $location
 * @var AdminForm      $form
 * @var TabbedLayout   $tabbedView
 * @var                $backButton
 * @var                $mainViewImage
 * @var                $mainGalleryImage
 * @var                $newRecord
 * @var                $galleryImageError
 * @var                $clientScript CClientScript
 */
?>
<?php
$clientScript = Yii::app()->clientScript;
$clientScript->registerCssFile(Yii::app()->baseUrl . '/css/PlaceForm.css');
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/adminUtilHead.js', CClientScript::POS_HEAD);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/adminUtil.js', CClientScript::POS_END);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js');

$backButton = '<a href="' . $this->createUrl("index") . '" class="btn btn-gray">Back</a>';
?>
<script type="text/javascript">
	var mainViewImageFlag = 0;
	var mainGalleryImageFlag = 0;
	var galleryImagesFlag1 = 0;
	var galleryImagesFlag2 = 0;
</script>


<?php $form = $this->beginWidget('AdminForm', array(
												   'id'                   => 'place-form',
												   'enableAjaxValidation' => false,
												   'htmlOptions'          => array('enctype' => 'multipart/form-data'),
											  )); ?>

<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header"><?php echo "Update Place: " . $model->title ?></div>
			<div class="block-buttons">
				<?php echo $backButton ?>
				<input type="submit" class="btn" value="Save" onclick="stopPopupPreview('#place-form')">
				<input type="submit" class="btn" value="Preview"
					   onclick="popupPreview('#place-form','/park/<?= $model->id ?>')">
			</div>
			<div class="content" id="placeInfo">
				<?php
				if ($model->hasErrors()) {
					echo '<div class="flash danger">';
					echo $form->errorSummary(array($model, $location, $mainViewImage, $mainGalleryImage));
					echo '</div>';
				}

				$galleryImgErMsg = '';
				if ($galleryImageError) {
					if ($galleryImageError == 1) {
						$galleryImgErMsg = 'At least, four Gallery/Article Photos must be uploaded';
						echo '<script type="text/javascript">galleryImagesFlag1 = 1</script>';
					}
					if ($galleryImageError == 2) {
						$galleryImgErMsg = 'Please check caption(s) for Gallery/Article Photo(s) must be uploaded';
						echo '<script type="text/javascript">galleryImagesFlag2 = 1</script>';
					}
				}

				$viewImgEr = $form->error($model, 'mainViewImageId');
				$viewImgCapEr = $form->error($mainViewImage, 'caption');
				if (!empty($viewImgEr) || (!empty($viewImgCapEr))) {
					echo '<script type="text/javascript">mainViewImageFlag = 1;</script>';
				}

				$viewGalleryImg = $form->error($model, 'mainGalleryImageId');
				$viewGalleryCapImg = $form->error($mainGalleryImage, 'caption');
				if (!empty($viewGalleryImg) || (!empty($viewGalleryCapImg))) {
					echo '<script type="text/javascript">mainGalleryImageFlag = 1;</script>';
				}
				?>
			</div>

			<?php if (Yii::app()->user->hasFlash('success')) { ?>
				<div class="content">
					<div class="flash success remove">
						<?php echo Yii::app()->user->getFlash('success'); ?>
					</div>
				</div>
			<?php } ?>
		</fieldset>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<?php $tabbedView = $this->beginWidget('TabbedLayout', array(
																	'id'        => 'place-form-' . $model->id,
																	'activeTab' => 'placeInfo'
															   )); ?>

		<?php $tabbedView->beginTab("Park Info", array('id' => 'placeInfo')) ?>
		<div class="content">
			<?php include('tabs/form.php') ?>
		</div>
		<?php $tabbedView->endTab(); ?>

		<?php $tabbedView->beginTab("Address", array('id' => 'placeAddress')) ?>
		<div class="content">
			<?php $this->renderPartial("application.modules.admin4.views.location._location_form", array(
																										'model'           => $location,
																										'form'            => $form,
																										'parentModel'     => $model,
																										'parentModelName' => 'Place',
																										'parentField'     => 'addressId'
																								   )) ?>
		</div>
		<?php $tabbedView->endTab(); ?>

		<?php $tabbedView->beginTab("Manage Photos", array('id' => 'managePhoto')) ?>
		<div class="content">
			<iframe src="<?php echo $this->createUrl("/admin4/place/UpdateImages", ['id' => $model->id]) ?>"
					id="uploadImage" name="uploadImage" width="100%" frameborder="0" scrolling="no"
					></iframe>
		</div>
		<?php $tabbedView->endTab(); ?>

		<?php $tabbedView->beginTab("Description", array('id' => 'placeDescription')) ?>
		<div class="content">
			<?php echo $form->textArea($model, 'description', array(
																   'class' => 'input-halfblock',
																   'style' => "height:1000px;"
															  )); ?>
		</div>
		<?php $tabbedView->endTab(); ?>

		<?php $this->endWidget() ?>
	</div>
</div>

<?php $this->endWidget() ?>


<script type="text/javascript">


	CKEDITOR.replace('Place_description', {
		width: $('#Place_description').width(),
		height: $('#Place_description').height(),
		toolbar: 'placeToolbar',
		toolbar_placeToolbar: [
			{ name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', 'Image'] }
		],

		resize_enabled: true,
		filebrowserBrowseUrl: '<?php echo $this->createUrl('/admin4/place/SelectImage', array('recordType' => 'Place', 'recordId' => $model->id)) ?>',
		filebrowserUploadUrl: '<?php echo Yii::app()->createUrl('/admin4/place/SaveDescriptionImage', array('id' => $model->id)) ?>',
		filebrowserImageWindowWidth: '1000',
		filebrowserImageWindowHeight: '1000'
	});


	function displayErrorSummary() {
		var thisSelector = '#placeInfo .errorSummary ul';
		if ($(thisSelector).length == 0) {
			$('#placeInfo').before('<div class="errorSummary flash danger"><p>Please fix the following input errors:</p><ul><li><?= $galleryImgErMsg ?></li></ul></div>')
		} else
			$(thisSelector).append('<li><?= $galleryImgErMsg ?></li>');

	}

	function checkImageError(element) {
		if (element == 'mainViewImage' && mainViewImageFlag == 1) {
			return true;
		} else if (element == 'mainGalleryImage' && mainGalleryImageFlag == 1) {
			return true;
		} else if (element == 'GalleryImage' && (galleryImagesFlag1 == 1 || galleryImagesFlag2 == 1)) {
			return true;
		}
	}

	$(".flash-success").animate({opacity: 1.0}, 1000).fadeOut("slow");

	<?php if ($galleryImageError) { ?>
	displayErrorSummary();
	<?php } ?>
</script>