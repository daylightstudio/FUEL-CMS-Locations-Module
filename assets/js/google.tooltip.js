//http://medelbou.wordpress.com/2012/02/03/creating-a-tooltip-for-google-maps-javascript-api-v3/
Tooltip.prototype = new google.maps.OverlayView();

function Tooltip(info, mapper) {
	this._info = info;
	this._mapper = mapper;
	this._map = mapper.map;
	this._marker = this._mapper.markers[this._info.i];
	this._$div = null;
	this.setMap(this._map);
	
	var _this = this;

	// Show tooltip on mouseover event.
	google.maps.event.addListener(this._marker, 'mouseover', function() {
		_this.show();
	});
	
	// Hide tooltip on mouseout event.
	google.maps.event.addListener(this._marker, 'mouseout', function() {
		_this.hide();
	});
}
Tooltip.prototype.onAdd = function(){
	var div = document.createElement('DIV');
	div.style.position = "absolute";
	div.style.display = "none";
	div.className += " marker_tooltip";

	var content = this._info.name;
	div.innerHTML = content;
	
	var panes = this.getPanes();
	this._$div = jQuery(div);
	panes.floatPane.appendChild(div);
}

Tooltip.prototype.draw = function(){
	// Position the overlay. We use the position of the marker
	// to peg it to the correct position, just northeast of the marker.
	// We need to retrieve the projection from this overlay to do this.
	var overlayProjection = this.getProjection();
	// Retrieve the coordinates of the marker
	// in latlngs and convert them to pixels coordinates.
	// We'll use these coordinates to place the DIV.
	var ne = overlayProjection.fromLatLngToDivPixel(this._marker.getPosition());
	// Position the DIV.

	this._$div.css('left', Math.floor(ne.x) + 'px');
	this._$div.css('top', Math.floor(ne.y) + 'px');
	
}

Tooltip.prototype.onRemove = function(){
	this._$div.remove();
	this._$div = null;
}

Tooltip.prototype.hide = function(){
	if (this._$div) {
		this._$div.fadeOut('fast');
	}
}

Tooltip.prototype.isShown = function(){
	if (this._$div) {
		return !this._$div.is(':hidden');
	}
	return false;
}


Tooltip.prototype.show = function(){
	if (!this.isShown()) {
		if (this._$div) {
			this._$div.fadeIn('fast');
		}
	}
}
