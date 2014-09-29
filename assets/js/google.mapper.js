function GoogleMapper(o)
{
	this.map = null;
	this.options = {
		mapID: 'map_canvas',
		mapCenter: {lat: 0, lng: 0}, // used no matter where the data is displayed
		defaultMapCenter: {lat: 0, lng: 0}, // used when no data exists
		imgPath: '',
		overview: false,
		mapType: 'roadmap',
		topZIndex: 100000,
		panToXOffset: 0,
		panToYOffset: 0,
		zoom: 7,
		useDebug: false,
		forceGeoLocation: false,
		customOverlay: CustomOverlay,
		displayInfoWindows: true,
		displayTooltips: true,

		scrollwheel: true,
    	navigationControl: true,
    	mapTypeControl: true,
    	scaleControl: true,
    	draggable: true,
 		disableDefaultUI: false,
 		zoomControl: true,

 		styles: [],

		// marker: {
		// 	custom: false,
		// 	// colors: {},
		// 	// img: 'map_pin',
		// 	// size: { width: 20, height: 34 },
		// 	// origin: { x: 0, y: 0},
		// 	// anchor: { x: 10, y: 34},
		// 	shape: {
		// 		coord: [13,0,15,1,16,2,17,3,18,4,18,5,19,6,19,7,19,8,19,9,19,10,19,11,19,12,19,13,18,14,18,15,17,16,16,17,15,18,14,19,14,20,13,21,13,22,12,23,12,24,12,25,12,26,11,27,11,28,11,29,11,30,11,31,11,32,11,33,8,33,8,32,8,31,8,30,8,29,8,28,8,27,8,26,7,25,7,24,7,23,6,22,6,21,5,20,5,19,4,18,3,17,2,16,1,15,1,14,0,13,0,12,0,11,0,10,0,9,0,8,0,7,0,6,1,5,1,4,2,3,3,2,4,1,6,0,13,0],
		// 	    type: 'poly'
		// 		// coord: [8,16,5,19,2,8,0,6,0,2,2,0,8,2,8,6,6,8],
		// 		// type: 'poly'
		// 	},
		// 	shadow: {
		// 		img: 'map_pin_shadow',
		// 		size: { width: 37, height: 34 },
		// 		origin: { x: 0, y: 0},
		// 		anchor: { x: 10, y: 34}
				
		// 	}
		// }
	}
	this.points = [];
	this.markers = [];
	this.infoWindows = [];
	this.tooltips = [];
	this.tooltips = [];
 	this.geocoder = new google.maps.Geocoder();
	this.overlaysLoaded = 0;
	this.numPoints = 0;
	this.setOptions(o);

	var _this = this;

	jQuery(document).bind('overlayAdded', function(){
			_this.overlaysLoaded++;
			if (_this.overlaysLoaded == _this.numPoints){
				parent.jQuery(top.window).trigger('mapInited');
				
			}
		})
	//this.init(o);

}

GoogleMapper.prototype = {
	
	setOptions : function(o){
		this.options = jQuery.extend(true, this.options, o);
	},

	createMap : function(points, options){
		this.setOptions(options);
		
		var validMapTypes = {hybrid : 'HYBRID', roadmap: 'ROADMAP', satellite: 'SATELLITE', terrain: 'TERRAIN'};
		if (typeof(validMapTypes[this.options.mapType.toUpperCase()]) != 'undefined'){
			this.options.mapType = eval('google.maps.MapTypeId.' + validMapTypes[this.options.mapType]);
		}

		var mapOptions = { 

			scrollwheel: this.options.scrollwheel,
    		navigationControl: this.options.navigationControl,
    		mapTypeControl: this.options.mapTypeControl,
    		scaleControl: this.options.scaleControl,
    		draggable: this.options.draggable,
    		zoomControl: this.options.zoomControl,

			zoom: this.options.zoom,
			center: new google.maps.LatLng(this.options.mapCenter.lat, this.options.mapCenter.lng),
			disableDefaultUI: this.options.disableDefaultUI,
			mapTypeId: this.options.mapType,
			scaleControl: true,
			overviewMapControl: this.options.overview,
			overviewMapControlOptions: {
			    opened: true
			  },
			styles: [ {"featureType": "poi", "stylers": [{"visibility": "simplified"} ] }, {"featureType": "road", "stylers": [{"visibility": "simplified"} ] }, {"featureType": "water", "stylers": [{"visibility": "simplified"} ] }, {"featureType": "transit", "stylers": [{"visibility": "simplified"} ] }, {"featureType": "landscape", "stylers": [{"visibility": "simplified"} ] }, {"featureType": "road.highway", "stylers": [{"visibility": "off"} ] }, {"featureType": "road.local", "stylers": [{"visibility": "on"} ] }, {"featureType": "road.highway", "elementType": "geometry", "stylers": [{"visibility": "on"} ] }, {"featureType": "water", "stylers": [{"color": "#84afa3"}, {"lightness": 52 } ] }, {"stylers": [{"saturation": -77 } ] }, {"featureType": "road"} ]
			};
			
		//mapOptions.center = results[0].geometry.location;
		var mapElem = document.getElementById(this.options.mapID);
		if (!mapElem) return;

		var map = new google.maps.Map(mapElem, mapOptions);
		this.map = map;
		if (points && points.length){
			//this.createMarkers(points);
		}

	
	},

	
	createMarkers : function(points){

		// clear any overlays first
		this.clearOverlays();
		
		var _this = this;

		this.points = points;

		if (!this.points.length) {
			this.map.panTo(this.options.defaultMapCenter);
			return;
		}
		var firstPoint = null;
		for (var i in points){
			if (!firstPoint) firstPoint = points[i];
			var id = points[i].id;
			points[i].i = i;

			// if we have the lat and lng then we don't need to geo lookup
			if (!this.forceGeoLocation && Math.abs(points[i].latitude) > 0 && Math.abs(points[i].longitude) > 0){
				points[i].address = this.createAddressString(points[i]);
				points[i].location = new google.maps.LatLng(points[i].latitude, points[i].longitude);
				this.markers[i] = this.createMarker(points[i].location, points[i].category, points[i].markerText);
				this.points[i].marker = this.markers[i];
				if (this.options.displayInfoWindows){
					this.infoWindows[i] = this.createInfoWindow(points[i]);
					this.attachMarkerListener(i);
				}
				if (this.options.displayTooltips){
					this.tooltips[i] = this.createTooltip(points[i]);
				}
				this.debug('NAME: ' + points[i].name + ' GEO LOCATED: ' + points[i].address + ' LAT: ' + points[i].location.lat() + ' LNG: ' + points[i].location.lng());

			// otherwise we geo lookup
			} else {
				this.geoLocate(i);
			}
			this.numPoints++;
		}

		// now center map on first spot
		if (firstPoint.location && this.map && this.options.mapCenter.lat == 0) {
			this.map.panTo(firstPoint.location);
		}
	},
	
	geoLocate : function(i, callback){
		var _this = this;
		this.points[i].address = this.createAddressString(this.points[i]);
		this.geocoder.geocode( { 'address': this.points[i].address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {

				// store the location info for later
				_this.points[i].location = results[0].geometry.location;
				
				_this.markers[i] = _this.createMarker(_this.points[i].location, _this.points[i].category, _this.points[i].markerText);
				_this.points[i].marker = _this.markers[i];
				_this.debug('LOOKED UP! ' + 'NAME: ' + _this.points[i].name + ' GEO LOCATED: ' + _this.points[i].address + ' LAT: ' + _this.points[i].location.lat() + ' LNG: ' + _this.points[i].location.lng());

				_this.infoWindows[i] = _this.createInfoWindow(_this.points[i]);
				// now center map on first spot
				if (i == 0) _this.map.panTo(_this.points[0].location);

				_this.attachMarkerListener(i);
				
			} else {
				_this.debug("Geocode was not successful for the following reason: " + status)
			}
			
			if (callback){
				callback();
			}
		});
	},
	
	createAddressString : function(point){
		var str = '';
		if (point.street){
			str += point.street;
		} else {
			str += point.address;
		}
		if (point.city){
			str + ', ' + point.city ;
		}
		if (point.state){
			str + ', ' + point.state;
		}
		return str;
	},

	createMarker : function(location, type, text){
		if (this.options.marker.custom){
			return this.createMarkerCustom(location, type, text);
		} else {

			var color = '#f7584c';
			if (typeof(this.options.marker.colors) != 'undefined'){
				var colors = this.options.marker.colors;
				if (!text) text = '';
				// default color
				if (colors && typeof(colors[type]) != 'undefined'){
					color = colors[type];
				}
			}
			return new StyledMarker({styleIcon:new StyledIcon(StyledIconTypes.MARKER,{color:color,text:text}),position:location,map:this.map});
	//		var styleMaker1 = new StyledMarker({styleIcon:new StyledIcon(StyledIconTypes.MARKER,{color:"00ff00",text:"A"}),position:location,map:this.map});
		//	var styleMaker3 = new StyledMarker({styleIcon:new StyledIcon(StyledIconTypes.MARKER,{color:"0000ff"}),position:location,map:this.map});
		}
	},
	
	createMarkerCustom : function(location, type){
		var config = this.options.marker;
		var image = null;
		if (config.img.length){
			var img = (type) ? config.img + '_' + type + '.png' : config.img + '.png';
			img = this.options.imgPath + img;
			
			image = {
				url: img, 
				size: new google.maps.Size(config.size.width, config.size.height),
				origin: new google.maps.Point(config.origin.x, config.origin.y),
				anchor: new google.maps.Point(config.anchor.x, config.anchor.y),
				scaledSize: new google.maps.Size(config.size.width, config.size.height)
			}
			
			var shadow = null;
			if (config.shadow){
				var shadowImg = this.options.imgPath + config.shadow.img + '.png';
				
				shadow = {
					url: shadowImg, 
					size: new google.maps.Size(config.shadow.size.width, config.shadow.size.height),
					origin: new google.maps.Point(config.shadow.origin.x, config.shadow.origin.y),
					anchor: new google.maps.Point(config.shadow.origin.x, config.shadow.origin.y),
					scaledSize: new google.maps.Size(config.shadow.size.width, config.shadow.size.height)
				}
			}
		}
		
		
		// Shapes define the clickable region of the icon.
		// The type defines an HTML <area> element 'poly' which
		// traces out a polygon as a series of X,Y points. The final
		// coordinate closes the poly by connecting to the first
		// coordinate.
		var markerConfig = {
			position: location,
			map: this.map
		};
		if (image){
			markerConfig.icon = image;
		}
		if (shadow){
			markerConfig.shadow = shadow;
		}
		if (config.shape){
			markerConfig.shape = config.shape;
		}

		return new google.maps.Marker(markerConfig);
	},
	
	createInfoWindow : function(info){
		if (typeof(this.options.customOverlay) == 'string'){
			this.customOverlay = eval(this.customOverlay);
		}
		var overlay = new this.options.customOverlay(info, this);
		return overlay;
	},

	createTooltip : function(info){
		var overlay = (Tooltip != undefined) ? new Tooltip(info, this) : new google.maps.OverlayView();
		return overlay;
	},
	
	panAndZoom : function(i){
		this.map.setZoom(this.options.zoom);
		var marker = this.markers[i];
		var infoWindow = this.infoWindows[i];
		var topZ = this.options.topZIndex;

		// set the zIndex of marker and infoWindow
		if (this.activeMarker && this.activeMarker != marker){
			this.activeMarker.setZIndex(topZ - 1);
		}
		if (this.activeInfoWindow && this.activeInfoWindow != infoWindow) {
			this.activeInfoWindow.setZIndex(topZ - 1);
			this.activeInfoWindow.hide();
		}
		marker.setZIndex(topZ);
		infoWindow.setZIndex(topZ);

		// set lng and lat
		var latLng = marker.getPosition();

		var panToLat = parseFloat(latLng.lat()) + this.options.panToYOffset;
		var panToLng = parseFloat(latLng.lng()) + this.options.panToXOffset;

		var panToLatLng = new google.maps.LatLng(panToLat, panToLng);
		this.map.panTo(panToLatLng);

		infoWindow.show();
		this.activeMarker = marker;
		this.activeInfoWindow = infoWindow;
	},

	attachMarkerListener : function(i){
		var _this = this;
		var marker = this.markers[i];
		var infoWindow = this.infoWindows[i];

		var topZ = this.options.topZIndex;
		google.maps.event.addListener(marker, 'click', function(e) {
			if (_this.activeInfoWindow && _this.activeInfoWindow != infoWindow){
				_this.activeInfoWindow.hide();
				_this.infoWindowShown = false;
				_this.activeMarker.setZIndex(topZ - 1);
				_this.activeInfoWindow.setZIndex(topZ - 1);
			}
		
			if (infoWindow.isShown && infoWindow.isShown()){
				infoWindow.hide();
				_this.infoWindowShown = false;
			} else if (infoWindow.show) {
				infoWindow.show();
				_this.infoWindowShown = true;
			}
			marker.setZIndex(topZ);
			if (infoWindow.setZIndex) infoWindow.setZIndex(topZ);
			_this.activeInfoWindow = infoWindow;
			_this.activeMarker = marker;
		});
		
		google.maps.event.addListener(marker, 'mouseover', function(e) {
			
		});
		
		google.maps.event.addListener(marker, 'mouseout', function(e) {
			
		});
	},
	
	clearOverlays : function(){
		for (var n in this.markers){
			this.markers[n].setMap(null);
			if (this.options.displayInfoWindows){
				this.infoWindows[n].setMap(null);
			}
			if (this.options.displayTooltips){
				this.tooltips[n].setMap(null);
			}
		}
		this.markers = [];
		this.infoWindows = [];
		this.tooltips = [];
	},
	
	debug : function(msg){
		if (this.options.useDebug && window.console){
			console.log(msg);
		}
	}
	
}
