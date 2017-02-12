<?php
/**
 * Transaction Controller
 */
class Dashboard extends CI_Controller 
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
	}

	public function index()
	{
		$this->twiggy->template('admin/dashboard/index')->display();
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('login');
	}

}
?>