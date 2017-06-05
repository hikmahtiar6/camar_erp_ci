<?php
/**
 * Helper for QUery
 */
function get_row_master($show = 'SectionId', $machine_id = '', $section_id = '', $length_id = '')
{
	$ci =& get_instance();
	$ci->load->model('master/query_model');

	$get_data = $ci->query_model->get_master_advance($machine_id, $section_id, $length_id)->row_array();

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

function qty_pr_per_section($header_id, $section_id)
{
	$ci =& get_instance();
	$ci->load->model('master/query_model');

	return $ci->query_model->qty_pr_per_section($header_id, $section_id);
}

function get_machine_in_spk_detail($master_detail_id)
{
	$ci =& get_instance();
	$ci->load->model('master/query_model');

	$machine = '';
	$data = $ci->query_model->get_machine_in_spk_detail($master_detail_id);

	if($data)
	{
		$machine = $data->machine_id;
	}

	return $machine;
}