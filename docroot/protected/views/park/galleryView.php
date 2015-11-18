<?php
/**
 * @var $this ParkController
 * @var $data Place
 */
$postcode = $data->addressId && $data->location->postcode ? ucfirst($data->location->postcode) : "";
$detailPageUrl = $this->detailPage($data->id);
?>
<div class="gallery-view">
<div class="span4 top-border-orange detail-box with-shadow">
	<div class="info">
		<h3 class="title">
			<a href="<?php echo $detailPageUrl; ?>" title=""><?php echo $data->title; ?></a>
		</h3>
		<div class="block subtitle">
			<?php echo $data->location->city ? $data->location->city . ", " . $postcode : $postcode ?>
		</div>
		<div class="block strapline">
			<?php
				$strapline = CHtml::encode($data->strapline);
				$thisStrapline = '';
				if (strlen($strapline) > 70) {
					$thisStrapline = substr($strapline, 0, 70);
					$temp = strrpos($thisStrapline, ' ');
					$thisStrapline = substr($strapline, 0, $temp) . '...';
				} else {
					$thisStrapline = $strapline;
				}
			?>
			<?php echo $thisStrapline; ?>

		</div>
	</div>

	<div class="thumbnail">
	<?php
		if ($data->mainViewImageId)	{
			$titleTag = $data->mainViewImage->caption ? $data->mainViewImage->caption : $data->title ;
			$altTag = $data->mainViewImage->caption ? $data->title . ", " . $postcode . $data->mainViewImage->caption ."; by Wooster & Stock" : $data->title . ", " . $postcode ." by Wooster & Stock" ;
			echo '<a href="'.$detailPageUrl.'" title="'.$titleTag.'">'.
					CHtml::image(
						Yii::app()->params['imgUrl'] . "/Place/" . $data->id . "/" . $data->mainViewImage->recordType . '/' . $data->mainViewImage->mediumName,
						$altTag,
						array('title'=>$titleTag,'width'=>'310')
					).
				'</a>';
		}
		else{
			echo CHtml::image(Icon::NO_IMAGE_AVAILABLE,"No Image Available, Wooster&Stock",array('width'=>'310'));
		}
	?>
	</div>

	<?php if($data->typeId == 2) { ?>
	<div class="secretSpace"><?php echo CHtml::image((Yii::app()->params['imgUrl'] . "/secretSpace.png"),"Secret Space");  ?></div>
	<?php } ?>
</div>
</div>