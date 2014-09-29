<?php 
$config['name'] = 'Locations Module';
$config['version'] = LOCATIONS_VERSION;
$config['author'] = 'David McReynolds';
$config['company'] = 'Daylight Studio';
$config['license'] = 'Apache 2';
$config['copyright'] = '2014';
$config['author_url'] = 'http://www.thedaylightstudio.com';
$config['description'] = 'The FUEL Locations Module can be used display location information on a Google map.';
$config['compatibility'] = '1.0';
$config['instructions'] = '';
$config['permissions'] = array('locations_points', 'locations_zip_codes');
$config['migration_version'] = 0;
$config['install_sql'] = 'locations_install.sql';
$config['uninstall_sql'] = 'locations_uninstall.sql';
$config['repo'] = 'git://github.com/daylightstudio/FUEL-CMS-Locations-Module.git';