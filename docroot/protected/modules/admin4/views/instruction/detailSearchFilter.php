<?php
/**
 * @var $this  InstructionController
 * @var $model Deal
 * @var $form  AdminFilterForm
 *
 */
$types = PropertyType::model()->getTypes();
$subtypes = array();
foreach ($types as $key => $value) {
	$subtypes[$value->pty_id] = PropertyType::model()->getTypes($value->pty_id);
}

$offices = Office::model()->enabledClientMatching()->findAll();
$matchingPostcodes = [];
foreach ($offices as $value) {
	$matchingPostcodes[$value->id] = LinkOfficeToPostcode::model()->getPostcodeList($value->id);
}
?>
<div class="content">
	<div class="control-group">
		<label class="control-label">Search</label>

		<div class="controls">
			<?php echo $form->textField($model, 'searchString', array('size' => 30, 'class' => 'input-xlarge')) ?>
			<span class="hint">Any part of address or owners name</span>
		</div>
	</div>
<?php echo $form->beginControlGroup($model, 'minPrice'); ?>
<?php echo $form->controlLabel($model, 'minPrice'); ?>
	<div class="controls">
		<?php echo $form->dropDownList($model, 'minPrice', Util::getPropertyPrices("minimum"), ['class' => 'input-xsmall', 'empty' => 'Min']) ?>
		<?php echo $form->dropDownList($model, 'maxPrice', Util::getPropertyPrices("maximum"), ['class' => 'input-xsmall', 'empty' => 'Max']) ?>
	</div>
<?php echo $form->endControlGroup(); ?>
<?php echo $form->beginControlGroup($model, 'minBedrooms'); ?>
<?php echo $form->controlLabel($model, 'minBedrooms'); ?>
	<div class="controls">
		<?php echo $form->textField($model, 'minBedrooms', ['class' => 'input-xxsmall', 'placeholder' => 'Min']) ?>
		<?php echo $form->textField($model, 'maxBedrooms', ['class' => 'input-xxsmall', 'placeholder' => 'Max']) ?>
	</div>
<?php echo $form->endControlGroup(); ?>

	<div class="control-group">
		<label class="control-label">Property Types :</label>

		<div class="controls"></div>
	</div>
	<div class="controls">
		<?php foreach ($types as $value) : ?>
			<div class="control-group">
				<label class="control-label">
					<?php echo $value->pty_title ?>
					<?php echo CHtml::checkBox("Deal[dea_ptype]" . $value->pty_id, in_array($value->pty_id, (array)$model->dea_ptype), [
							'class' => 'propertyPreferenceToggler',
							'id'    => 'sales_' . $value->pty_id
					]) ?>
				</label>

				<div class="controls">
					<table>
						<tr>
							<?php foreach ($subtypes[$value->pty_id] as $key => $subtype): ?>
								<td>
									<?php echo
									CHtml::checkBox('Deal[dea_psubtype][' . $subtype->pty_id . ']', in_array($subtype->pty_id, (array)$model->dea_psubtype), array(
											'value'       => $subtype->pty_id,
											'data-parent' => 'sales_' . $value->pty_id,
											'class'       => 'propertyPreference'
									)) ?>

									<?php echo CHtml::label($subtype->pty_title, 'Deal_dea_psubtype_' . $subtype->pty_id, ['class' => 'client-checkbox']); ?>
								</td>
								<?php echo (($key + 1) % 9 == 0) ? "</tr>\n<tr>\n" : '' ?>
							<?php endforeach; ?>
						</tr>
					</table>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<table>
	<tr>
		<?php foreach ($offices as $office) : ?>
			<td valign="top">
				<div class="control-group">
					<label class="control-label">
						<?php echo $office->shortTitle ?>
						<input type="checkbox" class="propertyOffice" data-id="<?php echo $office->id ?>">
					</label>

					<div class="controls">
						<table>
							<tr>
								<?php foreach ($matchingPostcodes[$office->id] as $key => $value): ?>
									<td valign="top">
										<?php echo
										CHtml::checkBox('Deal[matchingPostcodes][' . $value . ']', in_array($value, $model->matchingPostcodes), [
												'value'       => $value,
												'data-parent' => $office->id,
												'class'       => 'matchingPostcode',
										]); ?>
										<?php echo CHtml::label($value, 'Deal_matchingPostcodes_' . $value, ['class' => 'client-checkbox']); ?>

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
	</table><?php
