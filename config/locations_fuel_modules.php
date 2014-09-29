<?php 

$config['modules']['locations_points'] = array(
	'preview_path' => '', // put in the preview path on the site e.g products/{slug}
	'model_location' => 'locations', // put in the advanced module name here
	'exportable' => TRUE,
	'model_name' => 'locations_model',
	'module_uri' => 'locations/points',
	'module_name' => 'Location Points',
	'filters' => array('fuel_categories:id' => array('type' => 'select', 'label' => 'Category', 'model' => 'fuel_categories', 'model_params' => array('id', 'name', array('context' => 'locations')), 'first_option' => 'Select a category...'))
);

$config['modules']['locations_zip_codes'] = array(
	'preview_path' => '', // put in the preview path on the site e.g products/{slug}
	'model_location' => 'locations', // put in the advanced module name here
	'model_name' => 'zip_codes_model',
	'module_uri' => 'locations/zip_codes',
	'module_name' => 'Zip Codes',
	'hidden' => TRUE,
);
