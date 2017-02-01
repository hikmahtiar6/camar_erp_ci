<?php
/**
 * Transaction Controller
 */
class Report extends CI_Controller 
{
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// check session
		$this->auth->_is_authentication();

		// load model section
		$this->load->model('section_model');

	}

	/**
	 * Index Page
	 */
	public function index()
	{
		$this->twiggy->template('admin/report/index')->display();
	}

	/**
	 * Searching report
	 */
	public function search()
	{
		$post = $this->input->post();

		if($post['action'] == 'layar')
		{
			$search_data = $this->section_model->search($post['date_start'], $post['date_finish'], $post['shift']);

			$this->twiggy->set('search_data', $search_data);
			$this->twiggy->template('admin/report/layar')->display();
		}
		else
		{
			echo "Fitur masih dalam tahap pengembangan";
		}
	}
}
?>