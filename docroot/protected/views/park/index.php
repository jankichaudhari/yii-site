<?php
/**
 * @var $this         ParkController
 * @var $dataProvider CActiveDataProvider
 * @var $parks Place
 * @var $instructions Deal []
 */
/** @var $clientScript CClientScript */
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.easing-1.3.pack.js', CClientScript::POS_BEGIN);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.cycle.plugin.js', CClientScript::POS_BEGIN);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.mousewheel.min.js',CClientScript::POS_BEGIN);
$this->pageTitle = 'Parks and Green Spaces';
?>

<div class="detail-box-listings park-listings">
<div class="page-top-block park">
    <div class="page-content">
        <div class="row">
            <div class="span4 offset8 search-park-widget">
				<?php $this->widget("application.components.public.widgets.SearchPark.SearchPark") ?>
            </div>
        </div>
    </div>
</div>

<div class="body">
    <div class="page-content park-listing">
        <div class="row margin-bottom"></div>

        <div class="row margin-bottom park-view-options">
            <div class="span4 park-view-option">
                <a href="<?= $this->listingPage('gallery') ?>" class="gray hover">
					<?php echo CHtml::image(Icon::PUBLIC_GALLERY_VIEW_ICON, 'Gallery View, Wooster&Stock') ?>
                    <span class="park-icon">Gallery View</span>
				</a>
            </div>
            <div class="span4 park-view-option">
                <a href="<?= $this->listingPage('map') ?>" class="gray hover">
                    <?php echo CHtml::image(Icon::PUBLIC_MAP_VIEW_ICON, 'Map View, Wooster&Stock') ?>
                    <span class="park-icon">Map View</span>
                </a>
            </div>
            <div class="span4 park-view-option">
                <a href="<?= $this->listingPage('list') ?>" class="gray hover">
                    <?php echo CHtml::image(Icon::PUBLIC_LIST_VIEW_ICON, 'List View, Wooster&Stock') ?>
                    <span class="park-icon">List View</span>
				</a>
            </div>
        </div>

        <div class="gray-big-separator"></div>

        <div class="row margin-bottom">
				<?php if ($view == 'map'):
					$dataProvider->pagination->pageSize = $model->count();
					$this->renderPartial("//MapView/default", array(
																								  'latitude' => '51.472016',
																								  'longitude' => '-0.088395',
																								  'type' => 'park',
																								  'multiple' => true,
																								  'mode'  => 'map',
																								  'properties' => $instructions,
																								  'parks' => $parks,
																								  'mapDim' => ['w'=>'100%','h'=>'950px'],
																								  'mapZoom' => 14,
																							 ));

				else:
				$this->widget('zii.widgets.CListView', array(
															'dataProvider'=> $dataProvider,
															'itemView'    => $view . 'View',
															'viewData' => ['allParks'=>$parks,'instructions'=>$instructions],
															'summaryText'  => '{count} Parks Found',
															'template'     => '{pager} {summary}{items}{pager} {summary}',
															'htmlOptions'  => ['class' => 'park'],
															'itemsCssClass' => 'item-listing-container',
															'pagerCssClass' => 'span8 listing-pager-container',
															'summaryCssClass' => 'span4 listing-summary-container',
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
													   ));
				endif;
				?>
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