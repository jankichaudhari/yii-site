<?php
/**
 * @var $this                PropertyController
 * @var $model               Deal
 * @var $cListView           CListView
 * @var $dataProvider        CActiveDataProvider
 * @var $isMobile            bool
 */

$this->pageTitle = $model->dea_type;
?>

<div class="detail-box-listings property-listings">
	<div class="page-top-block property-listing">
		<div class="page-content">
			<div class="row">
				<div class="span8 search-property-widget">
					<?php $this->widget("application.components.public.widgets.SearchProperty.SearchProperty", ['view' => $isMobile ? 'defaultMobile' : '']) ?>
				</div>
			</div>
		</div>
	</div>

	<div class="body">
		<div class="page-content sale-properties">
			<div class="row margin-bottom"></div>
			<div class="row margin-bottom">
				<div class="span8 listings">
					<?php $cListView = $this->widget('zii.widgets.CListView', array(
							'itemView'        => 'view',
							'viewData'        => ['isMobile' => $isMobile],
							'dataProvider'    => $dataProvider,
							'summaryText'     => '{count} properties found',
							'template'        => '{pager} {summary}{items}{pager} {summary}',
							'itemsCssClass'   => 'item-listing-container',
							'pagerCssClass'   => 'listing-pager-container',
							'summaryCssClass' => 'listing-summary-container',
							'pager'           => [
									'class'          => 'application.components.public.widgets.PublicLinkPager.PublicLinkPager',
									'nextPageLabel'  => '',
									'lastPageLabel'  => '',
									'prevPageLabel'  => '',
									'firstPageLabel' => '',
									'header'         => '',
									'pageParams'     => $_GET,
									'cssFile'        => '',
									'maxButtonCount' => 11,
									'htmlOptions'    => ['class' => 'listing-pager'],
							],
					)) ?>

				</div>
				<div class="span4 additional-widgets">
					<div class="white-bg top-border-orange with-shadow">
						<?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="back-to-top">
		<div class="fixed-wrapper">
			<div class="fixed">
				<img src="<?= Icon::PUBLIC_BACK_TO_TOP ?>" alt="BACK TO TOP">
			</div>
		</div>
	</div>
</div>