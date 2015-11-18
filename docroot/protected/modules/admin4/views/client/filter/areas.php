<?php
/**
 * @var    $this      ClientController
 * @var    $model     Client
 * @var    $form      AdminForm
 * @var    $offices   Office[]
 */
$offices = Office::model()->enabledClientMatching()->findAll();
?>
<table>
	<tr>
		<?php foreach ($offices as $office) : ?>
			<td valign="top">
				<div class="control-group">
					<label class="control-label">
						<?php echo $office->shortTitle ?>
						<input type="checkbox" class="matchingPropertyOffice" data-id="<?php echo $office->id ?>">
					</label>

					<div class="controls">
						<table>
							<tr>
								<?php foreach ($office->areas as $key => $value): ?>
									<td valign="top">
										<?php echo
										CHtml::checkBox('Client[searchPostcodes][' . $value->postcode . ']', in_array($value->postcode, $model->searchPostcodes), array(
												'value'       => $value->postcode,
												'data-parent' => $office->id,
												'class'       => 'matchingPostcode',
										)); ?>
										<?php echo CHtml::label($value->postcode, 'Client_searchPostcodes_' . $value->postcode, ['class' => 'client-checkbox']); ?>

									</td>
									<?php echo (($key + 1) % 4 == 0) ? "</tr>\n<tr>\n" : ''; ?>
								<?php endforeach; ?>
							</tr>
						</table>
					</div>
				</div>
			</td>
		<?php endforeach; ?>
	</tr>
</table>
<script type="text/javascript">
	(function ()
	{
		function matchingPostcodeOnChange()
		{
			var parentId = $(this).data('parent');
			$('.matchingPropertyOffice[data-id=' + parentId + ' ]').attr('checked', (
					$('.matchingPostcode[data-parent=' + parentId + ' ]:checked').length === $('.matchingPostcode[data-parent=' + parentId + ' ]').length
					));
		}

		function matchingPropertyOfficeOnChange()
		{
			$('.matchingPostcode[data-parent=' + $(this).data('id') + ' ]').attr('checked', $(this).is(':checked'));
		}

		$('.matchingPostcode').on('change', matchingPostcodeOnChange).each(matchingPostcodeOnChange);
		$('.matchingPropertyOffice').on('change', matchingPropertyOfficeOnChange);

	})();

</script>