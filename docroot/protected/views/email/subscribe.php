<?php
/**
 * @var $this EmailController
 */
$this->pageTitle = 'Welcome back';
?>
<div class="body not-found">
	<div class="page-content unsubscribe">
		<div class="row margin-bottom">
			<div class="span12">
				<img src="/images/subscribe-back-banner.jpg" alt="Welcome Back!">
			</div>
		</div>
		<div class="orange-big-separator"></div>
		<div class="row margin-bottom">
			<div class="span8">
				<?php echo CHtml::link('SALES', ['property/'], ['class' => '_link-block with-shadow']) ?>
				<?php echo CHtml::link('ARRANGE VALUATION', ['site/page/view/valuations'], ['class' => '_link-block with-shadow']) ?>
			</div>
			<div class="span4 white-bg top-border-orange with-shadow">
				<?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
			</div>
		</div>
	</div>
</div>