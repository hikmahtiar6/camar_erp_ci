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

		// load model
		$this->load->model('master/indexdice_model');
		$this->load->model('master/machine_model');
		$this->load->model('master/scrap_model');
		$this->load->model('master/die_model');
		$this->load->model('master/query_model');
		$this->load->model('section_model');
	}
	
	/**
	 * Index Page
	 */
	public function index()
	{
		$machine_data = $this->machine_model->get_data();

		$this->twiggy->set('machine_data', $machine_data);
		$this->twiggy->set('date_now', date('d/m/Y'));
		$this->twiggy->template('admin/dies/index')->display();
	}

	/**
	 * FIltering dies
	 */
	public function filter()
	{
		// post
		$tgl = $this->input->post('tanggal-dies');
		$mesin = $this->input->post('mesin-dies');

		// var
		$date_now = change_format_date($tgl, 'd-m-Y');
		$date_now2 = change_format_date($tgl);
		$shift = array('1', '2', '3');

		// view
		$this->twiggy->set('machine', $mesin);
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
		$dies_problem = $this->input->post('dies_problem');
		$date = $this->input->post('date');
		$master_detail_id = $this->input->post('master_detail_id');

		if(!$date)
		{
			$date = date('Y-m-d H:i:s');
		}
		else
		{
			$date = $date.' '.date('H:i:s');
		}

		if(is_array($dies_id))
		{
			foreach ($dies_id as $value) {

				$data = array(
					'LogTime'        => change_format_date($date, 'Y-m-d H:i:s'),
					'DiesId'         => $value,
					'DiesStatusId'   => $status,
					'DiesLocationId' => $location
				);
				
				if($dies_problem)
				{
					$data['DiesProblemId'] = $dies_problem;
				}

				if($master_detail_id)
				{
					$data['MasterDetailId'] = $master_detail_id;
				}

				$save = $this->indexdice_model->set_dies_log($data);
			}
			
			$this->output->set_output('done');
		}
		else
		{
			$data = array(
				'LogTime'        => change_format_date($date, 'Y-m-d H:i:s'),
				'DiesId'         => $dies_id,
				'DiesStatusId'   => $status,
				'DiesLocationId' => $location
			);
			
			if($dies_problem)
			{
				$data['DiesProblemId'] = $dies_problem;
			}

			if($master_detail_id)
			{
				$data['MasterDetailId'] = $master_detail_id;
			}

			$save = $this->indexdice_model->set_dies_log($data);
			
			$this->output->set_output('<i class="material-icons">done</i>');
		}

	}
	
	/**
	 * Edit view
	 */
	public function edit($dies_card_id = '', $problem_id = '')
	{
		$data_problem = $this->indexdice_model->get_data_problem();
		$data_status = $this->indexdice_model->get_dice_status();

		$koreksi = '';
		$korektor = '';

		$last_data = $this->indexdice_model->get_log_by_id($dies_card_id);
		if($last_data)
		{
			$koreksi = $last_data->Koreksi;
			$korektor = $last_data->Korektor;

			if($korektor == "" && $korektor == null)
			{
				$korektor = $this->session->userdata('user_id');
			}
		}

		$this->twiggy->set('problem', $data_problem);
		$this->twiggy->set('koreksi', $koreksi);
		$this->twiggy->set('korektor', $korektor);
		$this->twiggy->set('status', $data_status);
		$this->twiggy->set('problem_id', $problem_id);
		$this->twiggy->display('admin/dies/edit');
	}
	
	/**
	 * List Problem
	 * @return html <option>
	 */
	public function list_problem()
	{
		$txt = "";
		$data_problem = $this->indexdice_model->get_data_problem();
		foreach($data_problem as $problem)
		{
			$txt .= '<option value="'.$problem->DiesProblemId.'">'.$problem->Problem.'</option>';
		}
		
		$this->output->set_output($txt);
	}
	
	/**
	 * Save problem
	 */
	public function save_problem()
	{
		$problem_id = $this->input->post('problem');
		$dies_id = $this->input->post('dies');
		$status = $this->input->post('status');
		$koreksi = $this->input->post('koreksi');
		$korektor = $this->input->post('korektor');

		if(isset($status))
		{
			$data = array(
				'LogTime'        => date('Y-m-d H:i:s'),
				'DiesId'         => $dies_id,
				'DiesStatusId'   => $status,
				'DiesLocationId' => 1
			);

			$save = $this->indexdice_model->set_dies_log($data);
			if($save)
			{
				$response = array(
					'status'  => 'success',
					'message' => 'Berhasil input status'
				);
			}
			else
			{
				$response = array(
					'status'  => 'error',
					'message' => 'Gagal input status'
				);
			}
			

		}
		else
		{
			$get_last_data = $this->indexdice_model->get_last_log_by_dies($dies_id);
		
			$data_update = array(
				'DiesProblemId' => $problem_id,
				'Koreksi'       => $koreksi,
				'Korektor'       => $korektor,
				'DiesLocationId' => 2
			);
			$update = $this->indexdice_model->update_log($data_update, $get_last_data->DiesHistoryCardLogId);
			
			if($update)
			{
				$response = array(
					'status'  => 'success',
					'message' => 'Berhasil input Problem',
					'dies'    => $dies_id
				);
			}
			else
			{
				$response = array(
					'status'  => 'error',
					'message' => 'Gagal input Problem'
				);
			}
			
		}

		$this->output->set_output(json_encode($response));
	} 

	/**
	 * Dies History Card
	 */
	public function history_card()
	{
		$sections = $this->section_model->get_section_grouping()->result();
		$dices = $this->indexdice_model->get_data2()->result();

		$this->twiggy->set('sections', $sections);
		$this->twiggy->set('dices', $dices);
		$this->twiggy->display('admin/dies/history.card');
	}

	/**
	 * Dies History Card
	 */
	public function history_card_search()
	{
		$dice = $this->input->post('dice');
		$section_id = $this->input->post('section');
		$tanggal = $this->input->post('tanggal');
		$tgl_pembelian = '';

		$section_description = '';
		$get_section_description = $this->section_model->get_section_grouping($section_id)->row();
		if($get_section_description)
		{
			$section_description = $get_section_description->SectionDescription;
		}

		$get_pembelian = $this->die_model->get_detail($dice);
		if($get_pembelian != '')
		{
			$tgl_pembelian = change_format_date($get_pembelian, 'd-M-Y');
		}

		$data = $this->indexdice_model->filter_history_card_fix($section_id= '', $dice, $tanggal);

		$this->twiggy->set('data', $data);
		$this->twiggy->set('dice', $dice);
		$this->twiggy->set('tgl_pembelian', $tgl_pembelian);
		$this->twiggy->set('tgl', change_format_date($tanggal));
		$this->twiggy->set('tgl2', change_format_date($tgl_pembelian));
		$this->twiggy->set('section_id', $section_id);
		$this->twiggy->set('section_description', $section_description);
		$this->twiggy->display('admin/dies/history.card.result');
	}

	private function get_tanggal_pembelian($data)
	{
		$res = array();
		$tgl_pembelian = '';

		if($data)
		{
			foreach($data as $row)
			{

				$get_pembelian = $this->die_model->get_detail($row->DiesId);
				if($get_pembelian != '')
				{
					$tgl_pembelian = change_format_date($get_pembelian, 'd-M-Y');
				}

				$res[$row->DiesId] = $tgl_pembelian;
			}
		}

		return $res;
	}

	/**
	 * Menghilangkan data dies id yg sama
	 */
	private function super_unique_die($data)
	{
		$ress = array();
		$res = array();

		if($data)
		{
			foreach($data as $row)
			{
				$res[] = array(
					'dies_id'=> $row->DiesId
				);
			}
		}

		$result = array_map("unserialize", array_unique(array_map("serialize", $res)) );

		foreach ($result as $rr) {
			$ress[] = array(
				'DiesId' => $rr['dies_id'],
			);
		}

		return $ress;
	}

	/**
	 * Dies History Card
	 */
	public function history_card_search_index()
	{
		$dice = $this->input->post('seqno');
		$section_id = $this->input->post('section');
		$tanggal = $this->input->post('dies_year');
		$tgl_pembelian = '';

		$section_description = '';
		$get_section_description = $this->section_model->get_section_grouping($section_id)->row();
		if($get_section_description)
		{
			$section_description = $get_section_description->SectionDescription;
		}

		$data = $this->query_model->get_dies_history_card2($section_id, $tanggal, $dice);
		$this->twiggy->set('data', $this->super_unique_die($data));
		$this->twiggy->set('tgl_pembelian', $this->get_tanggal_pembelian($data));
		$this->twiggy->set('tgl', change_format_date($tanggal));
		$this->twiggy->set('tgl2', change_format_date($tgl_pembelian));
		$this->twiggy->set('section_id', $section_id);
		$this->twiggy->set('section_description', $section_description);
		$this->twiggy->display('admin/dies/history.card.result2');
	}

	public function get_dies_year_by_section()
	{
		$response = '';

		$section_id = $this->input->post('section_id');

		$data = $this->query_model->get_dies_year_by_section($section_id);
		if($data)
		{
			foreach($data as $row)
			{
				$response .= '<option>'.$row->DiesYear.'</option>';
			}
		}

		output_json($response);
	}

	public function get_dies_seq_no_by_section_year()
	{
		$response = '';

		$section_id = $this->input->post('section_id');
		$year = $this->input->post('year');

		$data = $this->query_model->get_seq_no_by_section_year($section_id, $year);
		if($data)
		{
			foreach($data as $row)
			{
				$response .= '<option>'.$row->DiesSeqNo.'</option>';
			}
		}

		output_json($response);
	}
}
?>