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

		$this->load->model('master/indexdice_model');
	}
	
	/**
	 * Index Page
	 */
	public function index()
	{
		$this->twiggy->set('date_now', date('d/m/Y'));
		$this->twiggy->template('admin/dies/index')->display();
	}

	/**
	 * FIltering dies
	 */
	public function filter()
	{
		$tgl = $this->input->post('tanggal-dies');
		$date_now = change_format_date($tgl, 'd-m-Y');
		$date_now2 = change_format_date($tgl);
		$shift = array('1', '2');

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
?>