<?php
$this->beginContent('//layouts/main');
$this->renderPartial('//layouts/header');
?>
<div class="selectScreen">
	<?php echo $content; ?>
</div>
<?php $this->endContent(); ?>