<?php
class Section extends CI_Controller {
	public function __conctruct() 
	{
		parent::__conctruct();

	}

	public function index()
	{
		$this->load->model('section_model');
		$barang = $this->section_model->get_data();
		//return $this->output->set_output(var_dump());
		//var_dump($data);

		$data['brg'] = $barang;

		$this->load->view('section/index', $data);
	}
}
?>