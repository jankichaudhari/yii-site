<?php
/**
 * @var $value Deal
 * @var $model Deal
 * @var $form  AdminFilterForm
 * @var $this  CController
 */

$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js');
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/adminUtilHead.js', CClientScript::POS_HEAD);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/adminUtil.js', CClientScript::POS_END);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/instruction.js', CClientScript::POS_HEAD);
$clientScript->registerCssFile(Yii::app()->baseUrl . '/css/instruction.css');
$tabWidgetId = 'instruction-production-' . $model->dea_id;
$saveButton = CHtml::submitButton('Save', array(
		'class'   => 'btn',
		'onclick' => "stopPopupPreview('#instruction-form')"
));
?>

<?php $form = $this->beginWidget('AdminForm', array(
		'id'                   => 'instruction-form',
		'enableAjaxValidation' => false,
		'focus'                => [$model, 'dea_ptype'],
));

?>

<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">
				<a href="/admin4/instruction/summary/id/<?= $model->dea_id ?>" style="color: #555 ;">Update
																									 Instruction</a>» Production
			</div>
			<div class="content">
				<?php if (!empty($model->property->addressId)) { ?>
					<?=
					$model->property->address->getFullAddressString(', ') . " (" . $model->dea_type . ")"; ?>
				<?php } ?>
			</div>
			<?php if (($model->hasErrors()) || (Yii::app()->user->hasFlash('error'))): ?>
				<div class="content">
					<div class="flash danger"><?php echo $form->errorSummary($model) ?></div>
					<div class="flash danger"><?php echo Yii::app()->user->getFlash('error'); ?></div>
				</div>
			<?php endif;
			if (Yii::app()->user->hasFlash('success')) : ?>
				<div class="content">
					<div class="flash success"><?php echo Yii::app()->user->getFlash('success'); ?></div>
				</div>
			<?php endif; ?>
			<div class="block-buttons">
				<?php echo $saveButton ?>
				<input type="submit" class="btn" value="Preview"
					   onclick="popupPreview('#instruction-form','/details/<?= $model->dea_id ?>')">
				<?php echo CHtml::link('Summary', [
						'instruction/summary',
						'id' => $model->dea_id
				], ['class' => 'btn btn-gray']) ?>
				<?php echo CHtml::link('Property', [
						'property/update',
						'id' => $model->dea_prop
				], ['class' => 'btn btn-gray']) ?>
				<?php echo CHtml::link('PDF', '#', [
						'class'   => 'btn btn-gray',
						'onclick' => "popupWindow('/property/Pdf/" . $model->dea_id . "',700,950)"
				]) ?>
				<input type="button" class="btn" value="Generate Preview Link"
					   onclick="generatePreviewLink('<?= $model->dea_id ?>')">
				<input type="button" class="btn" value="Disable Preview Link"
					   onclick="disablePreviewLink('<?= $model->dea_id ?>')">
			</div>
		</fieldset>
	</div>
</div>
<div class="row-fluid">
<div class="span12">
<?php $tabbedView = $this->beginWidget('application.components.TabbedLayout.TabbedLayout', array(
		'id'        => $tabWidgetId,
		'activeTab' => 'description'
)); ?>
<?php $tabbedView->beginTab("Description", array('id' => 'description')) ?>
<div class="content">

	<?php echo $form->beginControlGroup($model, 'title'); ?>
	<?php echo $form->controlLabel($model, 'title'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'title', ['class' => 'input-double']); ?>
		<?php if (date("Y-m-d") < '2013-12-10'): ?>
			<span class="hint">New! Make sure it is correct. it will be displayed on website, mailshot and feeds.</span>
		<?php endif ?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'dea_strapline'); ?>
	<?php echo $form->controlLabel($model, 'dea_strapline'); ?>
	<div class="controls">
		<?php echo $form->textArea($model, 'dea_strapline', ['class' => 'input-double', 'style' => 'height:64px;']); ?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<?php echo $form->beginControlGroup($model, 'dea_description'); ?>
	<?php echo $form->controlLabel($model, 'dea_description'); ?>
	<div class="controls">
		<?php echo $form->textArea($model, 'dea_description', ['style' => 'height:395px;width:750px;']); ?>
	</div>
	<?php echo $form->endControlGroup(); ?>
</div>
<div class="block-buttons force-margin">
	<?php echo $saveButton ?>
</div>
<?php $tabbedView->endTab(); ?>
<?php $tabbedView->beginTab("Features", array('id' => 'features')); ?>
<div class="content">
	<?php
	/** @var $features */
	$feature = new Feature();
	$featureTypes = Util::enumItem($feature, 'fea_type');
	foreach ($featureTypes as $featureType) {
		echo '<div style="display:table;">';
		if ($featureType == 'Custom') {
			$features = Feature::model()->findAllByAttributes(['fea_type' => $featureType]);
			echo '<p><b>Custom Feature(s)</b></p>';
			echo '<div id="customFeatures">';
			if ($features) {
				foreach ($features as $feature) {
					if ($model->dealBelongsToFeature($feature->fea_id)) {
						echo '<div class="feature-box" id="customFeature-' . $feature->fea_id . '">';
						echo CHtml::hiddenField("Deal[feature][{$feature->fea_id}]", 1);
						echo CHtml::link(
								  '<img src="/images/sys/admin/icons/cross.gif" alt="delete">',
								  '',
								  [
										  'class'   => 'delete-customFeature',
										  'onclick' => "deleteCustomFeature('" . $feature->fea_id . "')",
								  ]
						);
						echo CHtml::label($feature->fea_title, '', array('style' => 'font-size:10px;'));
						echo '</div>';
					}
				}
			}
			echo '</div>';
		} else {
			if ($featureType != 'Lettings') {
				$features = Feature::model()->findAllByAttributes(['fea_type' => $featureType]);
				if ($features) {
					echo '<p><b>' . $featureType . '</b></p>';
					$featureCnt = 0;
					foreach ($features as $feature) {
						$featureCnt++;
						echo '<div class="feature-box">';
						echo CHtml::checkBox(
								  'Deal[feature][' . $feature->fea_id . ']',
								  $model->dealBelongsToFeature($feature->fea_id),
								  array()
						);
						echo CHtml::label($feature->fea_title, 'Deal_feature_' . $feature->fea_id, array('style' => 'font-size:10px;'));
						echo '</div>';
					}
				}
			}
		}
		echo '</div>';
	}
	?>
	<div class="control-group">
		<p><b>Add Custom Feature(s)</b></p>

		<p>
			<input type="text" name="textCustomFeature" id="textCustomFeature" value="">
			<input type="button" class="btn btn-green add-feature-button" value="Add" id="btnCustomFeature">
		</p>
	</div>
</div>
<div class="block-buttons">
	<?php echo $saveButton ?>
</div>
<?php $tabbedView->endTab(); ?>
<?php $tabbedView->beginTab("Manage Photos", array('id' => 'managePhotos')) ?>
<div class="content">
	<iframe src="<?php echo $this->createUrl("Media/PhotoForm", ['instructionId' => $model->dea_id]) ?>"
			id="uploadPhoto" name="uploadPhoto" width="100%" frameborder="0" scrolling="no"></iframe>
</div>
<?php $tabbedView->endTab(); ?>
<?php $tabbedView->beginTab("Floorplan & EPC", array('id' => 'floorplanEPC')) ?>
<div class="content">
	<iframe src="<?php echo $this->createUrl("Media/MediaForm", array('instructionId' => $model->dea_id)) ?>"
			id="uploadMedia" name="uploadMedia" width="100%" frameborder="0" scrolling="no"></iframe>
</div>
<?php $tabbedView->endTab(); ?>
<?php $tabbedView->beginTab("Manage Video", array('id' => 'manageVideo')) ?>
<div class="content">
	<div class="control-group">
		<label class="control-label" for="Deal_video_videoId">Video ID</label>

		<div class="controls">
			<?php $videoVal = ((!isset($model->video[0]->videoId)) || (empty($model->video[0]->videoId))) ? '' : $model->video[0]->videoId; ?>
			<?= CHtml::textField('Deal[video][videoId]', $videoVal, ['id' => 'Deal_video_videoId']) ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="Deal_video_featureVideo">Featured Video</label>

		<div class="controls">
			<?php $featureVideoVal = isset($model->video[0]->featuredVideo) ? $model->video[0]->featuredVideo : 0 ?>
			<?= CHtml::checkBox('Deal[video][featureVideo]', $featureVideoVal, ['id' => 'Deal_video_featureVideo']) ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="Deal_video_displayOnSite">Display on home page</label>

		<div class="controls">
			<?php $displayOnSiteVal = isset($model->video[0]->displayOnSite) ? $model->video[0]->displayOnSite : 0 ?>
			<?= CHtml::checkBox('Deal[video][displayOnSite]', $displayOnSiteVal, ['id' => 'Deal_video_displayOnSite']) ?>
		</div>
	</div>
	<div class="block-buttons force-margin">
		<?php echo $saveButton ?>
	</div>
</div>
<?php $tabbedView->endTab(); ?>
<?php $tabbedView->beginTab("Mailshots", ['id' => 'mailshots']) ?>
<div class="content">
	<!--	--><?php //echo CHtml::link('Custom Mailshot', ["Instruction/CustomMailshot", 'id' => $model->dea_id], ['class' => 'btn btn-green']); ?>
	<?php echo CHtml::link('Create Mailshot', ['client/detailSearch', 'instructionId' => $model->dea_id], ['class' => 'btn btn-green']); ?>

	<div class="grid-view">
		<table class="small-table">
			<tr>
				<th>Date</th>
				<th>Type</th>
				<th>Total</th>
				<th>Queued</th>
				<th>Sent</th>
				<th>Opened</th>
				<th>Property Hits</th>
				<th>Unique Hits</th>
				<th>User</th>
				<th>Details</th>
			</tr>
			<?php foreach ($model->mandrillMailshots as $key => $mailshot): ?>
				<tr>
					<td><?php echo Date::formatDate('d/m/Y H:i', $mailshot->created) ?></td>
					<td><?php echo $mailshot->type ?></td>
<!--					<td>--><?php //echo $mailshot->emailCount ?><!--</td>-->
<!--					<td>--><?php //echo $mailshot->queuedEmailCount ?><!--</td>-->
<!--					<td>--><?php //echo $mailshot->sentEmailCount ?><!--</td>-->
<!--					<td>--><?php //echo $mailshot->openEmailCount ?><!--</td>-->
<!--					<td>--><?php //echo $mailshot->hitCount ?><!--</td>-->
<!--					<td>--><?php //echo $mailshot->uniqueHitCount ?><!--</td>-->
					<td><?php echo $mailshot->creator->getFullName() ?></td>
					<td><?php echo CHtml::link(CHtml::image(Icon::EDIT_ICON), '#') ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<span class="old-mailshot-toggler" style="border-bottom: 1px dashed blue; color: blue">Old mailshot statictics (click to show)</span>

	<div class="old-mailshots" style="display: none; padding-top: 15px;">
		<?php if ($model->isPublic()) {
			echo CHtml::link('Create Mailshot (Old)', Yii::app()->params['globalUrlOld'] . "mailshot.php?dea_id=" . $model->dea_id, ['class' => 'btn btn-green']);
		} ?>
		<div class="grid-view">
			<table class="small-table">
				<tr>
					<th>Date</th>
					<th>Type</th>
					<th>Status</th>
					<th>Sent</th>
					<th>Hits</th>
					<th>User</th>
				</tr>
				<?php foreach ($model->mailshots as $mailshot) {
					echo '<tr>';
					echo '<td>' . date('d/m/y', strtotime($mailshot->mai_date)) . '</td>';
					echo '<td>' . $mailshot->mai_type . '</td>';
					echo '<td>' . $mailshot->mai_status . '</td>';
					echo '<td>' . $mailshot->mai_count . '</td>';
					$hits = '';
					if ($mailshot->hits) {
						$hits = count($mailshot->hits);
					}
					echo '<td>' . $hits . '</td>';
					echo '<td>' . $mailshot->user->use_fname . " " . $mailshot->user->use_sname . '</td>';
					echo '</tr>';
				}
				?>
			</table>
		</div>
	</div>
</div>
</div>
<?php $tabbedView->endTab(); ?>
<!-- Mailshots -->

<?php $this->endWidget() ?>

</div>

<?php $this->endWidget(); ?>
<script id="customFeature-span-template" type="text/customFeature-span-template">
	<div class="feature-box" id="customFeature-{fea_id}">
		<input type="hidden" value="1" name="Deal[feature][{fea_id}]">
		<a class="delete-customFeature" onclick="deleteCustomFeature('{fea_id}')"><img
				src="/images/sys/admin/icons/cross.gif" alt="delete"></a>
		<label style="font-size:10px;">{fea_title}</label>
	</div>
</script>
<script type="text/javascript">
	$(".success").animate({opacity : 1.0}, 1000).fadeOut("slow");
	$(".datePicker").datepicker();

	$('#Deal_dea_ptype').on('change', function ()
	{
		var thisVal = $(this).val();
		$.get('/admin4/instruction/propertySubTypes/propertyType/' + thisVal, function (data)
		{
//			console.log(data);
			if (data.length == 0 || data === "null" || data === null || data === "" || typeof data === "undefined") {
				return false;
			}
			$('#Deal_dea_psubtype').html(data);
		});
	});

	$('.goToElement').on('click', function ()
	{
		var jump = $(this).data('id');
		var new_position = $('#' + jump).offset();
		window.scrollTo(new_position.left, (new_position.top - 100));
		return false;
	});

	CKEDITOR.replace('Deal_dea_description', {
		width          : $('#Deal_dea_description').width(),
		height         : $('#Deal_dea_description').height(),
		resize_enabled : true
	});

	/*Custom Feature*/
	var count = 0;
	$('#btnCustomFeature').on('click', function ()
	{
		addFeature();
	});
	$('#textCustomFeature').on('keypress', function (event)
	{
		if (event.which == 13) {
			event.preventDefault();
			addFeature();
		}
	});

	var addFeature = function ()
	{
		var newCustomFeature = $('#textCustomFeature').val();
		if (newCustomFeature.length != 0) {

			$.getJSON('/admin4/instruction/saveCustomFeature/feaTitleText/' + newCustomFeature + '/instructionId/<?= $model->dea_id ?>/format/JSON', function (data)
			{
				var tpl = $('#customFeature-span-template').html();

				for (key in data) {
					var regexp = new RegExp('{' + key + '}', 'gi');
					tpl = tpl.replace(regexp, data[key]);
				}
				$('#customFeatures').append(tpl);
				$('#textCustomFeature').val('');
			});
		}
	};
	function deleteCustomFeature(id)
	{
		if (confirm('Are you sure you want to delete this custom feature?')) {
			$.getJSON('/admin4/instruction/deleteCustomFeature/featureId/' + id + '/instructionId/<?= $model->dea_id ?>/format/JSON', function (data)
			{
				$('#customFeature-' + id).remove();
			});
		}
	}
	/*Custom Feature*/

	(function ()
	{
		$('.old-mailshot-toggler').on('click', function ()
		{
			$('.old-mailshots').toggle();
		})
	})();
</script>
