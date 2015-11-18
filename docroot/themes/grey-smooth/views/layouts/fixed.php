<?php
/**
 * @var $this CController
 */
$this->beginContent('//layouts/new/main', ['fixed' => true]);
$this->renderPartial('//layouts/new/header', ['fixed' => true]);
$this->renderPartial('//layouts/leftMenu', ['fixed' => true]);
?>
<div class="page-content relative">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<?php echo $content; ?>
			</div>
		</div>
	</div>
</div>
<?php $this->endContent(); ?>

