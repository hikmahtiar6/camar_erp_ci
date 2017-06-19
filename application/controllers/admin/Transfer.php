<?php
/**
 * Class Transfer
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Transfer extends CI_Controller {
	/**
	 * COnstructor COdeigniter
	 */
	public function __construct()
	{
		parent::__construct();

		// load model
		$this->load->model('master/lot_model');
	}

	/**
	 * Index Page
	 * 
	 * @return HTML
	 */
	public function index()
	{
		$lot_data = $this->lot_model->get_data_header();


		// load view with Twig
		$this->twiggy->set('lot_data', $lot_data);
		$this->twiggy->display('admin/transfer/index');
		dump($lot_data);
	}
}