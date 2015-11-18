<?php
/**
 * @var           $this          MediaController
 * @var           $model         Media
 * @var           $files         Media[]
 * @var           $instructionId int
 * @var AdminForm $form
 */
/** @var $cs CClientScript */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/jquery.Jcrop.min.js');
$cs->registerCssFile('/css/jquery.Jcrop.min.css');
?>
<style type="text/css">
	.media-files {
		overflow: auto;
		min-width: 264px;
	}

	.previewMedia {
		border: 1px solid #B0B0B0;
		background: white;
		position: relative;
		float: left;
		height: 605px;
		width: 605px;
		text-align: center;
		overflow: auto;
	}

	.photo-container {
		width: 605px;
		height: 152px;
		padding: 10px 0;
		margin: 10px 0;
		text-align: left;
		border: 0;
	}

	.photo-upload-container {
		float: left;
		margin-left: 21px;
	}

	.thumbnail-container {
		float: left;
		width: 146px;
		height: 146px;
		overflow: hidden;
		border: 1px solid #B0B0B0;
		background: white;
		text-align: center;
	}

</style>

<div class="row-fluid" id="photo-wrapper">
	<div class="span6" style="width: 634px;" id="photo-uploader">
		<?php $form = $this->beginWidget('AdminForm', array(
														   'htmlOptions' => ['enctype' => 'multipart/form-data',]
													  )); ?>
		<div class="flash danger"><?php echo $form->errorSummary($model) ?></div>
		<input type="hidden" id="cropX" name="cropX">
		<input type="hidden" id="cropY" name="cropY">
		<input type="hidden" id="cropWidth" name="cropWidth">
		<input type="hidden" id="imageWidth" name="imageWidth">
		<input type="hidden" id="imageHeight" name="imageHeight">

		<div class="photo-container">
			<div class="thumbnail-container">
				<h4 id="thumbnail-text">THUMBNAIL PREVIEW</h4>
				<img src="" id="thumbnail" alt="">
			</div>

			<div class="photo-upload-container">
				<div class="control-group">
					<label class="bold"">Med Title</label>
					<div class="controls">
						<?php echo $form->dropDownList($model, 'med_title', Media::model()->getPhotoTitles()) ?>
					</div>
				</div>
				<div class="control-group">
					<label class="bold">File</label>

					<div class="controls">
						<?php echo CHtml::activeFileField($model, 'file', ['id' => 'media']) ?>
					</div>
				</div>

				<div class="control-group">
					<div class="controls" style="text-align: center;margin-top: 15px;">
						<input type="submit" value="Upload" class="btn">
						<input type="button" value="Cancel Upload" class="btn" id="cancelUpload">
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>

		<div id="previewMedia" class="previewMedia">
			<h1 id="preview-text">FILE PREVIEW</h1>
			<img id="preview-image">
		</div>
		<?php $this->endWidget() ?>
	</div>

	<div id="media-files-wrapper" style="float: left;min-width: 110px;">
		<div class="media-files sortable" id="media-files">
			<?php foreach ($files as $key => $value): ?>
				<div class="image-box photo">
					<span class="delete-file"
						  data-id="<?php echo $value->med_id ?>"><?php echo Icon::CROSS_SYMBOL ?></span>
					<img src="<?php echo $value->getThumbImageURIPath() ?>" class="media-file"
						 data-id="<?php echo $value->med_id ?>">

					<div class="image-actions">
						<div class="action">
							<?php echo CHtml::dropDownList('med_title_' . $value->med_id, $value->med_title, Media::model()->getPhotoTitles(), array(
																																					'data-id' => $value->med_id,
																																					'class'   => 'input-block titleChanger'
																																			   )) ?></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var jcrop_api;
	(function () {
		$(".sortable").sortable({
			stop: function (event, ui) {
				var images = $('#media-files img');

				var newSort = [];
				$('#media-files .media-file').each(function () {
					newSort.push($(this).data('id'));
				});
				$.post('/admin4/media/rearrange', {'newSort': newSort, 'instructionId': '<?php echo $instructionId ?>', 'type': 'Photograph' }, function (data) {
						}
				);


			}
		});

		$('.delete-file').on('click', function () {
			if (confirm('Are you sure you want to delete this image?')) {
				var self = this;
				$.post('/admin4/media/delete/', {'id': $(this).data('id')}, function (result) {
					self.parentNode.parentNode.removeChild(self.parentNode);
				})
			}

		})

		$('.titleChanger').on('change', function () {
			$.post('/admin4/media/changeTitle/', {'id': $(this).data('id'), 'value': $(this).val()}, function (data) {

			});
		})
	})();

	var preview = function () {
		if (this.files && this.files[0]) {
			if (jcrop_api) {
				jcrop_api.destroy();
			}

			destroySizeCss();

			var reader = new FileReader();

			reader.onload = function (e) {
				$('#preview-image').attr('src', e.target.result);
				$('#thumbnail').attr('src', e.target.result);
				$('#thumbnail-text').hide();
				$('#preview-text').hide();
			}
			reader.readAsDataURL(this.files[0]);
		}
	}

	$('#media').on('change', preview);

	function showThumbnail(coords) {
		document.getElementById("cropX").value = coords.x;
		document.getElementById("cropY").value = coords.y;
		document.getElementById("cropWidth").value = coords.w;

		var rx = 146 / coords.w;
		var ry = 146 / coords.h;
		var w = Math.round(rx * $('#preview-image').width()) + 'px';
		var h = Math.round(ry * $('#preview-image').height()) + 'px';

		$('#thumbnail').css({
			width: w,
			height: h,
			marginLeft: '-' + Math.round(rx * coords.x) + 'px',
			marginTop: '-' + Math.round(ry * coords.y) + 'px'
		});
	}

	var destroySizeCss = function () {
		$('#preview-image').attr('src', '');
		$('#preview-image').attr('width', '');
		$('#preview-image').attr('height', '');
		$('#preview-image').css('width', '');
		$('#preview-image').css('height', '');
	}

	$('#preview-image').on('load', function () {
		var x = this.width;
		var y = this.height;
		var w = 600;
		var h = 600;
		if (x < y) {
			if (w > x) {
				w = x;
			}
			var setSel = [0, ((y - x) / 2), x, x];
		} else {
			if (h > y) {
				h = y;
			}
			var setSel = [((x - y) / 2), 0, y, y];
		}
		document.getElementById("imageWidth").value = $('#preview-image').width();
		document.getElementById("imageHeight").value = $('#preview-image').height();
		$('#preview-image').Jcrop({
			aspectRatio: 1,
			onChange: showThumbnail,
			onSelect: showThumbnail,
			setSelect: setSel,
			boxWidth: w,
			boxHeight: h

		}, function () {
			jcrop_api = this;
		});
	});

	$('#cancelUpload').on('click', function () {
		if (jcrop_api) {
			jcrop_api.destroy();
		}
		destroySizeCss();
		$("#media").val('');
		$('#thumbnail').attr('src', '');
		$("#cropX").val('');
		$("#cropY").val('');
		$("#cropWidth").val('');
		$('#thumbnail-text').show();
		$('#preview-text').show();
		return false;
	});

	$(window).resize(function () {
		var wrapperWidth = $("#photo-wrapper").width();
		var uploaderWidth = $("#photo-uploader").width();
		var w = wrapperWidth - uploaderWidth;
		$("#media-files-wrapper").width(w);
	});

</script>
