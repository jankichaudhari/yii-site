<?php
/**
 * @var $this                SiteController
 * @var $latestProperties    Deal[]
 * @var $featuredVideo       InstructionVideo
 * @var $mostViewed          Deal[]
 * @var $instructionVideos   InstructionVideo[]
 * @var $clientScript        CClientScript
 * @var $propertyCategories  PropertyCategory[]
 *
 */
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.cycle.plugin.js', CClientScript::POS_BEGIN);
$this->pageTitle = "";
?>
<div class="page-top-block home">
	<div class="page-content">
		<div class="row spaced row-page-top-content">
			<div class="span6 cell-left">
				<div class="top-description">
					<div class="headline">
						Find your dream home, today.
					</div>
					<div class="strapline">
						A fresh approach to estate agency
					</div>
				</div>
			</div>
			<div class="span4 offset2 cell-right">
				<?php $this->widget("application.components.public.widgets.SearchProperty.SearchProperty", ['view' => 'home']) ?>
				<div class="top-widget-container narrow options-tray">
					<div class="inner-padding">
						<div class="row options-wrap">
							<div class="half-cell">
								<div class="input-wrapper">
									<a href="/register" class="btn full-width">Register</a>
								</div>
							</div>
							<div class="half-cell">
								<div class="input-wrapper">
									<a href="/valuations" class="btn full-width">Book Valuation</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="body home-container">
	<div class="page-content">

		<div class="row margin-bottom spaced latest-property-featured-video">
			<div class="span8 latest-property">
				<div class="row">
					<div class="span4 latest-property-img-container">
						<?php
						foreach ($latestProperties as $key => $instruction) {
							/** @var $instruction Deal [ ] */
							$className = !$key ? 'active' : 'inactive';
							if ($instruction->photos) {
								echo CHtml::link(
										  CHtml::image($instruction->getMainImage()
																   ->getMediaImageURIPath('_large'), $instruction->getMainImage()->med_title, [
															   'id'    => 'photo-' . $instruction->dea_id,
															   'style' => 'width: 100%;'
													   ]),
										  '/details/' . $instruction->dea_id,
										  ['class' => $className]
								);
							}
						}
						?>
					</div>
					<div class="span4 latest-property-list">
						<div class="inner-padding">
							<div class="header">NEW PROPERTY</div>
							<div class="new-underline"></div>
							<ul>
								<?php
								foreach ($latestProperties as $i => $instruction) {
									$info      = $instruction->property->getLine(3) . ", " . $instruction->property->getPostcodePart();
									$priceInfo = $instruction->dea_qualifier === Deal::QUALIFIER_POA ? Deal::QUALIFIER_POA : Locale::formatPrice($instruction->dea_marketprice);
									$className = !$i ? 'selected' : '';
									echo '<li data-id="' . $instruction->dea_id . '" class="' . $className . '">';
									if (!empty($instruction->property->addressId)) {
										echo CHtml::link($info . " " . $priceInfo, '/details/' . $instruction->dea_id);
									}
									echo '</li>';
								}
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<?php $this->renderPartial('home/featuredVideo', ['model' => $featuredVideo]) ?>
		</div>

		<div class="row margin-bottom row-most-viewed-pick-agent">
			<?php
			if (isset($mostViewed[0])):
				$instruction = $mostViewed[0];
				$info        = $instruction->property->getShortAddressString(', ');
				$priceInfo   = $instruction->dea_qualifier === Deal::QUALIFIER_POA ? Deal::QUALIFIER_POA : Locale::formatPrice($instruction->dea_marketprice);
				?>
				<div class="span4 home-container-small"
					 style="background-image: url(<?php echo $instruction->getMainImage()->getMediaImageURIPath('_large') ?>)">
					<div class="top-part"></div>
					<div class="bottom-part">
						<div class="inner-padding">
							<div class="header">
								<a href="/topTwenty" class="white-text-shadow">MOST VIEWED</a>
							</div>
							<div class="content">
								<?php echo CHtml::link($info . ' ' . $priceInfo, "/details/" . $instruction->dea_id, ['class' => 'gray hover bold white-text-shadow']); ?>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>


			<div class="span8 pick-pedigree">
				<div class="row">
					<div class="span4 pick-pedigree-picture">
						<img src="/images/pick-agent-pedigree.jpg">
					</div>
					<div class="span4 pick-pedigree-content">
						<div class="home-orange-headline">
							<a href="<?php echo $this->createUrl('/valuations') ?>">
								Pick An Agent With Pedigree
							</a>
						</div>
						<div class="home-gray-description">
							<a href="<?php echo $this->createUrl('/valuations') ?>">Deciding which agent to use can be a
								tricky business. Find out what puts us ahead
								of the pack.</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php $this->renderPartial('home/help-van') ?>

		<?php $this->renderPartial('home/park-open-space') ?>

	</div>
</div>


<script type="text/javascript">
	$("#office-gallery").imageScrollGallery({
		leftArrow: '#office-left-arrow',
		rightArrow: '#office-right-arrow',
		imgWidth: 146,
		imgHeight: 146,
		gap: 24,
		scrollFor: 6,
		duration: 700});

	$("#home-page-park-gallery").cycle({
		fx: 'scrollHorz',
		easing: 'easeInCirc',
		speed: 'slow',
		timeout: 0,
		prev: '#park-left-arrow',
		next: '#park-right-arrow'
	});

	$('.latest-property-list ul li').on('mouseover',
			function () {
				$('.latest-property-list li').removeClass('selected');
				$(this).addClass('selected');
				$('.latest-property-img-container img').hide();
				$('#photo-' + $(this).data('id')).show();
			});

	var propertyTypes = $('.property-types');

	propertyTypes.hover(
			function () {
				$(this).children('.button').hide();
				$(this).children('.picture').show();
			}, function () {
				$(this).children('.picture').hide();
				$(this).children('.button').show();
			}
	);

	propertyTypes.on('click', function () {
		window.location.href = $(this).data('id');
	})

</script>
