<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/Base_module_model.php');
/*******************************************************************************
 *                ORIGINAL AUTHOR OF THE CLASS INFO BELOW
 *******************************************************************************

/*******************************************************************************
 *                ZIP Code and Distance Claculation Class
 *******************************************************************************
 *      Author:     Micah Carrick
 *      Email:      email@micahcarrick.com
 *      Website:    http://www.micahcarrick.com
 *
 *      File:       zipcode.class.php
 *      Version:    1.2.0
 *      Copyright:  (c) 2005 - Micah Carrick 
 *                  You are free to use, distribute, and modify this software 
 *                  under the terms of the GNU General Public License.  See the
 *                  included license.txt file.
 *
 *******************************************************************************
 *  VERION HISTORY:
 *      v1.2.0 [Oct 22, 2006] - Using a completely new database based on user
                                contributions which resolves many data bugs.
                              - Added sorting to get_zips_in_range()
                              - Added ability to include/exclude the base zip
                                from get_zips_in_range()
                              
 *      v1.1.0 [Apr 30, 2005] - Added Jeff Bearer's code to make it MUCH faster!
 
 *      v1.0.1 [Apr 22, 2005] - Fixed a typo :)
 
 *      v1.0.0 [Apr 12, 2005] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 
 *    A PHP Class and MySQL table to find the distance between zip codes and 
 *    find all zip codes within a given mileage or kilometer range.
 *      
 *******************************************************************************
*/

// Altered by David McReynolds of Daylight STudio on 3/29/2012 to integrate better with FUEL CMS

// constants for setting the $units data member
define('_UNIT_MILES', 'm');
define('_UNIT_KILOMETERS', 'k');

// constants for passing $sort to get_zips_in_range()
define('_ZIPS_SORT_BY_DISTANCE_ASC', 1);
define('_ZIPS_SORT_BY_DISTANCE_DESC', 2);
define('_ZIPS_SORT_BY_ZIP_ASC', 3);
define('_ZIPS_SORT_BY_ZIP_DESC', 4);

// constant for miles to kilometers conversion
define('_M2KM_FACTOR', 1.609344);


class Zip_codes_model extends Base_module_model {
	public $last_time = 0; // last function execution time (debug info)
	public $units = _UNIT_MILES; // miles or kilometers
	public $decimals = 2; // decimal places for returned distance
  
	function __construct()
	{
		parent::__construct('zip_codes'); // table name
	}
	
	function get_distance($zip1, $zip2)
	{

		// returns the distance between to zip codes.  If there is an error, the 
		$this->chronometer();         // start the clock

		if ($zip1 == $zip2) return 0; // same zip code means 0 miles between. :)

		// get details from database about each zip and exit if there is an error
		$details1 = $this->get_zip_point($zip1);
		$details2 = $this->get_zip_point($zip2);
		
		if ($details1 == FALSE)
		{
			$this->add_error("No details found for zip code: $zip1");
			return false;
		}

		if ($details2 == FALSE)
		{
			$this->add_error("No details found for zip code: $zip2");
			return false;
		}     

		// calculate the distance between the two points based on the lattitude
		// and longitude pulled out of the database.

		$miles = $this->calculate_mileage($details1['lat'], $details2['lat'], $details1['lon'], $details2['lon']);

		$this->last_time = $this->chronometer();

		if ($this->units == _UNIT_KILOMETERS)
		{
			return round($miles * _M2KM_FACTOR, $this->decimals);
		}
		else
		{
			return round($miles, $this->decimals); // must be miles
		}
	}

	function get_zip_point($zip)
	{
		// This function pulls just the lattitude and longitude from the
		// database for a given zip code.
		$this->db->select('lat, lon');
		$where['zip_code'] = $zip;
		$data = $this->find_one_array($where);
		return $data;
   }

	function calculate_mileage($lat1, $lat2, $lon1, $lon2)
	{

		// used internally, this function actually performs that calculation to
		// determine the mileage between 2 points defined by lattitude and
		// longitude coordinates.  This calculation is based on the code found
		// at http://www.cryptnet.net/fsp/zipdy/

		// Convert lattitude/longitude (degrees) to radians for calculations

		// normalize them if they are strings
		$lat1 = (double) $lat1;
		$lat2 = (double) $lat2;
		$lon1 = (double) $lon1;
		$lon2 = (double) $lon2;
		
		$lat1 = deg2rad($lat1);
		$lon1 = deg2rad($lon1);
		$lat2 = deg2rad($lat2);
		$lon2 = deg2rad($lon2);

		// Find the deltas
		$delta_lat = $lat2 - $lat1;
		$delta_lon = $lon2 - $lon1;

		// Find the Great Circle distance 
		$temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);
		$distance = 3956 * 2 * atan2(sqrt($temp),sqrt(1-$temp));

		return $distance;
	}

	function get_zips_in_range($zip, $range = 5, $sort = 1, $include_base = TRUE)
	{

		// returns an array of the zip codes within $range of $zip. Returns
		// an array with keys as zip codes and values as the distance from 
		// the zipcode defined in $zip.

		$this->chronometer();                     // start the clock

		$details = $this->get_zip_point($zip);  // base zip details
		
		if ($details == FALSE) 
		{
			return FALSE;
		}

		// This portion of the routine  calculates the minimum and maximum lat and
		// long within a given range.  This portion of the code was written
		// by Jeff Bearer (http://www.jeffbearer.com). This significanly decreases
		// the time it takes to execute a query.  My demo took 3.2 seconds in 
		// v1.0.0 and now executes in 0.4 seconds!  Greate job Jeff!

		// Find Max - Min Lat / Long for Radius and zero point and query
		// only zips in that range.
		$lat_range = $range/69.172;
		$lon_range = abs($range/(cos($details['lat']) * 69.172));
		$min_lat = number_format($details['lat'] - $lat_range, "4", ".", "");
		$max_lat = number_format($details['lat'] + $lat_range, "4", ".", "");
		$min_lon = number_format($details['lon'] - $lon_range, "4", ".", "");
		$max_lon = number_format($details['lon'] + $lon_range, "4", ".", "");

		$return = array();    // declared here for scope

		$sql = "SELECT zip_code, lat, lon FROM zip_codes ";
		if (!$include_base) $sql .= "WHERE zip_code <> '$zip' AND ";
		else $sql .= "WHERE "; 
		$sql .= "lat BETWEEN '$min_lat' AND '$max_lat' 
		AND lon BETWEEN '$min_lon' AND '$max_lon'";
		
		$query = $this->db->query($sql);
		//$this->db->debug_query();
		$rows = $query->result_array();

		foreach($rows as $row)
		{
			// loop through all 40 some thousand zip codes and determine whether
			// or not it's within the specified range.
			
			$dist = $this->calculate_mileage($details['lat'],$row['lat'],$details['lon'],$row['lon']);
			if ($this->units == _UNIT_KILOMETERS)
			{
				$dist = $dist * _M2KM_FACTOR;
			}
			if ($dist <= $range)
			{
				$return[str_pad($row['zip_code'], 5, "0", STR_PAD_LEFT)] = round($dist, $this->decimals);
			}
		}

		// sort array
		switch($sort)
		{
			case _ZIPS_SORT_BY_DISTANCE_ASC:
				asort($return);
				break;

			case _ZIPS_SORT_BY_DISTANCE_DESC:
				arsort($return);
				break;

			case _ZIPS_SORT_BY_ZIP_ASC:
				ksort($return);
				break;

			case _ZIPS_SORT_BY_ZIP_DESC:
				krsort($return);
				break; 
		}

		$this->last_time = $this->chronometer();

		if (empty($return))
		{
			return FALSE;
		}
		return $return;
	}
	
	function chronometer()
	{
		// chronometer function taken from the php manual.  This is used primarily
		// for debugging and anlyzing the functions while developing this class.  

		$now = microtime(TRUE);  // float, in _seconds_
		$now = $now + time();
		$malt = 1;
		$round = 7;

		if ($this->last_time > 0) 
		{
			/* Stop the chronometer : return the amount of time since it was started,
			in ms with a precision of 3 decimal places, and reset the start time.
			We could factor the multiplication by 1000 (which converts seconds
			into milliseconds) to save memory, but considering that floats can
			reach e+308 but only carry 14 decimals, this is certainly more precise */

			$retElapsed = round($now * $malt - $this->last_time * $malt, $round);

			$this->last_time = $now;

			return $retElapsed;
		}
		else
		{
			// Start the chronometer : save the starting time

			$this->last_time = $now;

			return 0;
		}
	}
}

class Zip_code_model extends Base_module_record {
	
}
