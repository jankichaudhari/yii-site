<?php
/**
 * @var $this CController
 */

$this->beginContent('//layouts/new/main');
$this->renderPartial('//layouts/new/header');
?>
<div class="content" style="padding-top: 10px;">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<?php echo $content; ?>
			</div>
		</div>
	</div>
</div>
<?php $this->endContent(); ?>

