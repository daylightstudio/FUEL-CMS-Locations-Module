<?php  // put your helper functions here

if ( ! function_exists('location_map'))
{
	function location_map($width = '100%', $height = 500, $zoom = 13, $id = 'mapiframe')
	{
		if (is_array($width))
		{
			extract($width);
		}
		return '<iframe src="'.site_url('locations/map?zoom='.$zoom).'" width="'.$width.'" height="'.$height.'" frameborder="0" style="border:0" class="map" name="mapiframe" id="'.$id.'"></iframe>';
	}
}