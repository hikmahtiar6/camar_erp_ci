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
		$this->load->model('master_model');
		$this->load->model('master/shift_model');
		$this->load->model('master/query_model');
		$this->load->model('master/detail_model');
		$this->load->model('master/lot_model');

	}

	/**
	 * Index Page
	 */
	public function index()
	{
		$machine = $this->machine_model->get_data();
		$shift = $this->shift_model->get_data();
		$section = $this->section_model->get_data();

		$machine_data = $this->master_model->get_data_machine();
		$shift_data = $this->shift_model->get_data();

		$this->twiggy->set('shift_data', $shift_data);
		$this->twiggy->set('machine_data', $machine_data);
		$this->twiggy->set('date_now', date('d/m/Y'));

		$this->twiggy->set('machines', $machine);
		$this->twiggy->set('shifts', $shift);
		$this->twiggy->set('sections', $section);
		$this->twiggy->template('admin/report/index')->display();
	}

	/**
	 * Index Page
	 */
	public function lot()
	{
		$machine = $this->machine_model->get_data();
		$shift = $this->shift_model->get_data();
		$section = $this->section_model->get_data();

		$machine_data = $this->master_model->get_data_machine();
		$shift_data = $this->shift_model->get_data();

		$this->twiggy->set('shift_data', $shift_data);
		$this->twiggy->set('machine_data', $machine_data);
		$this->twiggy->set('date_now', date('d/m/Y'));

		$this->twiggy->set('machines', $machine);
		$this->twiggy->set('shifts', $shift);
		$this->twiggy->set('sections', $section);
		$this->twiggy->template('admin/report/lot/index')->display();
	}

	/**
	 * Searching report
	 */
	public function search()
	{
		$post = $this->input->post();
		$tanggal = (isset($post['tanggal'])) ? $post['tanggal'] : '' ;
		$machine = (isset($post['machine'])) ? $post['machine'] : '' ;
		$shift = (isset($post['shift'])) ? $post['shift'] : '0' ;
		$submit = (isset($post['submit'])) ? $post['submit'] : 'Submit' ;

		$machineDescription = '';
		$get_machine = $this->machine_model->get_data_by_id($machine);
		if($get_machine)
		{
			$machineDescription = $get_machine->Description;
		}

		$tgl = str_replace("/", "-", $tanggal);
		$tgl =  date('Y-m-d', strtotime($tgl));


		$idn_time = indonesia_day($tgl).', ' .indonesian_date($tgl);

		$search_data = $this->query_model->get_report_advance($machine, $tgl, $shift)->result();

		$this->twiggy->set('search_data', $search_data);
		$this->twiggy->set('post', $post);
		$this->twiggy->set('machine', $machine);
		$this->twiggy->set('shift', $shift);
		$this->twiggy->set('machine_description', $machineDescription);
		$this->twiggy->set('tanggal', $idn_time);
		$this->twiggy->set('tgl', $tgl);

		switch ($submit) {
			case 'Submit':
				# code...
				$this->twiggy->template('admin/report/layar')->display();
				break;

			case 'SPK2':
				# code...
				if($shift == 0)
				{
					$shift = array('SHIFT 1', 'SHIFT 2');
				}

				$get_master = $this->query_model->get_master_advance($machine, '')->result();
				$this->twiggy->set('master_data', $get_master);
				$this->twiggy->set('shift2', $shift);
				$this->twiggy->template('admin/report/spk2')->display();
				break;

			case 'SPK3':
				# code...
				$this->twiggy->template('admin/report/spk3')->display();
				break;
			
			default:
				# code...
				$this->twiggy->template('admin/report/layar')->display();
				break;
		}

	}

	/**
	 * Searching report
	 */
	public function search_lot()
	{
		$post = $this->input->post();
		$tanggal = (isset($post['tanggal'])) ? $post['tanggal'] : '' ;
		$machine = (isset($post['machine'])) ? $post['machine'] : '' ;
		$shift = (isset($post['shift'])) ? $post['shift'] : '0' ;

		$machineDescription = '';
		$get_machine = $this->machine_model->get_data_by_id($machine);
		if($get_machine)
		{
			$machineDescription = $get_machine->Description;
		}

		$tgl = str_replace("/", "-", $tanggal);
		$tgl =  date('Y-m-d', strtotime($tgl));


		$idn_time = indonesia_day($tgl).', ' .indonesian_date($tgl);

		$search_data = $this->query_model->get_report_advance($machine, $tgl, $shift)->result();

		$this->twiggy->set('search_data', $search_data);
		$this->twiggy->set('post', $post);
		$this->twiggy->set('machine', $machine);
		$this->twiggy->set('shift', $shift);
		$this->twiggy->set('machine_description', $machineDescription);
		$this->twiggy->set('tanggal', $idn_time);
		$this->twiggy->set('tgl', $tgl);
		$this->twiggy->template('admin/report/lot/layar')->display();
	}
}
?>