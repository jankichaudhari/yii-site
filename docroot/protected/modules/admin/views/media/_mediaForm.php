<?php
/**
 * @var           $this               MediaController
 * @var           $model              Media
 * @var           $floorPlans         Media[]
 * @var           $epc                Media[]
 * @var           $instructionId      int
 * @var AdminForm $form
 */
/** @var $cs CClientScript */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/jquery.Jcrop.min.js');
$cs->registerCssFile('/css/jquery.Jcrop.min.css');
?>
<style type="text/css">
</style>

<div class="row-fluid">


	<div class="span5">

		<?php $form = $this->beginWidget('AdminForm', array(
														   'id'          => 'Media_med_floorplan',
														   'htmlOptions' => ['enctype' => 'multipart/form-data',]
													  )); ?>
		<h4>Floorplan</h4>

		<div class="flash danger"><?php echo $form->errorSummary($model) ?></div>
		<div class="floorPlan-upload-container">
			<div class="control-group">
				<label class="control-label">Floorplan Title</label>

				<div class="controls">
					<?php echo $form->dropDownList($model, 'med_title', Media::getFloorplanTitles(), ['empty' => '']) ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Floorplan Area</label>

				<div class="controls">
					<?php echo $form->textField($model, 'med_dims', ['class' => 'input-xsmall']) ?>m&sup2;
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Floorplan File</label>

				<div class="controls">
					<?php echo CHtml::activeFileField($model, 'floorplan', ['id' => 'media']) ?>
				</div>
			</div>
			<div class="block-buttons force-margin"><?=
				CHtml::submitButton('Upload Floorplan', [
														'name'  => 'floorplanUpload',
														'class' => 'btn'
														]) ?></div>
		</div>
		<?php $this->endWidget() ?>
	</div>
	<div class="span7">
		<div class="media-files sortable" id="Floorplan">
			<?php foreach ($floorPlans as $value): ?>
				<div class="image-box">
					<span class="enlarge-photo" data-id="<?php echo $value->getImageURIPath() ?>"><img
								src="<?= Icon::ZOOM_IN_SYMBOL ?>" alt="Z"/></span>
					<span class="delete-file"
						  data-id="<?php echo $value->med_id ?>"><?php echo Icon::CROSS_SYMBOL ?></span>
					<?php echo CHtml::image($value->getThumbImageURIPath(), '', [
																				'class'   => 'media-file',
																				'data-id' => $value->med_id
																				]) ?>
					<div class="image-actions">
						<div class="action">
							<?php
							echo CHtml::dropDownList('med_title_' . $value->med_id, $value->med_title, Media::getFloorplanTitles(), array(
																																		 'data-id' => $value->med_id,
																																		 'class'   => 'titleChanger input-block'
																																	));
							?>
						</div>
						<div class="action">
							<?php echo $form->textField($value, 'med_dims', [
																			'class'       => 'areaChanger input-block',
																			'data-id'     => $value->med_id,
																			'placeholder' => 'Area in m²'
																			]) ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

</div>


<div class="row-fluid">
	<div class="span5">
		<?php $form = $this->beginWidget('AdminForm', array(
														   'id'          => 'Media_med_epc',
														   'htmlOptions' => ['enctype' => 'multipart/form-data',]
													  )); ?>
		<h4>EPC</h4>

		<div class="epc-upload-container">
			<div class="control-group">
				<label class="control-label">EPC File</label>

				<div class="controls">
					<?php echo CHtml::activeFileField($model, 'epc', ['id' => 'media']) ?>
				</div>
			</div>
			<div class="block-buttons force-margin"><?=
				CHtml::submitButton('Upload EPC', [
												  'name'  => 'epcUpload',
												  'class' => 'btn'
												  ]) ?></div>
		</div>
		<?php $this->endWidget() ?>
	</div>

	<div class="span7">
		<div class="media-files sortable" id="EPC">
			<?php foreach ($epc as $value): ?>
				<div class="image-box">
					<span class="delete-file"
						  data-id="<?php echo $value->med_id ?>"><?php echo Icon::CROSS_SYMBOL ?></span>
					<img src="<?php echo $value->getThumbImageURIPath() ?>" alt="" class="media-file"
						 data-id="<?php echo $value->med_id ?>">
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<script type="text/javascript">
	(function () {
		$(".sortable").sortable({
			stop: function (event, ui) {
				var thisId = $(this).attr('id');
				var images = $('#' + thisId + ' img');

				var newSort = [];
				$('#' + thisId + ' .media-file').each(function () {
					newSort.push($(this).data('id'));
				});
				$.post('/admin4/media/rearrange', {'newSort': newSort, 'instructionId': '<?php echo $instructionId ?>', 'type': thisId }, function (data) {
						}
				);


			}
		});

		$('.delete-file').on('click', function () {
			if (confirm('Are you sure you want to delete this image?')) {
				var self = this;
				$.post('<?php echo $this->createUrl('delete') ?>', {'id': $(this).data('id')}, function (result) {
					self.parentNode.parentNode.removeChild(self.parentNode);
				})
			}

		})
		$('.titleChanger').on('change', function () {
			$.post('<?php echo $this->createUrl('changeTitle') ?>', {'id': $(this).data('id'), 'value': $(this).val()}, function (data) {
			});
		})
		$('.areaChanger').on('blur', function () {
			$.post('<?php echo $this->createUrl('changeArea') ?>', {'id': $(this).data('id'), 'value': $(this).val()}, function (data) {
			});
		});
	})();

	$('.enlarge-photo').on('click', function () {
		var url = $(this).data('id');
		var w = $(window).width();
		var h = $(window).height();
		var thisWindow = window.open(url, "Original-Image", "menubar=1,resizable=1,width=1000,height=" + h);
		if (window.focus) {
			thisWindow.focus()
		}
	});
</script>
