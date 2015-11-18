<?php
/**
 * @var $fieldName String
 * @var $model     Address
 * @var $this      AdminController
 */
$fieldName = isset($fieldName) ? $fieldName : 'Address';
/** @var $cs CClientScript */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/AddressTools.js', CClientScript::POS_HEAD);
$model            = isset($model) && $model ? $model : false;
$noAddressMessage = isset($noAddressMessage) && $noAddressMessage ? $noAddressMessage : 'Address is not selected';
?>

<div>
    <div id="<?php echo $fieldName ?>Box">
		<?php if (!$model) : ?>
        <div class="control-group">
			<div class="controls" style="margin-top: -5px;">
                <span class="text"><?php echo $noAddressMessage ?></span>
            </div>
        </div>
        <div class="control-group">
            <div class="controls force-margin">
                <input type="button" class="btn btn-primary" value="Search" id="<?php echo $fieldName ?>_searchButton">
            </div>
        </div>
        <input type="hidden" name="<?php echo $fieldName ?>[id]" value="0" id="<?php echo $fieldName ?>SelectedAddressId">
		<?php else: ?>
        <div>
            <div class="control-group">
				<div class="controls" style="margin-top: -5px; font-weight: bold;">
                    <span class="text"><?php echo $model->toString(' ') ?></span>
                </div>
            </div>
            <div class="control-group">
                <div class="controls force-margin">
                    <input type="button" class="btn btn-primary" value="Change" id="<?php echo $fieldName ?>_searchButton">
                </div>
            </div>
            <input type="hidden" name="<?php echo $fieldName ?>[id]" value="<?php echo $model->id ?>" id="<?php echo $fieldName ?>SelectedAddressId">


        </div>
		<?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    (function ()
    {
        var name = '<?php echo $fieldName ?>';
        AddressTools(name).init();
        AddressTools(name).attachEvent('onSelectAddress', function (addrID)
        {

            var start = new Date().getTime();
            $.get('/admin4/Address/infoForOld', {'name' : name, 'id' : addrID}, function (data)
            {
                $('#' + name + 'Box').replaceWith($('#' + name + 'Box', data));
            });
        })
    })();

</script>