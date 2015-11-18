<style type="text/css">
	.slider-container, .slider-container * {
		-webkit-backface-visibility : visible !important;
	}
</style>
<?php
/**
 * @var $this SiteController
 */
$this->pageTitle = 'Valuations';
/** @var  $isMobile \Device */
$isMobile = Yii::app()->device->isDevice('mobile');
?>

<div class="valuation-page">
	<div class="slider-container">
		<div class="slider">
			<?php
			$imageFolderPath = Yii::app()->params['imgPath'] . '/valuations/';
			if ($galleryImages = glob(realpath($imageFolderPath) . "/*.{jpg,gif,png}", GLOB_BRACE)) {
				$count = 0;
				foreach (($galleryImages) as $galleryImage) {
					$count++;
					$imageName = '/images/valuations/' . basename($galleryImage);
					?>
					<div class="item item<?= $count; ?>">
						<img src="<?php echo $imageName; ?>"
							 alt="<?php echo $imageName . '; by Wooster & Stock' ?>"/>
					</div>
				<?php
				}
			}
			?>
		</div>
		<div class="move-arrows">
			<div id="slider-left-arrow" class="arrow left"><span></span></div>
			<div id="slider-right-arrow" class="arrow right"><span></span></div>
		</div>



	</div>

	<div class="page-content book-valuation-block">
		<div class="row">
			<div class="span8 book-valuation-form">
				<?php $this->widget("application.components.public.widgets.BookValuation.BookValuation", ['view' => $isMobile ? 'defaultMobile' : '']) ?>
			</div>
		</div>
	</div>

	<div class="body">
		<div class="page-content valuation">

			<div class="row margin-bottom"></div>
			<div class="row margin-bottom">
				<div class="span6 orange-big-heading">
					Why Choose Wooster & Stock?
				</div>
				<div class="span6 grey-heading-text">
					Wooster & Stock is a specialist in online marketing. Our website, at the heart of the operation, is
					one
					of the best-loved in the business. It’s easy to navigate, 100% currently and linked to all the major
					property portals. Behind the website is the W&S database. Extensive and richly detailed, it lets us
					quickly target the right buyers. Our philosophy is to give our clients’ properties the very best
					treatment, no matter what their size or location. We have earned a reputation for selling the finest
					homes in south London but we are as happy marketing a studio flat as a six bedroom villa.
				</div>
			</div>

			<div class="orange-big-separator"></div>

			<div class="row valuation-blocks">
				<div class="span4 detail-box">
					<div class="photo">
						<img src="/images/valuation/valuations-advertising.jpg"
							 alt="Wooster & Stock - Advertising">
					</div>
					<div class="detail-info">
						<div class="title">Advertising</div>
						<div class="info">
							With a mixture of online and paper advertising you can be sure we are reaching the people
							who
							matter. We have our own in-house design team who can create bespoke artwork at your request.
							Our
							regular leaflet drops do a fantastic drop of reaching the right type of clientele.
						</div>
					</div>
				</div>
				<div class="span4 detail-box">
					<div class="photo">
						<img src="/images/valuation/valuations-website.jpg" alt="Wooster & Stock - Website">
					</div>
					<div class="detail-info">
						<div class="title">Website</div>
						<div class="info">
							Our photos and floorplans are beautifully clear and our famous descriptions don’t “comprise
							of”
							anything dull. Photographs will display full screen, a feature that few other agents can
							match
							and a key attribute to marketing your home effectively. The site is super easy to navigate
							too.
						</div>
					</div>
				</div>
				<div class="span4 detail-box end-of-row">
					<div class="photo">
						<img src="/images/valuation/valuations-portals.jpg" alt="Wooster & Stock - Portals">
					</div>
					<div class="detail-info">
						<div class="title">Portals</div>
						<div class="info">
							We are on all the main portal sites and offer an extended service beyond simple listings.
							Our
							attractive and eye catching banners help drive traffic to our website and our featured
							property
							boxes can give your home the edge it needs.
						</div>
					</div>
				</div>
			</div>

			<div class="row valuation-blocks">
				<div class="span4 detail-box">
					<div class="photo">
						<img src="/images/valuation/valuation-experienced-staff.jpg"
							 alt="Wooster & Stock - Experienced Staff">
					</div>
					<div class="detail-info">
						<div class="title">Experienced Staff</div>
						<div class="info">
							Our experienced team of negotiators are familiar with the local area; having lived, loved
							and
							worked in south London for numerous years. We pride ourselves on our friendly, personable
							approach to selling homes and our ability to match the right buyer to your property is
							crucial
							to our success.
						</div>
					</div>
				</div>
				<div class="span4 detail-box">
					<div class="photo">
						<img src="/images/valuation/valuations-a-parting-gift.jpg"
							 alt="Wooster & Stock - A Parting Gift">
					</div>
					<div class="detail-info">
						<div class="title">A Parting Gift</div>
						<div class="info">
							When we sell a property we create our vendors a book with the most beautiful photos of their
							property in it. It’s a little memento for you of a well-loved home and our way of saying
							thank
							you for the business.
						</div>
					</div>
				</div>
				<div class="span4 detail-box">
					<div class="photo">
						<img src="/images/valuation/valuations-photography.jpg"
							 alt="Wooster & Stock - Photography">
					</div>
					<div class="detail-info">
						<div class="title">Photography</div>
						<div class="info">
							Our large, full screen photographs are some of the best in the business. Our standards are
							high
							for every property that we photograph, no matter how expensive it is.
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>