<?php
/**
 * @var $this CController
 */

$this->beginContent('//layouts/new/main');
$this->renderPartial('//layouts/new/header');
$this->renderPartial('//layouts/leftMenu');
?>
<div class="page-content">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12" style="padding-top: 14px">
				<?php echo $content; ?>
			</div>
		</div>
	</div>
</div>
<?php $this->endContent(); ?>

