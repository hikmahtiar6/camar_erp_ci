<?php
/**
 * Lot Helper
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */

function list_vendor_lot()
{
	$data = array('PM', 'EL', 'EV');
	return $data;
}

function get_detail_machine($machine_id , $show = 'MachineId')
{
	$ci =& get_instance();
	$ci->load->model('master/machine_model');

	$get_data = $ci->machine_model->get_detail($machine_id)->row_array();
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

function get_effective_item_dimension($section_id, $show = 'WeightUpperLimit')
{
	$ci =& get_instance();
	$ci->load->model('master/query_model');

	$get_data = $ci->query_model->get_effective_item_dimension($section_id)->row_array();

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
?>