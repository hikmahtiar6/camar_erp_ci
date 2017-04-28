<?php
/**
 * Helper for QUery
 */
function get_row_master($show = 'SectionId', $machine_id = '', $section_id = '')
{
	$ci =& get_instance();
	$ci->load->model('master/query_model');

	$get_data = $ci->query_model->get_master_advance($machine_id, $section_id)->row_array();

	if($get_data)
	{
		if(array_key_exists($show, $get_data))
		{
			return $get_data[$show];
		}

		return '';
	}
	
	return '';
}