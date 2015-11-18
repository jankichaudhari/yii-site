<?php
/**
 * @var $thisWidget TabbedLayout
 */
echo '<div class="tab-group">';
echo '<div class="tab-group-header">';
foreach ($thisWidget->tabs as $id => $tab) {
	echo '<span class="tab-header ' . ($thisWidget->activeTab == $id ? 'active' : '') . '" data-header-for="' . $id . '">' . $tab['header'] . '</span>';
}
echo '</div>';
echo '<div class="tab-container">';
foreach ($thisWidget->tabs as $id => $tab) {
	$tab['htmlOptions']['data-tab-id'] = $tab['htmlOptions']['id'];

	if ($id == $thisWidget->activeTab) {
		$tab['htmlOptions']['class'] .= ' active';
	}
	echo CHtml::tag('div', $tab['htmlOptions'], $tab['content']);
}
echo '</div>';
echo '</div>';
?>

<script type="text/javascript">
	$('iframe').each(function () {
		var id = $(this).attr('id');
		iframeHeight(id);
	});

	function setActiveTab(element, context, id) {
		$.get('/admin4/lists/setSessionValue/key/<?php echo $this->sessionKey ?>/value/' + id);

		$('.tab-header', context).removeClass('active');
		element.addClass('active');

		$('.tab', context).removeClass('active');
		$('#' + id + '', context).addClass('active');

		var thisContent = $('#' + id).html();
		if (thisContent.indexOf('iframe') >= 0) {
			var thisIframeId = $('#' + id + ' iframe').attr('id');
			iframeHeight(thisIframeId);
		}
		if (thisContent.indexOf('.map') >= 0) {
			var thisMapId = $('.map').attr('id');
			initialize(thisMapId);
		}
		location.hash = '##' + id;
	}
	$('.tab-group').each(function () {
		var context = $(this);
		$('.tab-header', context).each(function () {

			var id = $(this).data('header-for');

			if (window.location.hash) {
				var thisHash = window.location.hash;
				var thisHashVal = thisHash.split("##");

				if (id == thisHashVal[1]) {
					setActiveTab($(this), context, id);
				}
			}

			$(this).on('click', function () {
				setActiveTab($(this), context, id);
			});

		});
	});
</script>
