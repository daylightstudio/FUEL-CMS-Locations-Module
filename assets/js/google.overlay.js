CustomOverlay.prototype = new google.maps.OverlayView();
function CustomOverlay(info, mapper) {
	this._info = info;
	this._mapper = mapper;
	this._map = mapper.map;
	this._marker = this._mapper.markers[this._info.i];
	this._$div = null;
	this._panels = null;
	this.setMap(this._map);
}
CustomOverlay.prototype.onAdd = function(){
	var div = document.createElement('DIV');
	div.className = 'info_win';
	var panes = this.getPanes();
	this._$div = jQuery(div);
	panes.floatPane.appendChild(div);
	jQuery(document).trigger('overlayAdded', {overlayId:this._info.id})
}

CustomOverlay.prototype.draw = function(){
	var info = this._info;
	var $div = this._$div;
	var swBound = new google.maps.LatLng(info.location.lat(), info.location.lng());
	var neBound = new google.maps.LatLng(info.location.lat(), info.location.lng());
	var bounds = new google.maps.LatLngBounds(swBound, neBound);
	var overlayProjection = this.getProjection();
	var sw = overlayProjection.fromLatLngToDivPixel(bounds.getSouthWest());
	var ne = overlayProjection.fromLatLngToDivPixel(bounds.getNorthEast());
	
	var width = $div.width();
	var height = $div.height();
	var xOffset = width/2 * -1;
	var yOffset = height/2 * -1;
	
	$div.css({
		left: (sw.x + xOffset) + 'px',
		top: (sw.y + yOffset) + 'px'
	});
	
	var html ='<div">';
	html += '<h3>' + info.name + '</h3>';
	html += '<address>' + info.address + '</address>';
	if (info.phone.length) html += '<div><a href="tel:' + info.phone + '">' + info.phone + '</a></div>';
	if (info.website.length) html += '<div><a href="' + info.website + '" target="_blank">' + info.website + '</a></div>';
	html += '</div>';

	$div.html(html);
	var _this = this;
	$div.click(function(){
		_this.hide();
	})

}

CustomOverlay.prototype.onRemove = function(){
	this._$div.remove();
	this._$div = null;
}

CustomOverlay.prototype.hide = function(){
	if (this._$div) {
		this._$div.fadeOut('fast');
	}
}

CustomOverlay.prototype.isShown = function(){
	if (this._$div) {
		return !this._$div.is(':hidden');
	}
	return false;
}


CustomOverlay.prototype.show = function(){
	if (!this.isShown()) {
		// set lng and lat
		var latLng = this._info.marker.getPosition();

		var panToLat = parseFloat(latLng.lat());
		var panToLng = parseFloat(latLng.lng());

		var panToLatLng = new google.maps.LatLng(panToLat, panToLng);
		this._map.panTo(panToLatLng);
		
		//this._mapper.panAndZoom(this._info.i)
		this._$div.fadeIn('fast');
	}
}

CustomOverlay.prototype.toggle = function(){
	if (this._$div) {
		if (!this.isShown()) {
			this.show();
		} else {
			this.hide();
		}
	}
}

CustomOverlay.prototype.toggleDOM = function(){
	if (this.getMap()) {
		this.setMap(null);
	} else {
		this.setMap(this._map);
	}
}

CustomOverlay.prototype.setZIndex = function(z){
	if (this._$div) {
		this._$div.css('zIndex', z);
	}
}

CustomOverlay.prototype.getZIndex = function(){
	if (this._$div) {
		return this._$div.css('zIndex');
	}
	return null;
}