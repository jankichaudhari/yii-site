/**
 *
 * @param widgetId
 * @return NearestTransportWidget
 * @constructor
 */
var directionsDisplay;
var NearestTransportWidget = function (widgetId) {

	if (this == window) {
		NearestTransportWidget.widgets[widgetId] = NearestTransportWidget.widgets[widgetId] || new NearestTransportWidget(widgetId);
		return NearestTransportWidget.widgets[widgetId];
	}
	NearestTransportWidget.widgets[widgetId] = this;
	this.id = widgetId;
	this.map = null;
	this.Latitude = null;
	this.Longitude = null;
	this.clickId = null;
	this.events = {};

	this.addEvent = function (eventName, handler)
	{
		if (this.events.hasOwnProperty(eventName)) {
			this.events[eventName].push(handler);
		} else {
			this.events[eventName] = [handler];
		}
	};

	this.init = function(mapObj, thisLat, thisLng) {
		if(mapObj){
			this.map = mapObj;
			this.Latitude = thisLat;
			this.Longitude = thisLng;

			directionsDisplay = new google.maps.DirectionsRenderer();
			this.directionsService = new google.maps.DirectionsService();
			directionsDisplay.setMap(this.map);
		}

		//click event for map direction
		var thisObj = this;
		$(".mapDirectionsButton_"+this.id).each(function(){
			var thisId = $(this).attr("id");
			$(this).on('click',function(){
				thisObj.clickId = thisId;
				thisObj.onClick();
				thisObj.showRoute(thisId);
			});
		});
	};

	this.executeEvents = function (eventName, params)
	{
		if (!this.events.hasOwnProperty(eventName)) {
			return;
		}

		if (!params || !params.length) params = [];
		var events = this.events[eventName], l = events.length;
		for (var i = 0; i < l; i++) {
			events[i].apply(null, params);
		}
	};

	this.onBeforeClick = function(params) {
		this.executeEvents('onBeforeClick',(params));
	};

	this.onClick = function(params) {
		this.onBeforeClick();
		this.executeEvents('onClick',(params));
		this.onAfterClick();
	};

	this.onAfterClick = function(params) {
		this.executeEvents('onAfterClick',(params));
	};

	this.calcRoute = function(destLat,destLng,travelModeType) {
		if(this.map)
		{
			this.map.getStreetView().setVisible(false);
			var travelMode = google.maps.DirectionsTravelMode[travelModeType] || google.maps.DirectionsTravelMode.WALKING;
			this.request = {
				origin:this.Latitude+","+this.Longitude,
				destination:destLat+","+destLng,
				travelMode: travelMode
			};
			this.directionsService.route(this.request, function(result, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					directionsDisplay.setDirections(result);
				}
			});
		}
	};

	this.showRoute = function (thisId) {
		var lat = $('#'+thisId).attr('data-lat');
		var lng = $('#'+thisId).attr('data-lng');
		this.calcRoute(lat,lng,"WALKING");
	};
}
NearestTransportWidget.widgets = {};
function getWidgetById(id)
{
	return NearestTransportWidget(id);
}