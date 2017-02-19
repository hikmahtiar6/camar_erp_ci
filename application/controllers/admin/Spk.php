<?php
/**
 * Spk Controller
 */
class Spk extends CI_Controller 
{
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// check session
		$this->auth->_is_authentication();
		$this->load->model('master_model');
		$this->load->model('section_model');
		$this->load->model('master/header_model');
		$this->load->model('master/detail_model');
		$this->load->model('master/shift_model');
	}

	/**
	 * index page
	 */
	public function index()
	{
		$machine_id = ($this->input->post('machine')) ? $this->input->post('machine') : '';
		$shift = ($this->input->post('shift')) ? $this->input->post('shift') : '0';
		$date_start = ($this->input->post('date_start')) ? $this->input->post('date_start') : '';
		$date_finish = ($this->input->post('date_finish')) ? $this->input->post('date_finish') : '';
		$week = ($this->input->post('week')) ? $this->input->post('week') : '';

		$machine_data = $this->master_model->get_data_machine();
		$shift_data = $this->shift_model->get_data();
		$header_data = $this->header_model->advance_search($machine_id);

		$new_date_start = date('Y-m-d', strtotime(str_replace("/", "-", $date_start)));
		$new_date_finish = date('Y-m-d', strtotime(str_replace("/", "-", $date_finish)));

		//$section_data = $this->section_model->get_data_detail_new($new_date_start, $new_date_finish, $shift = 0, $machine_id = '', $section_id = '', $header_id = '', $limit = '', $start = '');

		$this->twiggy->set('machine_id', $machine_id);
		$this->twiggy->set('shift', $shift);
		$this->twiggy->set('date_start', $date_start);
		$this->twiggy->set('date_finish', $date_finish);
		$this->twiggy->set('week', $week);

		$this->twiggy->set('shift_data', $shift_data);
		$this->twiggy->set('machine_data', $machine_data);
		$this->twiggy->set('header_data', $header_data);
		$this->twiggy->template('admin/spk/index')->display();
	}

	public function cache_detail($header_id, $strtime_start, $strtime_finish, $shift)
	{
		$date_start = date('Y-m-d', $strtime_start);
		$date_finish = date('Y-m-d', $strtime_finish);

		$this->session->set_userdata('date_start', $date_start);
		$this->session->set_userdata('date_finish', $date_finish);
		$this->session->set_userdata('shift', $shift);

		$data_update = array(
			'date_start'  => $date_start,
			'date_finish' => $date_finish,
		);

		$update_header = $this->header_model->update($header_id, $data_update);

		redirect('admin/transaction/detail/'.$header_id);
	}
}
?>