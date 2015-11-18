<?php
/**
 * @var $this ParkController
 * @var $data Place
 * @var $instruction Deal
 * @var $allParks Place[]
 * @var $instructions Deal[]
 */
$postcode = $data->addressId && $data->location->postcode ? ucfirst($data->location->postcode) : "";
$detailPageUrl = '/park/' . $data->id;
?>

<div class="list-view">
    <div class="span8 detail-box with-shadow listings">
	<div class="row">
        <div class="span4 left-border-orange thumbnail">
			<?php
			if ($data->mainViewImageId)	{
				$titleTag = $data->mainViewImage->caption ? $data->mainViewImage->caption : $data->title ;
				$altTag = $data->mainViewImage->caption ? $data->title . ", " . $postcode . $data->mainViewImage->caption ."; by Wooster & Stock" : $data->title . ", " . $postcode ." by Wooster & Stock" ;
				echo '<a href="'.$detailPageUrl.'" title="'.$titleTag.'">'.
					CHtml::image(
						Yii::app()->params['imgUrl'] . "/Place/" . $data->id . "/" . $data->mainViewImage->recordType . '/' . $data->mainViewImage->mediumName,
						$altTag,
						array('width'=>'310','title'=>$titleTag)
					).
					'</a>';

			}
			else{
				echo CHtml::image(Icon::NO_IMAGE_AVAILABLE,"No Image Available, Wooster&Stock",array('width'=>'308'));
			}
			?>
        </div>

        <div class="info">
			<div class="block titles">
			<div class="inner-block">
				<h3 class="title">
					<a href="<?php echo $detailPageUrl ?>"><?php echo $data->title; ?></a>
				</h3>
				<div class="subtitle big">
					<?php echo $data->location->city ? $data->location->city . ", " . $postcode : $postcode ?>
				</div>
			</div>
			</div>
            <div class="block strapline">
				<div class="inner-block">
					<?php echo Util::strapString(CHtml::encode($data->strapline),0,100);?>
				</div>
            </div>
        </div>

		<?php if($data->typeId == 2) { ?>
        <div class="secretSpace"><?php echo CHtml::image((Yii::app()->params['imgUrl'] . "/secretSpace.png"),"Secret Space");  ?></div>
		<?php } ?>

    </div>
    </div>

    <div class="span4 map with-shadow additional-widgets">
		<?php
		if ($data->addressId && $data->location->latitude && $data->location->longitude):
			$this->renderPartial("//MapView/default", array(
																							 'id' => $data->id,
																							 'latitude' => $data->location->latitude,
																							 'longitude' => $data->location->longitude,
																							 'type' => 'park',
																							 'mode'  => 'map',
																							 'showBox' => false,
																							 'properties' => $instructions,
																							 'parks' => $allParks,
																							 'mapDim' => ['w'=>'312px','h'=>'207px'],
																							 'mapZoom' => 14,
																						));
		endif;
		?>
    </div>

</div>