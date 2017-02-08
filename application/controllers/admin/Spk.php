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
		$this->load->model('master/header_model');
		$this->load->model('master/detail_model');
	}

	/**
	 * index page
	 */
	public function index()
	{
		$header_data = $this->header_model->get_data();
		$this->twiggy->set('header_data', $header_data);
		$this->twiggy->template('admin/spk/index')->display();
	}

	public function cache_detail($header_id, $strtime_start, $strtime_finish)
	{
		$this->session->set_userdata('date_start', date('Y-m-d', $strtime_start));
		$this->session->set_userdata('date_finish', date('Y-m-d', $strtime_finish));

		redirect('admin/transaction/detail/'.$header_id);
	}
}
?>