<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['locations'] = array(
		'locations/points' => 'Location Points',
		//'locations/zip_codes' => 'Zip Codes',
	);


/*
|--------------------------------------------------------------------------
| ADDITIONAL SETTINGS:
|--------------------------------------------------------------------------
*/

// This is not required but if you want to add it you can here: https://developers.google.com/maps/documentation/javascript/tutorial#api_key
$config['locations']['api_key'] = '';

// the JSON parameters to pass to the map
$config['locations']['gmap'] = array(
	'mapID'            => 'map_canvas',
	//'mapCenter'        => array('lat' => 45.5424364, 'lng' => -122.654422), // PDX default
	'defaultMapCenter' => array('lat' => 45.5424364, 'lng' => -122.654422),
	'imgPath'          => img_path(),
	'zoom'             => 14,
	'scrollwheel'      => FALSE,
	'disableDefaultUI' => TRUE,
	'mapTypeControl'   => FALSE,
	'marker'           => array('custom' => FALSE),

	// https://snazzymaps.com/ ... must be in array format so that json_encode will work... so this WILL NOT WORK: 
	// [ {"featureType": "poi", "stylers": [{"visibility": "simplified"} ] }, {"featureType": "road", "stylers": [{"visibility": "simplified"} ] }, {"featureType": "water", "stylers": [{"visibility": "simplified"} ] }, {"featureType": "transit", "stylers": [{"visibility": "simplified"} ] }, {"featureType": "landscape", "stylers": [{"visibility": "simplified"} ] }, {"featureType": "road.highway", "stylers": [{"visibility": "off"} ] }, {"featureType": "road.local", "stylers": [{"visibility": "on"} ] }, {"featureType": "road.highway", "elementType": "geometry", "stylers": [{"visibility": "on"} ] }, {"featureType": "water", "stylers": [{"color": "#84afa3"}, {"lightness": 52 } ] }, {"stylers": [{"saturation": -77 } ] }, {"featureType": "road"} ]
	'styles'           => array(),

	// example of what can be done with other parameters
	// 'mapCenter'        => array('lat' => '45.5200', 'lng' => '122.6819'), // PDX
	// 'defaultMapCenter' =>  array('lat' => '45.5200', 'lng' => '122.6819'), // If no data exists, this will be used
	// 'overview'         => false,
	// 'mapType'         => 'roadmap', // hybrid : 'HYBRID', roadmap: 'ROADMAP', satellite: 'SATELLITE', terrain: 'TERRAIN'
	// 'topZIndex: 100000',
	// 'panToXOffset' => 0,
	// 'panToYOffset' => 0,
	// 'forceGeoLocation' => false,
	// 'customOverlay' => 'CustomOverlay',
	// 'displayInfoWindows' => true,
	// 'displayTooltips' => true,
	// 'navigationControl' => true,
	// 'scaleControl' => true,
	// 'draggable' => true,
	// 'zoomControl' => true,
	// 'marker'           => array('custom' => true,
	// 							'img' => 'map_pin',
	// 							'size' => array('width' => 20, 'height' => 20), 
	// 							'origin' => array('x' => 0, 'y' => 0),
	// 							'anchor' => array('x' => 0, 'y' => 0),
	// 							'shape' => array(
	// 										'coord' => array(8,16,5,19,2,8,0,6,0,2,2,0,8,2,8,6,6,8),
	// 										'type' => 'poly'
	// 										),
	// 							'colors' => array(
	// 								'hotel' => '#333333',
	// 								'bars' => '#dc4b59',
	// 								'bus-tours' => '#cfe7f7',
	// 								'dc-sights' => '#ffff41',
	// 								'music-venues' => '#a0c1d7',
	// 								'restaurants' => '#ca68df',
	// 								'sporting-attractions' => '#e5828c',
	// 								'theaters' => '#ffff41',
	// 								'transportation' => '#e4c2f6'
	// 							)
	// 						)
	

	); 

// used to specify where the views are located
$config['locations']['views'] = array('map' => array('locations' => 'map'), 'listing' => array('locations' => 'listing'));
