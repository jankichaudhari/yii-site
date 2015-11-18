<?php
/**
 * @var $this            ClientController
 * @var $model           Client
 * @var $form            AdminFilterForm
 *
 */
?>
<div class="row-fluid">
	<div class="span12">
		<?php $this->renderPartial('_filter_primary', ['model' => $model]) ?>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<?php $this->renderPartial('_listing_with_use', [
														'model' => $model
														]) ?>
	</div>
</div>
<script type="text/javascript">
	$('#client-list a.use').live("click", function (event) {
		event.preventDefault();
		var id = parseInt($(this).attr("href"));
		useClient(id);
		return false;
	});
	var selectClient = function (id) {
	}


	var useCreatedClient = function (id) {
		selectClient(id);
	}

	$('a[rel=client-list-add-button]').on('click', function () {
		new Popup('<?php echo $this->createUrl("Create", array('callback' => 'useClient')) ?>').open();
		return false;
	});

	var useClient = function (id) {
		<?php if (isset($_GET['onSelect']) && $_GET['onSelect']): ?>
		var functionName = '<?php echo $_GET['onSelect'] ?>';
		if (window.opener.window[functionName]) {
			window.opener.window[functionName](id);
			window.close();
		}
		return true;
		<?php endif; ?>
		alert('onSelect param is not defined!');
	}
</script>