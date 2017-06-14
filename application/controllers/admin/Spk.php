<?php
/**
 * Spk Controller
 */
class Spk extends CI_Controller 
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
		$this->load->model('section_model');
		$this->load->model('master/header_model');
		$this->load->model('master/detail_model');
		$this->load->model('master/shift_model');
		$this->load->model('master/query_model');
		$this->load->model('master/lot_model');
		$this->load->model('master/scrap_model');
	}

	/**
	 * index page
	 */
	public function index()
	{
		$machine_id = ($this->input->post('machine')) ? $this->input->post('machine') : '';
		$shift = ($this->input->post('shift')) ? $this->input->post('shift') : '0';
		$date_start = ($this->input->post('date_start')) ? $this->input->post('date_start') : '';
		$date_finish = ($this->input->post('date_finish')) ? $this->input->post('date_finish') : '';
		$week = ($this->input->post('week_number')) ? ltrim($this->input->post('week_number')) : '';

		$machine_data = $this->master_model->get_data_machine();
		$shift_data = $this->shift_model->get_data();
		$header_data = $this->header_model->advance_search($machine_id, $week);

		$this->twiggy->set('machine_id', $machine_id);
		$this->twiggy->set('shift', $shift);
		$this->twiggy->set('date_start', $date_start);
		$this->twiggy->set('date_finish', $date_finish);
		$this->twiggy->set('week', $week);

		$this->twiggy->set('shift_data', $shift_data);
		$this->twiggy->set('machine_data', $machine_data);
		$this->twiggy->set('header_data', $header_data);
		$this->twiggy->template('admin/spk/index')->display();
	}

	/**
	 * Get data SPK
	 * @return Json
	 */
	public function get_data($header_id = '')
	{
		$response = array();

		$tgl = $this->input->post('tanggal');
		$date = ($tgl != '') ? date('Y-m-d', strtotime($tgl)) : '';

		$dt_start = date('Y-m-d', strtotime($this->session->userdata('date_start')));
		$dt_finish = date('Y-m-d', strtotime($this->session->userdata('date_finish')));
		$shift = $this->session->userdata('shift');

		$machine = '';
		$header_data = $this->header_model->get_data_by_id($header_id);
		if($header_data)
		{
			$machine = $header_data->machine_id;
		}

		$get_md = $this->section_model->get_data_detail_new($dt_start, $dt_finish, $shift, $machine_id = '', $section_id = '',$header_id, '', '', $date);	
		if($get_md)
		{
			foreach($get_md as $row)
			{
				$section_id = $row->section_id;
				$target_prod = ($row->target_prod == null) ? '' : $row->target_prod;
				$ppic = ($row->ppic_note == null) ? '' : $row->ppic_note;

				$target_prod_btg = $target_prod;
				$f2_estfg =$row->F2_EstFG;
				$weight_standard = to_decimal($row->WeightStandard, 3, true);
				$hole_count = $row->HoleCount;
				$die_type = $row->DieTypeName;

				if($f2_estfg != NULL)
				{
					$target_prod_btg = $f2_estfg * $row->target_prod * $hole_count; 
				}
				$len = $row->Length;
				$target_section = $weight_standard * $target_prod_btg * $len;

				$tgl = ($row->tanggal == null) ? '' : date('d-m-Y', strtotime($row->tanggal));

				$btn_lot = "Isi Lot";
				$btn_class = "btn-primary";
				if($row->is_posted)
				{
					$btn_class = "btn-success";
					$btn_lot = "Lihat Lot";
				} 

				$response[] = array(
					'master_detail_id'  => $row->master_detail_id,
					'button'            => '<a href="javascript:;" class="btn btn-xs '.$btn_class.' waves-effect" data-toggle="modal" data-target="#defaultModal" data-backdrop="static" data-keyboard="false" onclick="window.TRANSACTION.handleModalLot('.$row->master_detail_id.')">'.$btn_lot.'</a>',
					'tanggal'           => $tgl,
					'shift'             => $row->ShiftDescription,
					'shift_id'          => $row->ShiftRefId,
					'shift_no'          => $row->ShiftNo,
					'section_name'      => $row->SectionDescription,
					'section_id'        => $row->section_id,
					'machine_id'        => $row->machine_id_header,
					'len'				=> to_decimal($len),
					'len_id'			=> $row->len,
					'finishing'         => $row->finishing_name,
					'dies'              => $this->convert_dice($row->index_dice),
					'target_prod'       => $target_prod,
					'ppic'              => $ppic,
					'target_prod_btg'   => $target_prod_btg,
					'weight_standard'   => $weight_standard,
					'target_section'    => $target_section,
					'die_type'          => $die_type,
					'header_id'         => $row->header_id,
					'posted'            => $row->is_posted,
					'time_finish'       => ($row->time_finish != '') ? 'bg-orange' : ''
				);
			}
		}

		output_json($response);
	}

	/**
	 * Delete SPK Per ROw
	 */
	public function delete()
	{
		$id = $this->input->post('id');

		$del = $this->detail_model->delete($id);

		// cek hapus data utk menghasilkan response yg dikirim utk notifikasi
		if($del)
		{
			$this->lot_model->delete_lot($id);
			$this->lot_model->delete_head_lot($id);
	
			$response = array(
				'status'  => 'success',
				'message' => 'Berhasil menghapus data'
			);
		}
		else
		{
			$response = array(
				'status'  => 'error',
				'message' => 'Gagal menghapus data'
			);
		}

		output_json($response);
	}

	/**
	 * Get Last Data
	 */
	public function get_last_data()
	{

	}

	private function convert_dice($dice)
	{
		$dice_txt = ($dice == null) ? '' : $dice;
		
		$txt = '';
		$expl = explode(",", $dice_txt);

		if(count($expl) > 0)
		{
			foreach($expl as $rexpl)
			{
				if($rexpl != '' || $rexpl != null)
				{
					$txt .= $rexpl.', ';
				}
			}
		}
		else
		{
			$txt = $dice_txt;
		}

		return rtrim($txt, ', ');

	}

	public function cache_detail($header_id, $strtime_start, $strtime_finish, $shift)
	{
		$date_start = date('Y-m-d', $strtime_start);
		$date_finish = date('Y-m-d', $strtime_finish);

		$this->session->set_userdata('date_start', $date_start);
		$this->session->set_userdata('date_finish', $date_finish);
		$this->session->set_userdata('shift', $shift);

		/*$data_update = array(
			'date_start'  => $date_start,
			'date_finish' => $date_finish,
		);

		$update_header = $this->header_model->update($header_id, $data_update);
		*/
		redirect('admin/transaction/detail/'.$header_id);
	}

	/**
	 * Save SPK
	 */
	public function save()
	{
		$master_detail_id = $this->input->post('master_detail_id');
		$finishing = $this->input->post('finishing');
		$len = $this->input->post('len');
		$ppic = $this->input->post('ppic');
		$section_id = $this->input->post('section_id');
		$shift = $this->input->post('shift');
		$tanggal = $this->input->post('tanggal');
		$target_prod = $this->input->post('target_prod');
		$dies = $this->input->post('dies');
		$header_id = $this->input->post('header_id');

		$data_save = array(
			'shift'       => $shift,
			'tanggal'     => change_format_date($tanggal),
			'section_id'  => $section_id,
			'len'         => $len,
			'finishing'   => $finishing,
			'ppic_note'   => $ppic,
			'header_id'   => $header_id,
			'index_dice'  => $this->set_idxdice($dies),
			'target_prod' => $target_prod

		);

		if($master_detail_id == '0')
		{
			$save = $this->section_model->save_detail($data_save);
		}
		else
		{
			$save = $this->section_model->update($master_detail_id, $data_save);
		}

		if($save)
		{
	
			$response = array(
				'status'  => 'success',
				'message' => 'Berhasil menyimpan data'
			);
		}
		else
		{
			$response = array(
				'status'  => 'error',
				'message' => 'Gagal menyimpan data'
			);
		}

		output_json($response);
	}

	/**
	 * Save Scrap
	 */
	public function save_scrap()
	{
		$scrap = $this->input->post('scrap');
		$tanggal = $this->input->post('tanggal');
		$shift = $this->input->post('shift');
		$gram = $this->input->post('gram');
		$endbutt = $this->input->post('endbutt');
		$header_id = $this->input->post('header');
		$opr1 = $this->input->post('op1');
		$opr2 = $this->input->post('op2');

		$data_save = array(
			'Scrap'      => $scrap,
			'EndButt'    => $endbutt,
			'Shift'      => $shift,
			'Gram'       => $gram,
			'Tanggal'    => date('Y-m-d', strtotime($tanggal)),
			'SpkHeaderId'=> $header_id,
			'Opr1'       => $opr1,
			'Opr2'       => $opr2,
		);

		$check_data = $this->scrap_model->get_data_tgl_header($header_id, $tanggal, $shift);
		if($check_data)
		{
			$save = $this->scrap_model->update($check_data->LotScrapId, $data_save);
		}
		else
		{
			$save = $this->scrap_model->save($data_save);
		}

		if($save)
		{
			$response = array(
				'message' => 'Berhasil menyimpan scrap , End butt, dan Gram',
				'status'  => 'success'
			);
		}
		else
		{
			$response = array(
				'message' => 'Gagal menyimpan scrap dan End butt',
				'status'  => 'danger'
			);
		}

		$this->output->set_output(json_encode($response));

	}

	private function set_idxdice($array)
	{
		$str = '';
		foreach($array as $row)
		{
			$str .= $row.', ';
		}

		return rtrim($str, ', ');
	}

	/**
	 * get data scrap, endbut, gram berdasarkan spk header, shift dan tanggal
	 */
	public function get_data_scrap()
	{
		// post
		$header_id = $this->input->post('header_id');
		$tanggal = $this->input->post('tanggal');
		
		// data
		$response = array();
		$data = $this->scrap_model->get_data_advance('', $header_id, $tanggal)->result();

		// jika data tersedia
		// maka looping data tsb
		if($data)
		{
			foreach($data as $row)
			{
				// set response baru
				$response[] = array(
					'shift'   => $row->Shift,
					'scrap'   => $row->Scrap,
					'endbutt' => $row->EndButt,
					'gram'    => $row->Gram,
					'op1'     => $row->Opr1,
					'op2'     => $row->Opr2
				);
			}
		}

		output_json($response);
	}

	/**
	 * Menghapus data header
	 */
	public function delete_header()
	{
		$response = array(
			'message' => 'Data gagal dihapus',
			'status'  => 'error'
		);
		$header_id = $this->input->post('id');

		// delete header
		$delete_header = $this->detail_model->delete_header($header_id);

		// jika sukses menghapus
		// maka menghapus detailnya juga
		// dan memberi response utk notifikasi
		if($delete_header)
		{
			// delete detail
			$this->detail_model->delete_detail_by_header($header_id);
			$response = array(
				'message' => 'Data berhasil dihapus',
				'status'  => 'success'
			);
		}

		output_json($response);

	}

}
?>