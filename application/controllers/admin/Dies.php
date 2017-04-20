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
		$shift = array('1', '2');

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

		if(is_array($dies_id))
		{
			foreach ($dies_id as $value) {

				$data = array(
					'LogTime'        => date('Y-m-d H:i:s'),
					'DiesId'         => $value,
					'DiesStatusId'   => $status,
					'DiesLocationId' => $location
				);
				
				if($dies_problem)
				{
					$data['DiesProblemId'] = $dies_problem;
				}

				$save = $this->indexdice_model->set_dies_log($data);
			}
			
			$this->output->set_output('done');
		}
		else
		{
			$data = array(
				'LogTime'        => date('Y-m-d H:i:s'),
				'DiesId'         => $dies_id,
				'DiesStatusId'   => $status,
				'DiesLocationId' => $location
			);
			
			if($dies_problem)
			{
				$data['DiesProblemId'] = $dies_problem;
			}

			$save = $this->indexdice_model->set_dies_log($data);
			
			$this->output->set_output('<i class="material-icons">done</i>');
		}

	}
	
	/**
	 * Edit view
	 */
	public function edit($problem_id = '')
	{
		$data_problem = $this->indexdice_model->get_data_problem();
		$data_status = $this->indexdice_model->get_dice_status();
		

		$this->twiggy->set('problem', $data_problem);
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

		if($status)
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
			);
			$update = $this->indexdice_model->update_log($data_update, $get_last_data->DiesHistoryCardLogId);
			
			if($update)
			{
				$response = array(
					'status'  => 'success',
					'message' => 'Berhasil input Problem'
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
	 * Save Scrap
	 */
	public function save_scrap()
	{
		$scrap = $this->input->post('scrap');
		$tanggal = $this->input->post('tanggal');
		$shift = $this->input->post('shift');
		$lost = $this->input->post('lost');
		$endbutt = $this->input->post('endbutt');
		$header_id = $this->input->post('header_id');

		$data_save = array(
			'Scrap'      => $scrap,
			'EndButt'    => $endbutt, 
			'Lost'       => $lost,
			'Shift'      => $shift,
			'Tanggal'    => date('Y-m-d', strtotime($tanggal)),
			'SpkHeaderId'=> $header_id,
		);

		$check_data = $this->scrap_model->get_data_tgl_header($header_id, $tanggal, $shift);
		if($check_data)
		{
			$this->scrap_model->update($check_data->LotScrapId, $data_save);
		}
		else
		{
			$this->scrap_model->save($data_save);
		}

	}

	/**
	 * Dies History Card
	 */
	public function history_card()
	{
		$sections = $this->section_model->get_section_grouping();
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

		$data = $this->indexdice_model->filter_history_card($section_id, $dice);
		$this->twiggy->set('data', $data);
		$this->twiggy->set('dice', $dice);
		$this->twiggy->set('section_id', $section_id);
		$this->twiggy->display('admin/dies/history.card.result');
	}
}
?>