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

	/**
	 * sinkronisasi
	 */
	public function sinkronisasi()
	{
		$view = $this->master_model->get_data_view();
		if($view)
		{
			$data = array();
			$this->master_model->truncate_master();
			foreach($view as $row)
			{
				$data[] = array(
					'section_id'            => $row->SectionId, 
				    'f2_estfg'              => $row->F2_EstFG,
				    'machine_type_id'       => $row->MachineTypeId,
				    'billet_id'             => $row->BilletTypeId,
				    'weight_standard'       => $row->WeightStandard,
				    'die_type_name'         => $row->DieTypeName,
				    'actual_pressure_time'  => NULL,
				    'machine_id'            => $row->MachineId,
				    'len_id'                => $row->LengthId,
				    'len'                   => $row->Length,
				);
			}
			$save = $this->master_model->insert_master($data);

			if($save)
			{
				echo 'sinkronisasi berhasil';
			}
			else
			{
				echo 'sinkronisasi gagal';
			}
		}
		else
		{
			echo 'no';
		}
	}

}
?>