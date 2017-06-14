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
	}

	/**
	 * Index Page
	 * 
	 * @return HTML
	 */
	public function index()
	{
		// load view with Twig
		$this->twiggy->display('admin/transfer/index');
	}
}