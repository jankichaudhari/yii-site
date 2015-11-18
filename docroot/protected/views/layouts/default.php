<?php
/**
 * @var $this    CController
 * @var $content String
 */
$activeLink = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$activeLink = $activeLink == '/' ? '/index' : $activeLink;
$pageTitle = isset($this) ? $this->pageTitle : (isset($pageTitle) ? $pageTitle : '');
$topProperties = 'Twenty';
/** @var $device \device */
$device = Yii::app()->device;
$isMobile = $device->isDevice('mobile');

/** @var $cs CClientScript */
$cs = Yii::app()->getClientScript();
//$cs->registerScriptFile('/js/jquery.latest.js');
$cs->registerScriptFile('/js/jquery-1.11.0.min.js');
$cs->registerScriptFile('/js/jquery.scrollTo-1.4.2-min.js');
$cs->registerScriptFile('/js/jquery.localscroll-1.2.7-min.js');
$cs->registerScriptFile('/js/imageScrollGalery.js');
$cs->registerScriptFile('/js/jquery.fancybox-1.3.4.pack.js');
$cs->registerScriptFile('/js/jquery.easing-1.3.pack.js');
$cs->registerScriptFile('/js/jquery.mousewheel.min.js');
$cs->registerScriptFile('/js/jquery.slider.js');
$cs->registerScriptFile('/js/Util-head.js', CClientScript::POS_HEAD);
$cs->registerScriptFile('/js/Util.js', CClientScript::POS_END);
if($isMobile){
	$cs->registerScriptFile('/js/jquery.jpanelmenu.js', CClientScript::POS_HEAD);
	$cs->registerScriptFile('/js/Util-mobile.js', CClientScript::POS_END);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8>
	<?php

	if ($isMobile) {
		echo '<link type="text/css" href="/css/public/mobile.css" rel="stylesheet"/>';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"/>';
	} else {
		echo '<link type="text/css" rel="stylesheet" href="/css/public/main.css">';

	}
	echo '<link rel="stylesheet" href="/css/public/fancybox/jquery.fancybox-1.3.4.css">';
	?>

	<link rel="shortcut icon" href="/images/sys/favicon-woosterstock.ico" type="icon/vnd.microsoft">
	<title>Wooster & Stock <?php echo $pageTitle ? ' - ' . $pageTitle : '' ?></title>
</head>

<body>
<?php if (!isset($_COOKIE['cookies_accepted']) || $_COOKIE['cookies_accepted'] !== 'yes'): ?>
	<div class="cookies-disclaimer" id="cookie-policy">
		Hello there, we use cookies to give you the best possible experience on our website. By continuing to browse
		this site, <span style="color: #ff9900; font-weight: bold;">you give consent for cookies to be used.</span>
	</div>
<?php endif ?>

<div class="page-header-top">
	<div id="header">
		<div class="logo">
			<a href="/">
				<img src="/images/sys/wooster-stock-logo.png" alt="Wooster & Stock">
			</a>
		</div>
		<div class="info">
			<div class="block">
				<div class="icon-block" style="margin-right: 13px;">
					<a href="//www.facebook.com/woosterstock" target="_blank"><span class="icon facebook"></span></a>
				</div>
				<div class="icon-block">
					<a href="//twitter.com/woosterstock" target="_blank"><span class="icon twitter"></span></a>
				</div>
				<div class="icon-block">
					<a href="//vimeo.com/user13981519" target="_blank"><span class="icon vimeo"></span></a>
				</div>
			</div>
			<div class="block">
				<span class="orange bold">Camberwell / Nunhead</span>
				<span class="separator"></span>
				<span class="white">020 7708 6700</span>
			</div>
			<div class="block">
				<span class="orange bold">Brixton</span>
				<span class="separator"></span>
				<span class="white">020 7952 0590</span>
			</div>
			<div class="block">
				<span class="orange bold">Sydenham</span>
				<span class="separator"></span>
				<span class="white">020 8613 0060</span>
			</div>
		</div>
	</div>

	<a href="#" class="menu-trigger">Menu</a>

	<div class="top-menu" id="menu">
		<div class="top-nav-container">
			<div class="page-content">
				<div class="row">
					<div class="span12 top-nav">
						<ul>
							<li>
								<a href="/" class="<?php echo $activeLink == "/index" ? "active" : "" ?>">Home</a>
							</li>
							<li>
								<a href="/property" class="<?php echo $activeLink == "/property" ? "active" : "" ?>">Property</a>
							</li>
							<li>
								<a href="/top<?php echo $topProperties ?>"
								   class="<?php echo $activeLink == "/top" . $topProperties ? "active" : "" ?>">Top <?php echo $topProperties ?></a>
							</li>
							<li>
								<a href="/register"
								   class="<?php echo $activeLink == "/register" ? "active" : "" ?>">Register</a>
							</li>
							<li>
								<a href="/valuations"
								   class="<?php echo $activeLink == "/valuations" ? "active" : "" ?>">Valuations</a>
							</li>
							<li>
								<a href="/local-events"
								   class="<?php echo $activeLink == "/local-events" ? "active" : "" ?>">Local
									events</a>
							</li>
							<li>
								<a href="/blog" class="<?php echo $activeLink == "/blog" ? "active" : "" ?>">Blog</a>
							</li>
							<li>
								<a href="/links" class="<?php echo $activeLink == "/links" ? "active" : "" ?>">Links</a>
							</li>
							<li>
								<a href="/contact"
								   class="<?php echo $activeLink == "/contact" ? "active" : "" ?>">Contact</a>
							</li>
							<li class="mobile-site-menu">
								<a href="/career"
								   class="<?php echo $activeLink == "/career" ? "active" : "" ?>">Careers</a>
							</li>
							<li>
								<a href="/parks"
								   class="<?php echo ($activeLink == "/parks" || $activeLink == "/parks/view/gallery" || $activeLink == "/parks/view/list" || $activeLink == "/parks/view/map") ? "active" : "" ?>">Parks</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="wrap-content">
	<?php echo $content; ?>
</div>
<div id="footer">
	<div class="page-content">
		<div class="footer-row">
			<a href="/"><img src="/images/sys/wooster-stock-small-logo.png" alt="Wooster&Stock"></a>
		</div>
		<div class="footer-row">
			<ul id="menu">
				<li>
					<a href="/property">Property</a>
					<span class="menu-separator"></span>
				</li>
				<li>
					<a href="/top<?php echo $topProperties ?>">Top <?php echo $topProperties ?></a>
					<span class="menu-separator"></span>
				</li>
				<li>
					<a href="/register">Register</a>
					<span class="menu-separator"></span>
				</li>
				<li>
					<a href="/valuations">Valuations</a>
					<span class="menu-separator"></span>
				</li>
				<li>
					<a href="/local-events">Local events</a>
					<span class="menu-separator"></span>
				</li>
				<li>
					<a href="/links">Links</a>
					<span class="menu-separator"></span>
				</li>
				<li>
					<a href="/contact">Contact</a>
					<span class="menu-separator"></span>
				</li>
				<li>
					<a href="/career">Careers</a>
					<span class="menu-separator"></span>
				</li>
				<li>
					<a href="//www.facebook.com/woosterstock">Follow us on Facebook</a>
				</li>
			</ul>
		</div>
		<div class="footer-row">
			<div class="office-block">
				<h5>Nunhead Office</h5>

				<div class="contact-info">
					<a href="mailto:nun@woosterstock.co.uk">nun@woosterstock.co.uk</a>
				</div>
				<div class="contact-info">020 7708 6700</div>
			</div>
			<div class="office-block">
				<h5>Brixton Office</h5>

				<div class="contact-info">
					<a href="mailto:brx@woosterstock.co.uk">brx@woosterstock.co.uk</a>
				</div>
				<div class="contact-info">020 7952 0590</div>
			</div>
			<div class="office-block">
				<h5>Sydenham Office</h5>

				<div class="contact-info">
					<a href="mailto:syd@woosterstock.co.uk">syd@woosterstock.co.uk</a>
				</div>
				<div class="contact-info">020 8613 0060</div>
			</div>
		</div>
		<div class="footer-row-mobile first">
			<span class="social-media-icon">
				<img src="/images/sys/woosterstock-twitter-black.png" alt="T"/>
			</span>
			<span class="social-media-icon">
				<img src="/images/sys/woosterstock-vimeo-black.png" alt="V"/>
			</span>
			<span class="social-media-icon">
				<img src="/images/sys/woosterstock-facebook-black.png" alt="F"/>
			</span>
		</div>

		<?php if($device->isMobile()): ?>
			<div class="device-view-option <?php echo $isMobile ? 'footer-row-mobile' : 'footer-row'  ?>">
				<a href="/site/setDevice/type/<?php echo $isMobile ? 'classic' : 'mobile' ?>" id="device-view-option">
					<?php echo $isMobile ? 'View Desktop' : 'View Mobile' ?>
				</a>
			</div>
		<?php endif; ?>
		<div class="<?php echo $isMobile ? 'footer-row-mobile' : 'footer-row' ?> last disclaimer">
			All content Copyright &copy; Wooster &amp; Stock
		</div>
	</div>
</div>
<?php if (defined('WS_PRODUCTION') && WS_PRODUCTION) ?>
<script>
	(function (i, s, o, g, r, a, m) {
		i['GoogleAnalyticsObject'] = r;
		i[r] = i[r] || function () {
			(i[r].q = i[r].q || []).push(arguments)
		}, i[r].l = 1 * new Date();
		a = s.createElement(o),
				m = s.getElementsByTagName(o)[0];
		a.async = 1;
		a.src = g;
		m.parentNode.insertBefore(a, m)
	})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

	ga('create', 'UA-44167381-1', 'woosterstock.co.uk');
	ga('send', 'pageview');
</script>
</body>
</html>

