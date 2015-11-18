<?php
/*
 * @var TransportStations $dataProvider
 *
 */
?>

<style type="text/css">
	div.mapOptions{
		position: absolute;
		background: #FFFCE6;
		height: 60px;
		width: 200px;
		max-height: 100px;
		max-width: 200px;
		padding: 10px;
		bottom: -375px;
		left:755px;
		border-radius: 10px;
	}
	#mapInfoWin #title{
		color:#333333;
		font-weight: bold;
		font-size: 14px;
		margin-bottom: 5px;
	}
	#mapInfoWin a:link{
		text-decoration: none;
		font-size: 12px;
		font-weight: bold;
	}
	div.mapOptions img{
		top: -5px;
	}

	div.markerTip{
		position: absolute;
	}

	#markerDetails{
		position: relative;
		height: 390px;
		width: 470px;
		bottom: 354px;
		left:-110px;
		padding: 10px;
		border: none;
		border:none;
		border-radius: 10px;
	}

		/* Firefox */
		@-moz-document url-prefix()
		{
			#markerDetails { bottom: 348px; }
		}


</style>
<?php /** @var $clientScript CClientScript */
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile('https://maps.google.com/maps/api/js?sensor=false', CClientScript::POS_BEGIN);
$clientScript->registerScriptFile('/js/infobox.js', CClientScript::POS_BEGIN);

//////Default Latitude and Longitude /////////
$defaultLat = 51.50722;		//OR 51.471952345537105;
$defaultLong =  -0.12750;	//OR -0.08856922388076782
//////Default Latitude and Longitude /////////
?>
<div class="form wide"><fieldset>
	<div class="block-header">Transport Stations</div>
	<ul>
		<li>Right click on map to add new marker.</li>
		<li>Drag marker to change position of that marker. (when you drop the marker it will be saved automatically).</li>
		<li>Click on marker to change it's details or delete it.</li>
		<li>Under the map, there is a list of all available stations on the map. Click on any station to find and pop up detail box on the map.</li>
	</ul>
	<div id="stationMap" style="width:1410px; height: 900px;"></div>
	<script type="text/javascript">
		/*////////////////// MAP //////////////////*/

		var lat = <?= $defaultLat ?> ;
		var lng = <?= $defaultLong ?>;
		if (lat && lng) {
			var latlng = new google.maps.LatLng(lat, lng);
			var myOptions = {
				zoom      : 16,
				center    : latlng,
				mapTypeId : google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(
				document.getElementById("stationMap"),
				myOptions);

			var infowindow = new InfoBox({
				content                   : "",
				map                    : map,
				disableAutoPan         : false,
				maxWidth               : 0,
				alignBottom            : true,
				pixelOffset            : new google.maps.Size(-100, -40),
				zIndex                 : null,
				boxClass               : "mapOptions",
				closeBoxURL            : "/mapping/images/corners/red_close.png",
				closeBoxMargin         : "0px",
				pane                   : "floatPane",
				enableEventPropagation : false,
				infoBoxClearance       : "10px" });

			function markerOperations(thisMarker){

				google.maps.event.addListener(thisMarker, 'mouseover', (function(event) {
					if(thisMarker.title.length==0 || thisMarker.title=='<untitled>'){
						var thisId = thisMarker.id;
						var titleFormUrl = '<?php echo $this->createUrl('TransportStations/') ?>/ViewRecord/id/'+thisId;
						$.post(titleFormUrl , function(thisTitle) {
							thisMarker.title = thisTitle;
						});
					}
				}) );

				google.maps.event.addListener(thisMarker, 'click', (function(event) {
					var thisId = thisMarker.id;
					makeMarkerSelected(thisId);
					popUpMarkerBox(thisId,"markerOptions",true);
				}) );
				google.maps.event.addListener(thisMarker, 'dragend', (function(event) {
					var thisId = thisMarker.id;
					makeMarkerSelected(thisId);
					var newLat = event.latLng.lat();
					var newLng = event.latLng.lng();
					var thisPosition = new google.maps.LatLng(newLat,newLng);
					saveMarkerOnly(thisMarker,thisId,thisPosition);

					$('#markerRecords').attr("src", $('#markerRecords').attr("src"));

				}) );
			}

			var markers = [];

		}

		<?php foreach($dataProvider as $data){	?>
			var thisMarker;
			markers.push(thisMarker = new google.maps.Marker ({
				map:map,
				position:new google.maps.LatLng(<?= $data->latitude ?>, <?= $data->longitude ?>),
				draggable : true,
				title:'<?= addslashes($data->title); ?>',
				id : <?= $data->id ?>,
				showInfoBox : false,
				icon : "/images/sys/map-transport-icon.png"
			}));
			markerOperations(thisMarker);
		<?php } ?>

		google.maps.event.addListener(map, "rightclick", function (event)
		{
			var thisMarker;
			var thisPosition = event.latLng;

			markers.push(thisMarker = new google.maps.Marker ({
				map:map,
				position:thisPosition,
				title : '<untitled>',
				draggable : true,
				id : 0,
				showInfoBox : false,
				icon : "/images/sys/map-transport-icon.png"
			}));

			$('#markerRecords').attr("src", $('#markerRecords').attr("src"));

			/*Create a new marker in database on right click*/
			saveMarkerOnly(thisMarker,0,thisPosition);

			markerOperations(thisMarker);
		});
		/*////////////////// MAP //////////////////*/
	</script>
<iframe id="markerRecords" src="<?=  $this->createUrl('TransportStations/Index/recordsOnly/'.true) ?>" width="1410px" height="1000px" frameborder="0"></iframe>
</fieldset></div>

<script type="text/javascript">

	function makeMarkerSelected(thisId){
		for(var i=0; i<markers.length; i++){
			if(markers[i].id==thisId){
				markers[i].setIcon("/images/sys/map-transport-selected-icon.png");
			} else {
				markers[i].setIcon("/images/sys/map-transport-icon.png");
			}
		}
	}

	function getMarker(thisId){
		var thisMarker;
		for(var i=0; i<markers.length; i++){
			if(markers[i].id==thisId){
				thisMarker = markers[i];
			}
		}
		return thisMarker;
	}

	/*Delete Marker */
	function deleteMarker(markerId){
		var thisMarker = getMarker(markerId);
		var DeleteUrl = '<?php echo $this->createUrl('TransportStations/') ?>/Delete/id/'+markerId;
		$.post(DeleteUrl , function(data) {
			if(data.length!=0){
				thisMarker.setMap(null);
				infowindow.open(null,null);
				$('#markerRecords').attr("src", $('#markerRecords').attr("src"));
			}
		});
	}

	/*show saved message and save id for emty record */
	function saveMarkerOnly(thisMarker,thisId,thisPosition){
		var newRecord = false;
		if(thisId==0){
			newRecord = true;
		}
		var formUrl = '<?php echo $this->createUrl('TransportStations/') ?>/SavePosition/position/'+thisPosition+'/id/'+thisId;
		$.post(formUrl , function(data) {
			if(data.length!=0)
			{
				thisMarker.id = data;
				if(newRecord==true){
					//display infowindow to edit details
					var formType = '<?php echo $this->createUrl('TransportStations/') ?>/Update/id/'+data;
					var thisContent = '<iframe id="markerDetails" src="'+formType+'"  style="width:450px;height:390px;padding: 10px;border: none;border:none;"></iframe>';
					makeMarkerSelected(thisMarker.id);
					infowindow.setPosition(thisMarker.position);
					infowindow.setContent(thisContent);
					infowindow.open(map, thisMarker);
					markerDetails(thisMarker.id);
				} else {
					makeMarkerSelected(thisId);
					infowindow.setPosition(thisPosition);
					infowindow.setContent("Saved...");

					infowindow.open(map, thisMarker);
					setTimeout(function(){
//						infowindow.open(null,null);
						infowindow.close();
					},1000);
				}
			}
		});
	}
	/*show saved message save id for emty record */

	function closeMarkerDetails(id)
	{
		$('.mapOptions').hide();
		$('#markerRecords').attr("src", $('#markerRecords').attr("src"));
		return false;
	}

	function markerDetails(thisId){
		var thisMarker = getMarker(thisId);
		var formType = '<?php echo $this->createUrl('TransportStations/') ?>/Update/id/'+thisId;
		$(".mapOptions").append('<iframe id="markerDetails" src="'+formType+'"></iframe>');
		map.panTo(thisMarker.getPosition());
	}

	/* Pop up Infobox for marker details*/
	function popUpMarkerBox(thisId,thisContent,setPos){
		var formUrl = '<?php echo $this->createUrl('TransportStations/') ?>/ViewRecord/id/'+thisId;
		$.post(formUrl , function(data) {
			var content = "";
			if(thisContent=="markerOptions"){
				content = '<div id="mapInfoWin">' +
					'<div id="title">'+data+'</div>' +
					'<div><a onclick="markerDetails(this.id);" id="'+thisId+'" href="#">Edit</a></div>' +
					'<div><a href="#" onclick="deleteMarker('+thisId+');">Delete</a></div>' +
					'</div>';
			} else if(thisContent=="markerTitle") {
				content = '<div id="mapInfoWin">' +
					'<div id="title">'+data+'</div>' +
					'</div>';
			}
			var thisMarker = getMarker(thisId);
			infowindow.setPosition(thisMarker.position);
			infowindow.setContent(content);
			infowindow.open(map, thisMarker);
			if(setPos==true || setPos==undefined){
				map.panTo(thisMarker.getPosition());
			}
		});
	}
	/* Pop up Infobox */
</script>
