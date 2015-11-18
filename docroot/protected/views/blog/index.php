<?php
/**
 * @var $this  BlogController
 *
 * @var $model Blog
 *
 */
$this->pageTitle = "Blogs";
?>

<div class="detail-box-listings blog-listings">
	<div class="page-top-block blog">

	</div>
	<div class="body">
		<div class="page-content">
			<div class="row box">
				<div class="span8 overlay-row">
					<div class="overlay-cell light-gray-bg">
						Industry musings from our favourite property focused canine
					</div>
				</div>
			</div>
			<div class="row spaced box">
				<div class="span12">
					<div class="orange-big-heading">Cosmo Speaks</div>
				</div>
			</div>
			<div class="orange-big-separator"></div>
			<div class="row spaced margin-bottom">
				<div class="span8 listings">
					<?php
					$this->widget('zii.widgets.CListView', array(
																'dataProvider' => $model->search(),
																'itemView'     => '_post',
																'template'     => '{items}<div class="clearfix"></div>{pager}',
																'emptyText'    => 'No blog posts here yet. Come back later for update.',
																'pager'        => [
																	'class'          => 'application.components.public.widgets.PublicLinkPager.PublicLinkPager',
																	'prevPageLabel'  => 'Newer entries »',
																	'lastPageLabel'  => '',
																	'nextPageLabel'  => '« Older entries',
																	'firstPageLabel' => '',
																	'header'         => '',
																	'pageParams'     => $_GET,
																	'cssFile'        => '',
																	'maxButtonCount' => 11,
																	'htmlOptions'    => ['class' => 'simple-pager inversed'],
																],
														   ));
					?></div>
				<div class="span4 additional-widgets">
					<div class="white-bg top-border-orange with-shadow">
						<?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

