<?php
/**
 * @var    $this      ClientController
 * @var    $model     Client
 * @var    $form      AdminForm
 */
$features = Feature::model()->notCustom()->findAll();

?>
<div class="content">
	<?php if ($features) : ?>
		<div class="control-group">
			<label class="control-label">
				Features
				<input type="checkbox" id="matchingFeatures">
			</label>

			<div class="controls">
				<table>
					<tr>
						<?php foreach ($features as $key => $feature) : ?>
							<td style="vertical-align: top">
								<?php
								echo CHtml::checkBox(
										  'Client[feature][' . $feature->fea_id . ']',
										  $model->clientBelongsToFeature($feature->fea_id),
										  ['class' => 'feature-box', 'value' => $feature->fea_id]
								);
								echo CHtml::label($feature->fea_title, 'Client_feature_' . $feature->fea_id, ['class' => 'client-checkbox']);
								?>
							</td>
							<?php echo (($key + 1) % 7 == 0) ? "</tr>\n<tr>\n" : ''; ?>
						<?php endforeach; ?>
					</tr>
				</table>
			</div>
		</div>
	<?php endif; ?>

</div>
<script type="text/javascript">
	(function ()
	{
		$('#matchingFeatures').on('change', function ()
		{
			$('.feature-box').attr('checked', $(this).is(':checked'));
		});
	})();

</script>