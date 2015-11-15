<?php
class Locations extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	function map()
	{
		$this->load->model('locations_model');

		$vars['startpoint'] = ($this->input->get('startpoint')) ? $this->input->get('startpoint', TRUE) : 0;

		$gmap_config = $this->fuel->locations->config('gmap');
		$vars['zoom'] = ($this->input->get('zoom')) ? $this->input->get('zoom', TRUE) : (!empty($gmap_config['zoom'])) ? $gmap_config['zoom'] : 16;
		$vars['type'] = ($this->input->get('type')) ? $this->input->get('type', TRUE) : (!empty($gmap_config['mapType'])) ? $gmap_config['mapType'] : 'roadmap';
		
		$pagevars = $this->fuel->pagevars->retrieve('locations/map');
		$vars['js'] = (!empty($pagevars['js'])) ? $pagevars['js'] : '';
		$vars['css'] = (!empty($pagevars['css'])) ? $pagevars['css'] : '';
		$locations = $this->_get_data('array');
		$vars['data'] = json_encode($locations);
		$this->_render('map', $vars);
	}

	function listing()
	{
		$vars['locations'] = $locations = $this->_get_data();
		$this->_render('listing', $vars);
	}

	function _get_data($return_type = NULL)
	{
		$where = array();
		$valid = array('id', 'zip');
		foreach($valid as $v)
		{
			if ($this->input->get_post($v))
			{
				$where[$v] = $this->input->get_post($v, TRUE);
			}
		}

		$locations = $this->fuel->locations->search($where, $return_type);
		return $locations;
	}

	function _render($view, $vars)
	{
		$views = $this->fuel->locations->config('views');
		$view = $views[$view];
		$module = 'app';
		if (is_array($view))
		{
			$module = key($view);
			$view = current($view);
		}
		$this->load->module_view($module, $view, $vars);

	}
}