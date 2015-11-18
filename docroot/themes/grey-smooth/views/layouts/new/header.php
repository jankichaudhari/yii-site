<?php
/**
 * @var $fixed bool|null
 */
$fixed = isset($fixed) ? $fixed : null;
?>
<div class="row-fluid page-header <?php echo $fixed ? 'fixed' : '' ?>">
	<div class="span12">
		<div class="cell image">
			<img src="/images/sys/admin/admin-top-logo.png" alt="">
		</div>
		<div class="cell"><?php echo CHtml::link(Yii::app()->user->getState("fullname"), array("User/UserPreferences")); ?></div>
		<div class="cell"><?php echo date("l jS F Y") ?> <span id="timecontainer"></span></div>
		<div style="float:right; line-height: 55px; margin-right: 20px;">
			<input type="text" style="border-radius: 10px; width: 250px;" placeholder="Quick Search" id="quicksearch" />
		</div>
	</div>
</div>
<script type="text/javascript">
	(function ()
	{
		$('#quicksearch').autocomplete({
										   source : function (req, res)
										   {
											   $.getJSON('/admin4/quicksearch', {'search' : req.term}, function (data)
											   {
												   res(data);
											   })
										   },
										   select : function (event, ui)
										   {
											   console.log(ui.item);

											   if (ui.item.url) {
												   document.location.href = ui.item.url;
											   }
										   }
									   })
	})();
</script>
