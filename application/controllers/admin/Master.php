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
		$this->load->model('master/finishing_model');
		$this->load->model('master/len_model');
		$this->load->model('master/indexdice_model');
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

	public function get_data_finishing()
	{
		$row = array();

		$data = $this->finishing_model->get_data();

		if($data)
		{
			foreach($data as $r)
			{
				$row[] = array(
					'value' => $r->finishing_id,
					'text'  => $r->finishing_name,
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
					'value' => $r->section_id.'|'.$r->master_id,
					'text'  => $r->section_name,
				);
			}
		}

		return $this->output->set_output(json_encode($row));
	}

	public function get_data_len()
	{
		$row = array();
		$data = $this->len_model->get_data();

		if($data)
		{
			foreach($data as $r)
			{
				$row[] = array(
					'value' => $r->LengthId,
					'text'  => $r->Length,
				);
			}
		}

		return $this->output->set_output(json_encode($row));
	}

	public function get_data_index_dice($section_id, $machine_type_id)
	{
		$row = array();

		if($section_id == 'null' || $section_id == '')
		{
			$r = 'b';
			$get_dice = $this->indexdice_model->get_data();
		}
		else
		{
			$r = 'a';
			$get_data = array(
				'SectionId'     => str_replace('%20', ' ', $section_id),
				'MachineTypeId' => $machine_type_id
			);
			$get_dice = $this->indexdice_model->get_data_by($get_data)->result();
		}

		if($get_dice)
		{
			foreach($get_dice as $r)
			{
				$row[] = array(
					'value' => $r->DiesId,
					'text'  => $r->DiesId,
				);
			}
		}

		return $this->output->set_output(json_encode($row));
	}

}
?>