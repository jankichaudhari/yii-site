<?php
/**
 * @var    $this      ClientController
 * @var    $model     Client
 * @var    $form      AdminForm
 * @var    $offices   Office[]
 */
?>
<div class="content">
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
									<?php foreach ($matchingPostcodes[$office->id] as $key => $value): ?>
										<td valign="top">
											<?php echo
											CHtml::checkBox('Client[matchingPostcode][' . $value . ']', $model->clientBelongsToPostcode($value), array(
																																					  'value'       => $value,
																																					  'data-parent' => $office->id,
																																					  'class'       => 'matchingPostcode',
																																				 )); ?>
											<?php echo CHtml::label($value, 'Client_matchingPostcode_' . $value, ['class' => 'client-checkbox']); ?>

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
</div>
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