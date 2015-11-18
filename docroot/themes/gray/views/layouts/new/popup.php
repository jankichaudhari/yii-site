<?php
$this->beginContent('//layouts/new/main');
$this->renderPartial('//layouts/new/header');
?>
<div class="popup-content">
	<?php echo $content; ?>
</div>
<?php $this->endContent(); ?>


