<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>Map</title>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css" media="screen">
	html, body { height: 100%; }
</style>

<?=css('google_map', LOCATIONS_FOLDER)?>
<?=css($css)?>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?=$this->fuel->locations->config('api_key')?>"></script>

<?=js('jquery')?>
<?=js('google.mapper, google.overlay, google.tooltip, StyledMarker', LOCATIONS_FOLDER)?>
<?=js($js)?>

<script type="text/javascript" charset="utf-8">

var basePath = '<?=site_url();?>';

$(window).load(function(){ 
	var mapParams = <?=$this->fuel->locations->gmap_params()?>;
	<?php if (!empty($zoom)) : ?>mapParams.zoom = <?=$zoom?>;<?php endif; ?>
	<?php if (!empty($type)) : ?>mapParams.mapType = '<?=$type?>';<?php endif; ?>
	<?php if (!empty($startpoint)) : ?>mapParams.mapCenter = '<?=$startpoint?>';<?php endif; ?>

	var map = new GoogleMapper(mapParams);
	map.createMap();
	map.createMarkers(<?php echo $data ?>);
})	

</script>

</head>
<body class="googlemap">
<div id="map_canvas" style="width: 100%; height: 100%;"></div>
</body>
</html>