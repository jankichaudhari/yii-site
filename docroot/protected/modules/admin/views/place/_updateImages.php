<?php
/**
 * @var               $this         PlaceController
 * @var Place         $model
 * @var               $clientScript CClientScript
 */
$clientScript = Yii::app()->clientScript;
$clientScript->registerCssFile(Yii::app()->baseUrl . '/css/PlaceForm.css');
$this->layout = '';
?>

<style type="text/css">
	.placeImgPreview {
		position: relative;
		height: 250px;
		float: left;
		overflow: hidden;
	}
</style>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
													 'id'                   => 'place-image-form',
													 'enableAjaxValidation' => false,
													 'htmlOptions'          => array('enctype' => 'multipart/form-data'),
												)); ?>
<?php $id = (isset($model->id)) ? $model->id : 0; ?>
<div id="imageRows">

<!--Main View Image-->
<div id="MainViewImageInfo">
	<h3>Upload Listing Page Photo</h3>

	<div class="row" id="selectViewImageRow">
		<?php $this->widget('CMultiFileUpload', array(
													 'name'        => 'mainViewImage',
													 'id'          => 'mainViewImage',
													 'accept'      => 'jpeg|jpg|gif|png',
													 'max'         => 1,
													 'file'        => CHtml::image("/images/loading.gif", "", array('style' => 'cursor: pointer;cursor: hand;')),
													 'remove'      => '',
													 'duplicate'   => 'Duplicate Image',
													 'denied'      => 'Invalid Image',
													 'htmlOptions' => array(
														 'size'     => 25,
														 'onChange' => ''
													 ),
												));

		$mainViewImage = $model->mainViewImage;
		if (isset($mainViewImage) && (count($mainViewImage) > 0)) {
			$imageType = "'MainViewImage'";
			?>
			<div id="MainViewImage_Preview">
				<div class="placeImgPreviewTop">
					<?php echo CHtml::image(Icon::CROSS_ICON, "", array(
																	   'onclick' => 'deleteImage(' . $mainViewImage->id . ',' . $imageType . ',false)',
																	   'style'   => 'cursor: pointer;cursor: hand;float:right;'
																  )); ?>
				</div>
				<div><?php echo CHtml::image(Yii::app()->params['imgUrl'] . "/Place/" . $model->id . '/' . $mainViewImage->recordType . "/" . $mainViewImage->smallName, "", array('id' => $mainViewImage->fullPath . '/' . $mainViewImage->name)); ?></div>
				<div>
					<?php
					$mainImageText = !empty($mainViewImage->caption) ? $mainViewImage->caption : 'Click to add Caption';
					echo CHtml::link($mainImageText, '#', array(
															   'id'      => 'captionText_' . $mainViewImage->id,
															   'onClick' => 'addCaption(this);'
														  ));
					echo CHtml::textField('caption', $mainViewImage->caption, array(
																				   'placeHolder' => 'Type caption here...',
																				   'class'       => 'captionField',
																				   'id'          => 'captionField_' . $mainViewImage->id,
																				   'maxlength'   => 250
																			  ));
					?>
				</div>
			</div>
		<?php } ?>
		<div class="imageIcon"><?php echo CHtml::image(Icon::PLACE_IN_GRIDVIEW_POSITION_THUMBNAIL_ICON, ""); ?>    </div>
		<div class="imageInstruction">
			Please crop image to the ratio 3:2.
		</div>
		<br class="clear"/>
	</div>
</div>
<!--Main View Image-->

<!--Main Gallery Image-->
<!--<div id="MainGalleryImageInfo">-->
<!--	<h3>Upload Main Page Large Photo</h3>-->
<!---->
<!--	<div class="row" id="selectMainImageRow">-->
<!--		--><?php //$this->widget('CMultiFileUpload', array(
//													 'name'        => 'mainGalleryImage',
//													 'id'          => 'mainGalleryImage',
//													 'accept'      => 'jpeg|jpg|gif|png',
//													 'max'         => 1,
//													 'file'        => CHtml::image("/images/loading.gif", "", array('style' => 'cursor: pointer;cursor: hand;')),
//													 'remove'      => '',
//													 'duplicate'   => 'Duplicate Image',
//													 'denied'      => 'Invalid Image',
//													 'htmlOptions' => array(
//														 'size'     => 25,
//														 'onChange' => ''
//													 ),
//												));
//
//		$mainGalleryImage = $model->mainGalleryImage;
//		if (isset($mainGalleryImage) && (count($mainGalleryImage) > 0)) {
//			$imageType = "'MainGalleryImage'";
//
?>
<!--			<div id="MainGalleryImage_Preview">-->
<!--				<div class="placeImgPreviewTop">-->
<!--					--><?php //echo CHtml::image("/images/sys/admin/icons/cross-icon.png", "", array(
//																							   'onclick' => 'deleteImage(' . $mainGalleryImage->id . ',' . $imageType . ',false)',
//																							   'style'   => 'cursor: pointer;cursor: hand;float:right;'
//																						  ));
?>
<!--				</div>-->
<!--				<div>-->
<?php //echo CHtml::image(Yii::app()->params['imgUrl'] . "/Place/" . $model->id . '/' . $mainGalleryImage->recordType . "/" . $mainGalleryImage->smallName, "", array('id' => $mainGalleryImage->fullPath . '/' . $mainGalleryImage->name)); ?><!--</div>-->
<!--				<div>-->
<!--					--><?php
//					$mainGalleryText = !empty($mainGalleryImage->caption) ? $mainGalleryImage->caption : 'Click to add Caption';
//					echo CHtml::link($mainGalleryText, 'javascript:void(0);', array(
//																				   'id'      => 'captionText_' . $mainGalleryImage->id,
//																				   'onClick' => 'javascript:addCaption(this);'
//																			  ));
//					echo CHtml::textField('caption', $mainGalleryImage->caption, array(
//																					  'placeHolder' => 'Type caption here...',
//																					  'class'       => 'captionField',
//																					  'id'          => 'captionField_' . $mainGalleryImage->id,
//																					  'maxlength'   => 250
//																				 ));
//
?>
<!--				</div>-->
<!--			</div>-->
<!--		--><?php //} ?>
<!--		<div class="imageIcon">-->
<?php //echo CHtml::image(Icon::PLACE_IN_DESCRIPTION_MAIN_IMAGE_POSITION_THUMBNAIL_ICON, ""); ?><!--</div>-->
<!--		<div class="imageInstruction">-->
<!--			Please crop image to the 990 X 391 pixels or approx ration 5:2.-->
<!--		</div>-->
<!--		<br class="clear"/>-->
<!--	</div>-->
<!--</div>-->
<!--Main Gallery Image-->

<!--Gallery Images-->
<div id="galleryImageInfo">
	<h3>Upload Gallery/Article Photos</h3>

	<div class="row" id="selectImageRow">
		<?php $this->widget('CMultiFileUpload', array(
													 'name'        => 'GalleryImage',
													 'id'          => 'GalleryImage',
													 'accept'      => 'jpeg|jpg|gif|png',
													 'max'         => 20,
													 'file'        => CHtml::image("/images/loading.gif", "", array('style' => 'cursor: pointer;cursor: hand;')),
													 'remove'      => '',
													 'duplicate'   => 'Duplicate Image',
													 'denied'      => 'Invalid Image',
													 'htmlOptions' => array(
														 'size'         => 25,
														 'onChange'     => '',
														 'multiple'     => 'multiple',
														 'onFileSelect' => 'function(e, v, m){ alert("onFileSelect - "+v) }',
													 ),
												)
		) ?>
	</div>
	<!--	<div class="imageIcon">-->
	<?php //echo CHtml::image(Icon::PLACE_RIGHT_GALLERY_POSITION_THUMBNAIL_ICON, ""); ?><!--</div>-->
	<div class="imageInstruction">
		Please select maximum 10 images(Maximum size is 15MB to 20MB per image) at a time of browsing
	</div>
	<br class="clear"/>

	<?php
	$images = $model->images;
	if (isset($images) && count($images) > 0) {
		?>
		<div style="float: left;">
			<?php echo CHtml::checkBox('Place[selectAll]', false, array(
																	   'value'   => 0,
																	   'id'      => 'selectAll',
																	   'onClick' => 'toggleChecked(this.checked)'
																  )) ?>
			<label for="selectAll" style="float: none;">Select/Deselect All</</label>
		</div>
		<div style="float: left;margin-left:1000px;">
			<?php echo CHtml::link('Delete Selected', 'javascript:void(0)', array('onClick' => 'deleteSelected(' . $model->id . ')')) ?>
		</div>
		<br class="clear">
	<?php } ?>


	<?php
	if (isset($images) && count($images) > 0) {
		?>
		<div class="row" id="sortableGallery">    <?php
			$imageCount = 0;
			foreach ($images as $image) {
				?>
				<div id="placeImgPreview-<?php echo $image->id ?>" class="placeImgPreview" valign="top">
					<div class="placeImgPreviewTop">
						<?php echo CHtml::image("/images/sys/admin/icons/cross-icon.png", "", array(
																								   'onclick' => 'deleteImage(' . $image->id . ',' . "'GalleryImage'" . ',false)',
																								   'style'   => 'cursor: pointer;cursor: hand;float:right;'
																							  )); ?>
						<?php echo CHtml::checkBox('Place[selectImage]', false, array(
																					 'value' => $image->id,
																					 'id'    => 'selectImageId_' . $image->id,
																					 'class' => 'checkImage',
																					 'style' => 'float:left;'
																				)) ?>
					</div>
					<div><?php echo CHtml::image(Yii::app()->params['imgUrl'] . "/Place/" . $model->id . "/" . $image->smallName, "", array('id' => $image->fullPath . '/' . $image->name)); ?></div>
					<div>
						<?php
						$text = !empty($image->caption) ? $image->caption : 'Click to add Caption';
						echo CHtml::link($text, 'javascript:void(0);', array(
																			'id'      => 'captionText_' . $image->id,
																			'onClick' => 'javascript:addCaption(this);'
																	   ));
						echo CHtml::textArea('caption', $image->caption, array(
																			  'placeHolder' => 'Type caption here...',
																			  'class'       => 'captionField',
																			  'id'          => 'captionField_' . $image->id,
																			  'maxlength'   => 250, 'cols' => 16
																		 ));
						?>
					</div>
				</div>
			<?php } ?>
			<br class="clear">
		</div> <?php
	}
	?>

</div>
<!--Gallery Images-->

</div>

<br class="clear"/>

<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
	if (window.parent.checkImageError('mainViewImage')) {
		$('#place-image-form #mainViewImage').addClass('error');
	}
	if (window.parent.checkImageError('mainGalleryImage')) {
		$('#place-image-form #mainGalleryImage').addClass('error');
	}
	if (window.parent.checkImageError('GalleryImage')) {
		$('#place-image-form #GalleryImage').addClass('error');
	}

	$('.captionField').hide();
	function addCaption(thisEle) {
		$(thisEle).hide();
		$(thisEle).next().show();
	}

	$('.captionField').live("keydown keypress", function (e) {
		var thisId = $(this).attr('id').split("_");
		var id = thisId[1];
		var val = $(this).val();
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) { //Enter keycode
			insertCaption(id, val);
			return false;
		}
	});
	$('.captionField').blur(function () {
		var thisId = $(this).attr('id').split("_");
		var id = thisId[1];
		var val = $(this).val();
		insertCaption(id, val);
		return false;
	});


	function insertCaption(id, captionVal) {

		$.get('<?php echo $this->createUrl('/admin4/place/AddCaption') ?>/id/' + id, { 'captionVal': (captionVal)}, function (data) {
			if (data.result == true) {
				if (captionVal.length == 0) {
					captionVal = 'Click to add Caption';
				}
				$('#captionField_' + id).hide();
				$('#captionText_' + id).text(captionVal);
				$('#captionText_' + id).show();

				window.parent.iframeHeight('uploadImage');
			}
		}, "json");
	}

	function toggleChecked(status) {
		$(".checkImage").each(function () {
			$(this).attr("checked", status);
		})
	}

	function deleteSelected(record) {
		var countChecked = $('input.checkImage:checked').length;
		if (countChecked != 0) {
			if (!confirm("Are you sure you want to delete selected image(s) (It could be used in this description)?")) {
				return false;
			}
			$('input.checkImage:checked').each(function (i) {
				var thisId = $(this).val();
				var multiple = true;
				if (i == (countChecked - 1)) {
					deleteImage(thisId, 'GalleryImage', multiple, true);
				} else {
					deleteImage(thisId, 'GalleryImage', multiple, false);
				}
			})
		} else {
			alert("Please select image(s)");
			return false;
		}
	}

	/*On Change Event of Image selector*/
	var submitImage = function () {
		$('#place-image-form').submit();
		return true;
	}


	var deleteImage = function (id, imageType, multiple, last) {
		var multipleImages = 'no';
		if (!multiple) {
			$msg = '';
			if (imageType == 'GalleryImage') {
				$msg = ' (It could be used in this description )';
			}
			if (!confirm("Are you sure you want to delete this image " + $msg + "?")) {
				return false;
			}
		} else {
			if (last == false) {
				multipleImages = 'yes';
			}
		}

		$.get('<?php echo $this->createUrl('File/delete') ?>/id/' + id + '/fileModel/' + imageType + '/recordModel/Place/multipleImages/' + multipleImages, function (data) {
			if (data.result == true) {
				location.href = '<?= $this->createUrl("/admin4/place/UpdateImages/id/". $model->id) ?>';
			}
		}, "json");
	}

	$("#sortableGallery").sortable({
		revert: true,
		cursor: "move",
		stop: function (event, ui) {
			var updateOrderIds = [];
			$(".placeImgPreview").each(function (i) {
				var thisId = $(this).attr("id").split("-");
				updateOrderIds.push(thisId[1]);
			});
			if (updateOrderIds.length != 0) {
				$.post('/admin4/file/rearrange', { 'updateOrderIds': updateOrderIds, 'recordId': '<?= $model->id ?>', 'recordType': 'Place' }, function (data) {
					if (data == 'error') {
						alert("Error! Order not changed..");
						return false;
					}
				});
			}
		}
	}, "json");

	$('body').on('change', '#GalleryImage, #mainGalleryImage, #mainViewImage', function () {
		$('#place-image-form').submit();
	});
</script>



