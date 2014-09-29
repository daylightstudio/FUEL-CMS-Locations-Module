<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 */

// ------------------------------------------------------------------------

/**
 * Fuel Locations object 
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 */

// --------------------------------------------------------------------

class Fuel_locations extends Fuel_advanced_module {
	
	public $name = "locations"; // the folder name of the module
	
	/**
	 * Constructor - Sets preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the backup object
	 *
	 * Accepts an associative array as input, containing backup preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params)
	{
		parent::initialize($params);
		$this->set_params($this->_config);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of locations
	 *
	 * @access	public
	 * @param	array	where conditions. Can be either a string, (will look at any corresponding fuel categories), integer (location ID) or an array
	 * @return	array
	 */	
	function search($where = array(), $return_type = 'object')
	{
		if (!empty($where))
		{

			if (!empty($where['zip']))
			{
				$this->CI->load->module_model('locations', 'zip_codes_model');
				$radius = $this->CI->input->get('radius');
				if (!$radius)
				{
					$radius = 5;
				}
				$zips = $this->CI->zip_codes_model->get_zips_in_range($this->CI->input->get('zip', TRUE), $radius);

				$where['zip'] = array();
				if (!empty($zips))
				{
					$where['zip'] = array_keys($zips);
				}
				else
				{
					// set it to empty
					$where['zip'] = array('0');
				}
			}


		}
		$model = $this->model('locations');
		$model->db()->select('locations.*, fuel_categories.slug as category, fuel_categories.slug as category_slug');
		$i = 1;
		if ($return_type == 'array')
		{
			$data = $this->locations_model->find_all_array($where);	
			foreach($data as $k => $v)
			{
				$data[$k]['markerText'] = "{$i}";
				$i++;	
			}

		}
		else
		{
			$data = $this->locations_model->find_all($where);
			foreach($data as $k => $v)
			{
				$data[$k]->markerText = $i;
				$i++;	
			}
		}

		$i = 1;

		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an JSON encoded GMap parameters
	 *
	 * @access	public
	 * @return	string
	 */	
	function gmap_params()
	{
		$params = $this->fuel->locations->config('gmap');
		return json_encode($params, JSON_PRETTY_PRINT);
	}
	
}