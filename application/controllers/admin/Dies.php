<?php
/**
 * Controller Dies
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */

class Dies extends CI_Controller 
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// load model
		$this->load->model('master/indexdice_model');
		$this->load->model('master/machine_model');
	}
	
	/**
	 * Index Page
	 */
	public function index()
	{
		$machine_data = $this->machine_model->get_data();

		$this->twiggy->set('machine_data', $machine_data);
		$this->twiggy->set('date_now', date('d/m/Y'));
		$this->twiggy->template('admin/dies/index')->display();
	}

	/**
	 * FIltering dies
	 */
	public function filter()
	{
		// post
		$tgl = $this->input->post('tanggal-dies');
		$mesin = $this->input->post('mesin-dies');

		// var
		$date_now = change_format_date($tgl, 'd-m-Y');
		$date_now2 = change_format_date($tgl);
		$shift = array('1', '2');

		// view
		$this->twiggy->set('machine', $mesin);
		$this->twiggy->set('date_now', $date_now);
		$this->twiggy->set('date_now2', $date_now2);
		$this->twiggy->set('shift', $shift);
		$this->twiggy->display('admin/dies/result');
	}

	/**
	 * History Page
	 */
	public function history()
	{
		$data = $this->indexdice_model->get_dies_log()->result();

		$this->twiggy->set('data', $data);
		$this->twiggy->template('admin/dies/history')->display();
	}

	/**
	 * Set Card Log
	 */
	public function set_log()
	{
		$location = $this->input->post('location');
		$status = $this->input->post('status');
		$dies_id = $this->input->post('dies_id');

		if(is_array($dies_id))
		{
			foreach ($dies_id as $value) {

				$data = array(
					'LogTime'        => date('Y-m-d H:i:s'),
					'DiesId'         => $value,
					'DiesStatusId'   => $status,
					'DiesLocationId' => $location
				);

				$save = $this->indexdice_model->set_dies_log($data);
			}
			
			$this->output->set_output('done');
		}
		else
		{
			$data = array(
				'LogTime'        => date('Y-m-d H:i:s'),
				'DiesId'         => $dies_id,
				'DiesStatusId'   => $status,
				'DiesLocationId' => $location
			);

			$save = $this->indexdice_model->set_dies_log($data);
			
			$this->output->set_output('<i class="material-icons">done</i>');
		}

	}
	
	/**
	 * Edit view
	 */
	public function edit($problem_id = '')
	{
		$data_problem = $this->indexdice_model->get_data_problem();
		$this->twiggy->set('problem', $data_problem);
		$this->twiggy->set('problem_id', $problem_id);
		$this->twiggy->display('admin/dies/edit');
	}
	
	/**
	 * Save problem
	 */
	public function save_problem()
	{
		$problem_id = $this->input->post('problem');
		$dies_id = $this->input->post('dies');
		
		$get_last_data = $this->indexdice_model->get_last_log_by_dies($dies_id);
		
		$data_update = array(
			'DiesProblemId' => $problem_id
		);
		$update = $this->indexdice_model->update_log($data_update, $get_last_data->DiesHistoryCardLogId);
		
		if($update)
		{
			$response = array(
				'status'  => 'success',
				'message' => 'Berhasil input Problem'
			);
		}
		else
		{
			$response = array(
				'status'  => 'error',
				'message' => 'Gagal input Problem'
			);
		}
		
		$this->output->set_output(json_encode($response));
	} 
}
?>