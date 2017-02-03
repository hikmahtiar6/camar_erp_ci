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
		$this->load->model('master/machine_model');
		$this->load->model('master/shift_model');

	}

	/**
	 * Index Page
	 */
	public function index()
	{
		$machine = $this->machine_model->get_data();
		$shift = $this->shift_model->get_data();
		$section = $this->section_model->get_data();

		$this->twiggy->set('machines', $machine);
		$this->twiggy->set('shifts', $shift);
		$this->twiggy->set('sections', $section);
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
			$search_data = $this->section_model->get_data_detail($date_start = '', $date_finish = '', $post['shift'], $post['mesin'], $post['section']);

			$this->twiggy->set('search_data', $search_data);
			$this->twiggy->set('post', $post);
			$this->twiggy->template('admin/report/layar')->display();
		}
		else
		{
			echo "Fitur masih dalam tahap pengembangan";
		}
	}
}
?>