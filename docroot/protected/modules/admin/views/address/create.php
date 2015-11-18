<?php
/**
 * @var           $this              AddressController
 * @var           $model             Address
 * @var           $existingAddresses Address[]
 * @var           $form              AdminForm
 * @name          $name              String
 */
/** @var $cs CClientScript */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/AddressTools.js', CClientScript::POS_HEAD);

?>
<div class="container-fluid">
	<?php $form = $this->beginWidget('AdminForm', array()); ?>
    <div class="row-fluid">
        <div class="span12">
            <fieldset>
                <div class="block-header">Create new address</div>
                <div class="clearfix"></div>
				<?php if (isset($existingAddresses) && $existingAddresses): ?>
                <div class="flash warning">
                    There are addresses that may be the same:
                    <ul>
						<?php foreach ($existingAddresses as $key => $value): ?>
                        <li>
                            <img src="/images/sys/admin/icons/Use-Icon.png" alt="Use" style="cursor:pointer" onclick="AddressTools('<?php echo $name ?>').selectAddress(<?php echo $value->id ?>)"><?php echo $value->toString(); ?>
                        </li>
						<?php endforeach; ?>
                    </ul>
                    <input type="submit" value="Save anyway" name="force-save" class="btn btn-warning">
                </div>
				<?php endif ?>
                <div class="control-group">
                    <label class="control-label" for="<?php echo $name ?>_line1">Address</label>

                    <div class="controls">
						<?php echo CHtml::textField('' . $name . '[line1]', $model->line1, ['placeholder' => 'Line 1']) ?>
<!--                        <input id="--><?php //echo $name ?><!--SelectCoordsButton" type="button" class="btn btn-primary btn-white input-xsmall" value="View on map">-->
                        <br>
						<?php echo CHtml::textField('' . $name . '[line2]', $model->line2, ['placeholder' => 'Line 2']) ?><br>
						<?php echo CHtml::textField('' . $name . '[line3]', $model->line3, ['placeholder' => 'Line 3']) ?><br>
						<?php echo CHtml::textField('' . $name . '[line4]', $model->line4, ['placeholder' => 'Line 4']) ?><br>
						<?php echo CHtml::textField('' . $name . '[line5]', $model->line5, ['placeholder' => 'Line 5']) ?><br>
						<?php echo CHtml::textField('' . $name . '[postcode]', $model->postcode, ['placeholder' => 'Postcode', 'style' => 'text-transform:uppercase;']) ?>
                        <input id="<?php echo $name ?>LookupButton" type="button" class="btn btn-primary btn-white input-xsmall" value="Lookup">
                        <img src="/images/sys/loading.gif" id="<?php echo $name ?>lookupIcon" style="display:none;">
                        <br>

                        <div class="flash danger input-xlarge" style="display:none" id="<?php echo $name ?>LookupErrors">
                        </div>
                        <select name="" id="<?php echo $name ?>LookupResults" size="30" class="input-xlarge" style="display: none">
                        </select>
						<?php echo CHtml::hiddenField($name . '[lat]', $model->lat) ?>
						<?php echo CHtml::hiddenField($name . '[lng]', $model->lng) ?>
						<?php echo CHtml::hiddenField($name . '[postcodeAnywhereID]', $model->postcodeAnywhereID) ?>
                    </div>
                </div>
                <div class="control-group shaded ">
                    <div class="controls form-buttons force-margin">
                        <input type="submit" class="btn" value="<?php echo $model->isNewRecord ? 'Create' : 'Save' ?>">
                        <input type="submit" class="btn btn-warning" name="close" value="<?php echo $model->isNewRecord ? 'Create & Close' : 'Save & Close' ?>">
                        <input type="submit" class="btn btn-danger" name="close" value="Close" onclick="window.close()">
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
	<?php $this->endWidget() ?>
</div>
<script type="text/javascript">

    var name = '<?php echo $name ?>';
    AddressTools(name).init();
	<?php if (!$model->isNewRecord): ?>
    AddressTools(name).selectAddress(<?php echo $model->id ?>);
		<?php endif ?>
	<?php if (isset($_GET['close'])) : ?>
    window.close();
		<?php endif; ?>
</script>