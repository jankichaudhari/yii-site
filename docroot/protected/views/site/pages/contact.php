<?php
/**
 * @var $this SiteController
 *
 */
/** @var  $isMobile \Device */
$isMobile = Yii::app()->device->isDevice('mobile');
$this->pageTitle = 'Contacts';
/** @var $offices Office[] */
$offices = Office::model()->with(array("branches" => array('condition' => "bra_status = 'Active' AND business_unit is not null AND business_unit > 0")))->findAll();
$pageGalleryImages = PageGalleryImage::model()->officePhotos()->findAll();
$imagePath = Yii::app()->params['imgUrl'] . "/ContactPageGallery/1/";
?>
<div class="detail-box-listings contact-page">
	<?php
	if ($pageGalleryImages):
		?>

		<div class="slider-container">
			<div class="slider">
				<?php foreach ($pageGalleryImages as $key => $pageGalleryImage):
					$fullThumbImagePath = $imagePath . $pageGalleryImage->fullName;
					$fullOrgImagePath   = $imagePath . $pageGalleryImage->name;
					?>
					<div class="item item<?php echo $key; ?>">
						<a href="<?php echo $fullOrgImagePath ?>" rel="office-photo-gallery" id="<?php echo $key ?>">
							<img src="<?php echo $fullThumbImagePath ?>" alt="Wooster & Stock" width="440">
							<span class="zoom-symbol"></span>
						</a>
					</div>
				<?php endforeach ?>
			</div>
			<div class="move-arrows">
				<div id="slider-left-arrow" class="arrow left"><span></span></div>
				<div id="slider-right-arrow" class="arrow right"><span></span></div>
			</div>
		</div>
	<?php endif; ?>


	<?php if ($isMobile): ?>
		<div class="mobile-page-contact-widget">
			<div class="page-content">
				<?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="body">
		<div class="page-content">
			<div class="row margin-bottom"></div>
			<div class="row margin-bottom">
				<div class="span8 listings">
					<?php foreach ($offices as $office) : ?>
						<div class="detail-box contact-us">
							<div class="row">

								<div class="span4 thumbnail">
									<img class="branchImage" src="/images/offices/<?php echo $office->image ?>"
										 alt="<?php echo $office->title ?>"/>
								</div>

								<div class="info">
									<h3><?php echo $office->title ?></h3>

									<?php if ($office->address) : ?>
										<div class="block address">
											<?php echo $office->address->line1 ?> <?php echo $office->address->line3 ?> <?php echo $office->postcode ?>
										</div>
									<?php endif; ?>

									<?php foreach ($office->branches as $branch): ?>
										<div class="contact-details">

											<div class="contact-detail telephone">
												<div class="block">Telephone</div>
												<div class="block"><?php echo $branch->bra_tel ?></div>
											</div>

											<div class="contact-detail email">
												<div class="block">Email</div>
												<div class="block"><a href="mailto:<?php echo $branch->bra_email ?>"
																	  class="blue-link"><?php echo $branch->bra_email ?></a>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>

							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="span4 widgets">

					<div class="info-box contact-us-widget additional-widgets">
						<?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
					</div>

					<div class="all-offices info-box">
						<div class="inner-padding">
							<div class="row-fluid">
								<div class="header">All Offices</div>
							</div>
							<div class="row-fluid">
								<div class="narrow-info-row first fax">
									(fax) 08456 800 461
								</div>
								<div class="narrow-info-row email">
									<a href="mailto:admin@woosterstock.co.uk" class="blue-link">admin@woosterstock.co.uk</a>
								</div>
							</div>
						</div>
					</div>

					<div class="opening-hours info-box last">
						<div class="inner-padding">
							<div class="row-fluid">
								<div class="header">Opening Hours</div>
							</div>
							<div class="row-fluid timings">
								<div class="narrow-info-row first">Monday to Fridays</div>
								<div class="narrow-info-row">9am to 6pm</div>
							</div>
							<div class="row-fluid timings">
								<div class="narrow-info-row">Saturdays</div>
								<div class="narrow-info-row">9am to 5pm</div>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$("a[rel=office-photo-gallery]").each(function () {
		openPopUp(this, {
			cyclic: true
		});
	});
</script>