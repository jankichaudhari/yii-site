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
$model = isset($model) && $model ? $model : false;
$noAddressMessage = isset($noAddressMessage) && $noAddressMessage ? $noAddressMessage : 'Address is not selected';
?>

<div>
	<div id="<?php echo $fieldName ?>Box">
		<?php if (!$model) : ?>
		<span class="text">
			<input type="hidden" name="<?php echo $fieldName ?>[id]" value="0" id="<?php echo $fieldName ?>SelectedAddressId">
			<input type="text" id="searchAddress" placeholder="Search for address">
			<?php else: ?>
				<span class="text"><?php echo $model->toString('<br>') ?></span><br>
				<input type="button" class="btn btn-primary" value="Change" id="<?php echo $fieldName ?>_searchButton">
				<input type="hidden" name="<?php echo $fieldName ?>[id]" value="<?php echo $model->id ?>" id="<?php echo $fieldName ?>SelectedAddressId">
			<?php
			endif; ?>
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
			$.get('<?php echo Yii::app()->createUrl('Address/info') ?>', {'name' : name, 'id' : addrID}, function (data)
			{
				$('#' + name + 'Box').replaceWith($('#' + name + 'Box', data));
			});
		})
	})();

	$('#searchAddress').autocomplete({
										 source : function (request, response)
										 {
											 $.getJSON('/admin4/Address/autocomplete/search/' + request.term, function (data)
											 {
												 data.push({'label' : 'Add New Address...', 'value' : '', 'id' : 'new'});
												 response(data);
											 });
										 },

										 select : function (event, ui)
										 {
											 if (ui.item.id != 'new') {
												 AddressTools("<?php echo $fieldName ?>").selectAddress(ui.item.id);
											 } else {

											 }

										 }
									 });
</script>