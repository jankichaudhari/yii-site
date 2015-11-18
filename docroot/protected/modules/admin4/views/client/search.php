
<?php
/**
 * @var $this            ClientController
 * @var $model Client
 */
?>
<div class="row-fluid">
	<div class="span12">
		<?php $this->renderPartial('_filter_main', ['model' => $model]) ?>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<?php $this->renderPartial('_listing_with_edit', [
														'dataProvider' => $model->search(),
														'title' => 'Client Search',
														'addButton' => true,
														]) ?>
	</div>
</div>