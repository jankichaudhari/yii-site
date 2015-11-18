<?php
/**
 * @var $this  BlogController
 * @var $model Blog
 */
$this->pageTitle = $model->title;
?>

<div class="blog-details">
	<div class="page-top-block blog"></div>
	<div class="body">
		<div class="page-content detail-page-content">
			<div class="row spaced">
				<div class="span8 description-content">
					<div class="main-title"><?php echo $model->title ?></div>
					<div class="orange-big-separator"></div>
					<div class="info-container">
						<div class="strapline"><?php echo $model->strapline ?></div>
						<div class="description" id="toggle-description">
							<?php echo $model->body ?>
						</div>
					</div>
					<div id="disqus_thread"></div>
				</div>

				<div class="span4 widget-info-content">
					<div class="contact-us-widget">
						<?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
	var disqus_shortname = 'woosterstock'; // required: replace example with your forum shortname

	/* * * DON'T EDIT BELOW THIS LINE * * */
	(function () {
		var dsq = document.createElement('script');
		dsq.type = 'text/javascript';
		dsq.async = true;
		dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
	})();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a>
</noscript>