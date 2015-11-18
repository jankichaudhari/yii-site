<?php
/**
 * @var $this         ParkController
 * @var $model        Place
 * @var $title        String
 * @var $instructions Deal []
 * @var $smallDevice  bool
 */
$this->pageTitle = $model->title;
?>

<div class="park-details">

	<?php
	$this->renderPartial('detailsView/slider-gallery', [
			'model'       => $model,
			'smallDevice' => $smallDevice,
			'title'       => $title
	])
	?>

	<div class="body">
		<div class="page-content detail-page-content">
			<div class="row margin-bottom"></div>
			<div class="row margin-bottom">
				<div class="span8 description-content">
					<div class="main-title">
						<?php echo $title; ?>
					</div>

					<div class="orange-big-separator"></div>

					<div class="info-container">

						<?php
						$this->renderPartial('detailsView/social-options', [
								'model' => $model,
								'title' => $title
						]);
						?>

						<div class="strapline">
							<?php echo $model->strapline ?>
						</div>
						<div class="description">
							<?php echo $model->description ?>
						</div>
					</div>
				</div>

				<div class="span4 widget-info-content">

					<?php
					$this->renderPartial('detailsView/options', [
							'model'       => $model,
							'title'       => $title,
							'smallDevice' => $smallDevice,
					]);
					?>

					<div class="contact-us-widget info-box additional-widgets">
						<?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
					</div>


					<?php
					$this->renderPartial('detailsView/transport', [
							'model' => $model,
							'title' => $title,
					]);
					?>

				</div>

			</div>
		</div>
	</div>

	<?php $this->renderPartial('//layoutElement/backToTop'); ?>

	<script type="text/javascript">

		$(".description  img").each(function (i, ele) {
			modifyImageTag(i, ele)
		});

		function modifyImageTag(i, ele) {
			var existChildren = $(ele).parent().children('.captionText').length;
			if (existChildren == 0) {
				$(ele).unwrap();
				var thisSrc = $(ele).attr('src');
				var thisSrcArray = thisSrc.split('/images/Place/<?php echo $model->id ?>/');
				var imageName = thisSrcArray[1];
				$(ele).attr('alt', '<?php echo str_replace("'","",$title); ?>');
				$(ele).wrap('<a href="' + thisSrc + '" class="descImage" title="<?php echo str_replace("'","",$model->title); ?>"></a>');
				$(ele).parents('a').append('<span class="zoom-symbol"></span>');

				var requireWidth = 640;
				var thisWidth = $(ele).width();
				var thisHeight = $(ele).height();
				if (thisWidth > requireWidth) {
					var requireHeight = ((requireWidth * thisHeight) / thisWidth);
					$(ele).width(requireWidth);
					$(ele).height(requireHeight);
				}

				$.get('<?php echo $this->createUrl('park/getParkImageInfo') ?>', {'recordId': '<?php echo $model->id ?>', 'recordType': 'Place', 'imageName': imageName }, function (data) {
					if ((data.length != 0) && !(data.captionText === null)) {
						var altTag = "<?php echo $title; ?>, " + data.captionText + "; by Wooster & Stock";
						$(ele).attr('alt', altTag);
						$(ele).parent().attr('title', data.captionText);
						$(ele).parent().after('<div class="captionText">' + data.captionText + '</div>');
					}
				}, "json");
			}
		}

		$('.descImage').on('click', function (event) {
			var thisSrc = $(this).attr('href');
			var stop = false;
			$("a[rel=park-gallery]").each(function () {
				if (stop) return;
				if ($(this).attr('href') == thisSrc.replace('_medium', '')) {
					$(this).trigger('click');
					stop = true;
				}
			});
			event.preventDefault();
			return false;
		});

	</script>