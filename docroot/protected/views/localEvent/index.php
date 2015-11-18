<?php
/**
 * @var $this         CController
 * @var $dataProvider CActiveDataProvider
 */
$this->pageTitle = 'Local Events';
?>

<div class="detail-box-listings localevent-listings">
<div class="page-top-block localevent">
    <div class="page-content">
        <div class="row"></div>
    </div>
</div>

<div class="body">
    <div class="page-content">
        <div class="row margin-bottom"></div>
        <div class="row margin-bottom">
            <div class="span8 listings">
				<?php $this->widget('zii.widgets.CListView', array(
																  'dataProvider'=> $dataProvider,
																  'itemView'    => 'view',
																  'summaryText'  => '{count} events found',
																  'template'     => '{pager} {summary}{items}{pager}',
																  'htmlOptions'  => ['class' => 'local-event'],
																  'itemsCssClass' => 'item-listing-container',
																  'pagerCssClass' => 'listing-pager-container',
																  'summaryCssClass' => 'listing-summary-container',
																  'pager' => [
																	  'class' => 'application.components.public.widgets.PublicLinkPager.PublicLinkPager',
																	  'nextPageLabel' => '',
																	  'lastPageLabel' => '',
																	  'prevPageLabel' => '',
																	  'firstPageLabel' => '',
																	  'header' => '',
																	  'pageParams' => $_GET,
												  					  'cssFile' => '',
																	  'maxButtonCount' => 11,
																	  'htmlOptions' => ['class' => 'listing-pager'],
																  ],
															 )); ?>


            </div>
            <div class="span4 additional-widgets" style="padding-top: 49px;">
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