<?php
/**
 * @var $model OuterLink[ ]
 * @var $form  AdminForm
 * @var $photo OuterLinkImage[ ]
 */
?>

<?php $form = $this->beginWidget('AdminForm', array(
											 'htmlOptions' => array(
												 'enctype' => 'multipart/form-data',
											 )
										));
?>
<div class="block-buttons">
	<?php echo CHtml::submitButton('Upload', ['name' => 'uploadOuterLinkImage', 'class' => 'btn']); ?>
</div>
<div class="control-group">
	<label class="bold">Upload Photo</label>

	<div class="controls">
		<?php $this->widget('CMultiFileUpload', array(
													 'name'        => 'OuterLinkImage',
													 'accept'      => 'jpg|png:gif',
													 'max'         => 1,
													 'remove'      => '',
													 'denied'      => 'Invalid Image',
													 'htmlOptions' => array(
														 'size'     => 25,
														 'onChange' => ''
													 ),
												));
		?>
	</div>
	<div class="controls">
		<?php if ($model->image) { ?>
			<div id="imageContainer-<?php echo $model->image->id ?>"
				 style="float:left; border:1px solid #dedede; margin: 3px 3px; ">
				<?php echo CHtml::image($model->image->getUrlToFile(), $model->title, ['width' => '230']); ?>
				<span style="float: right">
					<?php echo CHtml::image("/images/sys/admin/icons/cross-icon.png", "", array('onclick' => 'deleteImage(' . $model->image->id . ')')); ?>
					</span>
			</div>
		<?php } ?>
	</div>
</div>

<?php $this->endWidget();
?>
<script type="text/javascript">
	var deleteImage = function (id) {
		var multipleImage = 'false';
		if (!confirm("Are you sure you want to delete this image?")) {
			return false;
		}
		$.get('<?php echo $this->createUrl('File/Delete') ?>/id/' + id + '/fileModel/OuterLinkImage/recordModel/OuterLink/multipleImages/' + multipleImage, function (data) {
//			console.log(data);
			if (data.result == true) {
				$("#imageContainer-" + id).hide();
			}
		}, "json");
	};
</script>