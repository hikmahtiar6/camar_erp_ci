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

/**
 * Cek isian lot
 */
function cek_isian_lot($master_detail_id = '')
{
	$ci =& get_instance();
	$ci->load->model('master/lot_model');

	$exists = array();

	$cek_billet = $ci->lot_model->get_isian_billet_by_master_detail_id($master_detail_id, 'billet');
	$cek_berat_aktual = $ci->lot_model->get_isian_billet_by_master_detail_id($master_detail_id, 'berat_aktual');
	$cek_hasil = $ci->lot_model->get_isian_billet_by_master_detail_id($master_detail_id, 'hasil');

	if(count($cek_billet) > 0) {
		array_push($exists, 1);
	}

	if(count($cek_berat_aktual) > 0) {
		array_push($exists, 1);
	}

	if(count($cek_hasil) > 0) {
		array_push($exists, 1);
	}

	return count($exists);
}
?>