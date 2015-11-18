<style type="text/css">
	.markers, .markersInactive{
		width:250px;
		float: left;
		padding:3px;
		font-weight: bold;
	}
	.markers a{
		text-decoration: none;
		font-weight: bold;
		font-size: 11px;
	}
	.markersInactive a{
        text-decoration: none;
        font-weight: bold;
        font-size: 11px;
		color: #999;
	}
</style>

<?php
/**
 * @var TransportStations $dataProvider
 */
?>

<?php foreach($dataProvider as $data){
	$className = 'markers';
	if($data->statusId=='2'){
		$className = 'markersInactive';
	}
	?>
<div class="<?= $className ?>">
	<a href="#" id="<?= $data->id ?>">
		<?php foreach($data->transportTypes as $transportType){
			switch($transportType->id){
				case 1 : echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/tube.png", "tube");
				break;
				case 2 : echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/rail.png", "rail");
				break;
				case 3 : echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/dlr.png", "DLR");
				break;
				case 4 : echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/overground.png", "Overground");
				break;
				case 5 : echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/tram.png", "tram");
				break;
				case 6 : echo CHtml::image(Yii::app()->params['imgUrl'] . "/transportIcons/river-ferry.png", "river");
				break;
			}
		}
		?>
		<?= $title = (!empty($data->title)) ? CHtml::encode($data->title) : 'Untitled'; ?>
	</a>
</div>
<?php } ?>

<script type="text/javascript">
	$('.markers a, .markersInactive a').click(function(event){
		var thisId = $(this).attr('id');
		window.parent.popUpMarkerBox(thisId,'markerOptions');
	});
</script>