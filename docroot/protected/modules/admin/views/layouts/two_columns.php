<?php
/**
 * @var $this CController
 */
$this->beginContent('//layouts/main');
$this->renderPartial('//layouts/header');
$this->renderPartial('//layouts/leftMenu');
?>
<div class="content_wide">
	<?php echo $content; ?>
</div>
<?php $this->endContent(); ?>
