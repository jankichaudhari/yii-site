<?php
/**
 * @var $this      CController
 * @var $fieldName String
 * @var $model     Address
 * @var $cs        CClientScript
 *
 */
$name = $fieldName;

$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/AddressTools.js');

?>
<input type="hidden" name="<?php echo $fieldName ?>[id]" value="<?php echo $model ? $model->id : 0 ?>" id="<?php echo $fieldName ?>_addressId">

<div id="<?php echo $fieldName ?>_container">

	<div class="control-group" id="<?php echo $fieldName ?>_addressStringContainer" style="<?php echo $model ? '' : 'display: none;'?>">
		<label class="control-label">Address</label>

		<div class="controls">
			<span class="text" id="<?php echo $fieldName ?>_addressStringElement">
				<?php echo $model ? $model->getFullAddressString(' ') : '' ?>
			</span>
			<br>
			<input type="Button" value="Change" class="btn" id="<?php echo $fieldName ?>_changeButton">
			<input type="Button" value="Show on map" class="btn btn-gray" id="<?php echo $fieldName ?>_showOnMapButton">
			<?php if ($model && (Yii::app()->user->is('Production') || Yii::app()->user->is('SuperAdmin'))): ?>
				<?php echo CHtml::link("Edit", ['Address/edit', 'id' => $model->id], ['class' => 'btn btn-red']) ?>
			<?php endif ?>
		</div>
	</div>

	<div class="control-group" id="<?php echo $fieldName ?>_searchAddressContainer" style="<?php echo $model ? 'display:none' : '' ?>">
		<div class="controls force-margin">
			<input type="text" id="<?php echo $fieldName ?>_searchAddressField" placeholder="Search for address">
		</div>
	</div>
	<div class="control-group">
		<div class="flash danger input-medium controls force-margin" style="display:none" id="<?php echo $fieldName ?>_errorContainer"></div>
	</div>
	<div class="control-group <?php echo $fieldName ?>_manualAddContainer" style="display:none">
		<div class="controls force-margin">
			<input type="text" placeholder="Line 1" name="line1" class="<?php echo $fieldName ?>_lineField">
		</div>
	</div>
	<div class="control-group <?php echo $fieldName ?>_manualAddContainer" style="display:none">
		<div class="controls force-margin">
			<input type="text" placeholder="Line 2" name="line2" class="<?php echo $fieldName ?>_lineField">
		</div>
	</div>
	<div class="control-group <?php echo $fieldName ?>_manualAddContainer" style="display:none">
		<div class="controls force-margin">
			<input type="text" placeholder="Line 3" name="line3" class="<?php echo $fieldName ?>_lineField">
		</div>
	</div>
	<div class="control-group <?php echo $fieldName ?>_manualAddContainer" style="display:none">
		<div class="controls force-margin">
			<input type="text" placeholder="Line 4" name="line4" class="<?php echo $fieldName ?>_lineField">
		</div>
	</div>
	<div class="control-group <?php echo $fieldName ?>_manualAddContainer" style="display:none">
		<div class="controls force-margin">
			<input type="text" placeholder="Line 5" name="line5" class="<?php echo $fieldName ?>_lineField">
		</div>
	</div>

	<div id="<?php echo $fieldName ?>_lookupContainer" style="display:none;">
		<div class="control-group">
			<div class="controls force-margin">
				<input placeholder="Postcode" type="text" id="<?php echo $fieldName ?>_postcodeInputField">
				<input type="button" class="btn" value="Lookup" id="<?php echo $fieldName ?>_lookupButton">
				<input type="button" class="btn btn-red" value="Add Manually" id="<?php echo $fieldName ?>_addManuallyButton">
				<img src="/images/sys/loading.gif" id="<?php echo $fieldName ?>_lookupIcon" style="display:none;">
				<br>
				<select name="" id="<?php echo $fieldName ?>_lookupResultSelect" size="30" class="input-xlarge" style="display: none; margin-top: 7px;">
				</select>
			</div>
		</div>
	</div>

	<div class="control-group <?php echo $fieldName ?>_manualAddContainer" style="display: none">
		<div class="controls force-margin">
			<input type="button" value="Save Address" class="btn btn-green" id="<?php echo $fieldName ?>_saveAddressButton">
		</div>
	</div>
</div>


<script type="text/javascript">AddressTools('<?php echo $fieldName ?>').init();</script>

