<?php
/**
 * class master
 */
class Master extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	
		$this->load->model('master_model');
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

	public function get_data_section()
	{
		$machine = $this->input->post('machine');
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

}
?>