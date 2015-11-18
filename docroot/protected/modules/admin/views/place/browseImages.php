<style type="text/css">
	.form #browseImagesRow {
		border: 1px solid #dedede;
	}

	.form #browseImagesRow .browseImgPreview {
		border: 1px solid #999999;
		margin: 3px;
		text-align: center;
		white-space: normal;
		width: 200px;
		word-wrap: break-word;
	}

	.clear {
		clear: both;
	}
</style>
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: janki.chaudhari
 * Date: 02/07/12
 * Time: 14:45
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
														 'id'          => 'browse-image-form',
														 'action'      => Yii::app()->createUrl('/admin4/place/AddToEditor/', array('CKEditorFuncNum' => $CKEditorFuncNum)),
														 'method'      => 'get',
														 'htmlOptions' => '',
													)); ?>
	<?php
	if (isset($browseImages) && (count($browseImages) > 0)) {
		?>
		<h1>Select Your Image</h1>
		<table id="browseImagesRow">
			<div class="row buttons">
				<?php echo CHtml::submitButton('Add to Editor'); ?>
			</div>
			<?php
			$imageCount = 0;
			foreach ($browseImages as $browseImage) {
				if (($imageCount % 4) == 0 || $imageCount == 0) {
					echo '<tr>';
				}
				?>
				<td id="browseImgPreview-<?php echo $browseImage->id ?>" class="browseImgPreview">
					<div><?php echo CHtml::image(Yii::app()->params['imgUrl'] . '/' . $browseImage->recordType . '/' . $browseImage->recordId . "/" . $browseImage->smallName, "", array('id' => $browseImage->fullPath . '/' . $browseImage->name)); ?></div>
					<div>
						<?php echo CHtml::radioButton('chooseImage', false, array(
																				 'value'        => $browseImage->id,
																				 'uncheckValue' => null,
																				 'name'         => 'chooseImage',
																				 'id'           => 'chooseImage' . $browseImage->id
																			)) ?>
					</div>
				</td>
				<?php
				if ((($imageCount + 1) % 4) == 0) {
					echo '</tr>';
				}
				$imageCount++;
			}
			?>
		</table>
		<br class="clear"/>
		<div class="row buttons">
			<?php echo CHtml::submitButton('Add to Editor'); ?>
		</div>
	<?php
	} else {
		echo '<h1>No any Images available, Please upload to choose the Image</h1>';
	}
	?>
	<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
	$('#browse-image-form').submit(function () {
		if ($("input:checked").length == 0) {
			alert("Please Choose any image.");
			return false;
		}

	});
</script>