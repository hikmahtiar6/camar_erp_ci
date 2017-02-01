<?php
/**
 * class master
 */
class Master extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	
		$this->load->model('master_model');
		$this->load->model('master/header_model');
	}

	public function get_data_shift()
	{
		$row = array();
		$data = $this->master_model->get_data_shift();

		if($data)
		{
			foreach($data as $r)
			{
				$row[] = array(
					'value' => $r->ShiftNo,
					'text' => $r->ShiftDescription,
				);
			}
		}

		return $this->output->set_output(json_encode($row));
	}

	public function get_data_section($header_id)
	{
		$row = array();
		$header_data = $this->header_model->get_data_by_id($header_id);

		$machine_id = '';

		if($header_data)
		{
			$machine_id = $header_data->machine_id;
		}

		$data = $this->master_model->get_data_by_machine_id($machine_id);

		if($data)
		{
			foreach($data as $r)
			{
				$row[] = array(
					'value' => $r->section_id,
					'text' => $r->section_id,
				);
			}
		}

		return $this->output->set_output(json_encode($row));
	}

}
?>